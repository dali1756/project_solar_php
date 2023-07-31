<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include('db.php');
    define("ROOT_PATH", __DIR__);
?>
<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>太陽能電力管理系統</title>
    <script src="assets/vendor/jquery/jquery.js"></script>
    <script src="assets/vendor/jquery/jquery-3.5.1.js"></script>
    <!-- 套件CSS -->
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
    <!-- 自訂CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- 機器人 -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LeR90omAAAAAFidJMvnIdTzzza94nWFTLa9E5GE" async defer></script>
</head>

