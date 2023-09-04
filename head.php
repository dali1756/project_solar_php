<?php
    session_start();
    if (!isset($_SESSION["login_user"])) {
        header("location: login.php");
        exit;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once('db.php');
    define("ROOT_PATH", __DIR__);

    function get_area_head($db, $id) {
        $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["name"] : "";
    }
    $area = [];
    for ($i = 1; $i <= 3; $i++) {
        $area[$i] = get_area_head($db, $i);
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>唐榮鐵工廠股份有限公司</title>
    <script src="assets/vendor/jquery/jquery.js"></script>
    <script src="assets/vendor/jquery/jquery-3.5.1.js"></script>
    <script src="assets/js/script.js"></script>
    <?php // 套件CSS ?>
    <link href="assets/vendor/fontawesome/css/fontawesome.min.css" rel="stylesheet">
    <link href="assets/vendor/fontawesome/css/solid.min.css" rel="stylesheet">
    <link href="assets/vendor/fontawesome/css/brands.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/sweetalert/sweetalert.min.css" rel="stylesheet">
    <link href="assets/vendor/flagiconcss/css/flag-icon.min.css" rel="stylesheet">
    <link href="assets/vendor/datatables/datatables.min.css" rel="stylesheet">
    <link href="assets/vendor/airdatepicker/css/datepicker.min.css" rel="stylesheet">
    <link href="assets/vendor/mdtimepicker/mdtimepicker.min.css" rel="stylesheet">    
    <link href="assets/css/master.css" rel="stylesheet">
    <?php // 自訂CSS ?>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" href="assets/img/favicon.svg">
</head>

<body>
    <div class="wrapper">        
        <nav id="sidebar" class="active">           
            <ul class="list-unstyled components text-secondary">
                <li>
                    <a href="index.php"><i class="fas fa-home"></i>首頁</a>
                </li>
                <li>
                    <a href="index-view-all.php"><i class="fas fa-layer-group"></i>監控總覽</a>
                </li>
               <?php
                $area_url = [
                    1 => "converter-a.php",
                    2 => "converter-b.php",
                    3 => "converter-c.php"
                ];
               ?>
                <li>
                    <a href="#menu2" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle no-caret-down"><i class="fas fa-layer-group"></i>變流器</a>
                    <ul class="collapse list-unstyled" id="menu2">
                        <?php
                            for ($i = 1; $i <= count($area_url); $i++) {
                                echo "<li>";
                                echo '<a href = "'. $area_url[$i]. '"><i class = "fas fa-angle-right"></i>'. $area[$i]. '</a>';
                                echo "</li>";
                            }
                        ?>                          
                    </ul>
                </li>   
                
                
                <li>
                    <a href="meter.php"><i class="fas fa-layer-group"></i>直/交流電錶</a>
                </li>
                 <li>
                    <a href="history-total-d.php"><i class="fas fa-layer-group"></i>統計報表</a>
                </li>                    
                
                <li>
                    <a href="event.php"><i class="fas fa-layer-group"></i>故障查詢</a>
                </li>
                <li>
                    <a href="map.php"><i class="fas fa-layer-group"></i>廠區圖面</a>
                </li>                               
            </ul>            
        </nav>        
        <div id="body" class="active">
            <?php // navbar start ?>
            <nav class="navbar navbar-expand-lg navbar-white bg-white">
                
                <button type="button" id="sidebarCollapse" class="btn sidebar-btn">
                    <i class="fas fa-bars"></i><span></span>
                </button>
                
                  
                    <div class="main-logo"> <a href="index.php"><img src="assets/img/logo.svg"></a></div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="nav navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <div class="nav-dropdown"> 
                                <a href="#" id="nav2" class="nav-item nav-link dropdown-toggle text-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (isset($_SESSION["login_user"])): ?>
                                        <a href = "logout.php"><i class="fas fa-user"></i> <span><?php echo "登出"; ?></span></a>
                                    <!-- <i class="fas fa-user"></i> <span><?php echo isset($_SESSION["login_user"]) ? $_SESSION["login_user"] : "登入";?></span> <i style="font-size: .8em;" class="fas fa-caret-down"></i> -->
                                    <?php else: ?>
                                        <i class="fas fa-user"></i> <span>登入</span> <i style="font-size: .8em;" class="fas fa-caret-down"></i>    
                                    <?php endif; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end nav-link-menu">
                                    <ul class="nav-list">
                                       
                                        <div class="dropdown-divider"></div>
                                        <?php
                                            if (isset($_SESSION["login_user"])) {
                                                echo '<a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> 登出</a>';
                                            } else {
                                                echo '<a href="login.php" class="dropdown-item"><i class="fas fa-sign-in-alt"></i> 登入</a>';
                                            }
                                        ?>                                        
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>                      
                </div> 
                
                <!-- <a href="#"><div class="btn btn-primary" ><?php echo isset($_SESSION["login_user"]) ? $_SESSION["login_user"] : "使用者登入";?></div></a> -->
            </nav>
