<?php   
        include('head.php'); 

        $login_user = '';
        if (isset($_SESSION["login_user"])) {
                $login_user = $_SESSION["login_user"]; 
        }

?>
<div class="content-home">

        <div id="main-bg">
                <h1 class="index-title">
                <!-- 太陽能發電智慧電力系統 -->
                </h1>
                <?php if ($login_user): ?>
                <?php endif; ?>
        </div>
</div>
<?php include('footer.php'); ?>