<?php
    date_default_timezone_set("Asia/Taipei");   		//時區(亞洲/台北)

    // session_cache_expire(28800);						//session逾時設定; 

    // session_start();

    ob_start();								    		//可以解決header有先送出東西的問題
    // ob_end_clean();							            //先ob_start 再進行一次ob_end_clean

    header("Cache-Control:no-cache,must-revalidate");   //強迫更新
    header("P3P: CP=".$_SERVER["HTTP_HOST"]."");        //解決在frame中session不能使用的問題，可填ip或是domain
    header('Content-type: text/html; charset=utf-8');	//指定utf8編碼 
    header('Vary: Accept-Language');

    $db = db_conn();
    function db_conn() {
        $localhost = '127.0.0.1:3306';
        $DBname    = 'solar_energy';
        $user      = 'root';
        $password  = 'a12345';

        try {
            $db = new PDO("mysql:host={$localhost};dbname={$DBname}", $user, $password);
            $db->query("SET NAMES 'utf8'");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "連線失敗.". $e->getMessage();
        }
        return $db;
    }

?>