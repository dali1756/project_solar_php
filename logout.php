<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION["logon_user"])) {  // 檢查有無登入
        unset($_SESSION['login_user']);
    }
    session_destroy();                     // 銷毀以上設定資料,確保登出
    header("location: index.php");
    exit;
?>