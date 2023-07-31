<?php 
    include('head-login.php');

    $sql_c = "SELECT * FROM captcha";
    $stmt = $db->prepare($sql_c);
    $stmt->execute();
    $result_captcha = $stmt->fetch(PDO::FETCH_ASSOC);

    function check($db, $result_captcha) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_account = $_POST["account"];
            $user_password = $_POST["password"];
            $turn_password = hash("sha256", $user_password);   // 轉亂碼

            $sql_a = "SELECT * FROM admin WHERE account = :user_account AND password = :user_password";
            $stmt = $db->prepare($sql_a);
            $stmt->bindParam(":user_account", $user_account);
            $stmt->bindParam(":user_password", $turn_password);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                $error = "帳號或密碼不正確.";
            } else {
                if ($result_captcha && $result_captcha["number"] == 0) {   // number = 0 關閉google機器人驗證只需驗證admin表的帳密是否正確
                    $_SESSION["login_user"] = $user_account;
                    header("location: index-view-all.php");
                    exit;
                } else if ($result_captcha && $result_captcha["number"] == 1) {   // number = 1 開啟google機器人驗證需驗證機器人金鑰和admin帳密是否正確
                    if (isset($_POST["g-recaptcha-response"])) {
                        $captcha_response = $_POST["g-recaptcha-response"];
                        $url = "https://www.google.com/recaptcha/api/siteverify";
                        $data = array(
                            "secret" => $result_captcha["password"],   // 驗證captcha.password 金鑰
                            "response" => $captcha_response);
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        $result = curl_exec($ch);
                        $response = json_decode($result, true);
                        curl_close($ch);

                        if ($response["success"]) {
                            $_SESSION["login_user"] = $user_account;
                            header("location: index-view-all.php");
                            exit;
                        } else {
                            $error = "驗證失敗,再試一次.";
                        }
                    } else {
                        $error = "驗證未完成.";
                    }
                } else {
                    $error = "帳號不存在.";
                }
            }
            return $error;
        }
    }
    $error = check($db, $result_captcha);
?>

<div class="login-bg" >
<div class="login-width" >
    <div class="card-body text-center">
        <div class="login-logo">
        <img src="assets/img/logo.svg">
        </div>

        <form action="login.php" method="POST" id="login_form">
            <div class="mb-3 login ">
                <label for="account" class=""><i class="fas fa-user"></i>帳號</label>
                <input type="text" name="account" class="form-control" placeholder="請輸入"  required>
            </div>
            <div class="mb-3 login">
                <label for="password" class=""><i class="fas fa-lock"></i>密碼</label>
                <input type="password" name="password" class="form-control" placeholder="請輸入"  required>
            </div>
            <?php
                if (isset($result_captcha) && is_array($result_captcha) && $result_captcha['number'] == 1) {
            ?>
            <div class="text-center">
                <div class="g-recaptcha" data-sitekey="6LeR90omAAAAAFidJMvnIdTzzza94nWFTLa9E5GE"></div>
                
            </div>
            <?php
                }
            ?>
            <button type="submit" class=' btn btn-primary action_btn' style = "margin-top: 100px;">登入系統</button>
            <?php // <div class="g-recaptcha" data-sitekey="6LeR90omAAAAAFidJMvnIdTzzza94nWFTLa9E5GE"></div> ?>
        </form>
        <?php
            if (isset($error)) {
                echo '<div class = "alert alert-danger">'. $error. '</div>';
            }
        ?>

        <?php // <p class=" btn btn-outline-secondary mb-2 forget"><a href="forget.php">忘記密碼? </p> ?>

    </div>
</div>
</div>

<?php include('footer.php'); ?>