<?php 
    header("Refresh: 300");
    include('head.php');
    function area($db) {
        $sql_area = "SELECT id FROM area";  
        $stmt = $db->prepare($sql_area);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    // 區域名稱, 碳排係數, 裝置容量
    function getData($db, $area_id) {
        $sql_data = "SELECT a.id, a.name, a.coe, a.capacity FROM area a WHERE id = :area_id";
        $stmt = $db->prepare($sql_data);
        $stmt->bindParam(":area_id", $area_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    // 裝置容量加總
    function getTotalCapacity($db) {
        $sql_total_cap = "SELECT SUM(a.capacity) AS total_capacity FROM area a";
        $stmt = $db->prepare($sql_total_cap);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result["total_capacity"];
    }
    // 區域 即時發電量,今日發電量,今日發電小時   
    function power($db, $area_id) {
        // $sql_power = "SELECT SUM(i.totally_active_power) AS totally_active_power, SUM(i.Energy_today) AS Energy_today, SUM(i.Energy_total) AS Energy_total,
        //               i.update_date, MIN(i.update_date) FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
        //               AND s.area_id = :area_id GROUP BY s.area_id";
        $sql_power = "SELECT SUM(i.totally_active_power) AS totally_active_power, SUM(i.Energy_today) AS Energy_today, SUM(i.Energy_total) AS Energy_total,
                      MAX(i.update_date), MIN(i.update_date) FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
                      AND s.area_id = :area_id GROUP BY s.area_id";
        $stmt = $db->prepare($sql_power);
        $stmt->bindParam(':area_id', $area_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $capacity = getData($db, $area_id)['capacity'];
        $result['daily_average_power'] = $result['Energy_today'] / $capacity;
        return $result;
    }   
    // 區域 昨日發電量,昨日發電小時
    function yesterday($db, $area_id, $date_yesterday) {
        // $sql_yesterday = "SELECT SUM(il.Energy_today) AS energy_today, il.add_date, il.invertor_id, s.area_id, s.sensor_type, s.sensor_type_id
        //                   FROM invertor_log il JOIN sensor s ON il.invertor_id = s.sensor_type_id
        //                   WHERE s.sensor_type = 'invertor' AND s.area_id = :area_id AND DATE(il.add_date) = :date_yesterday ORDER BY il.add_date DESC";
        $sql_yesterday = "SELECT SUM(il.Energy_today) AS energy_today, MAX(il.add_date) AS last_add_date, 
                          MAX(il.invertor_id) AS invertor_id, MAX(s.area_id) AS area_id, MAX(s.sensor_type) AS sensor_type, 
                          MAX(s.sensor_type_id) AS sensor_type_id 
                          FROM invertor_log il JOIN sensor s ON il.invertor_id = s.sensor_type_id 
                          WHERE s.sensor_type = 'invertor' AND s.area_id = :area_id AND DATE(il.add_date) = :date_yesterday 
                          ORDER BY last_add_date DESC";
        $stmt = $db->prepare($sql_yesterday);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->bindParam(":date_yesterday", $date_yesterday, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $capacity = getData($db, $area_id)['capacity'];
        $result["daily_average_power"] = $result["energy_today"] / $capacity; 
        return $result;
        if ($result) {
            $capacity = getData($db, $area_id)['capacity'];
            $result["daily_average_power"] = $result["energy_today"] / $capacity; 
            return $result;
        } else {
            return null;
        }
    }
    // 日照計
    function get_pyranometer($db, $area_id) {
        $sql_pyranometer = "SELECT p.id, p.solar_irradiance, p.update_date, s.id, s.name, s.sensor_type FROM pyranometer p JOIN sensor s ON p.id = s.sensor_type_id
                            WHERE s.sensor_type = 'pyranometer' AND s.area_id = :area_id ORDER BY p.update_date ASC";
        $stmt = $db->prepare($sql_pyranometer);
        $stmt->bindParam(":area_id", $area_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    // 溫度計
    function get_thermometer($db, $area_id) {
        $sql_thermometer = "SELECT t.id, t.temperature, s.id, s.name, s.sensor_type FROM thermometer t JOIN sensor s ON t.id = s.sensor_type_id
                            WHERE s.sensor_type = 'thermometer' AND s.area_id = :area_id";
        $stmt = $db->prepare($sql_thermometer);
        $stmt->bindParam(":area_id", $area_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    // NG顯示變流器名稱
    function error($db, $area_id){
        $sql_error = "SELECT i.id, s.name, i.Operation_mode, i.Error_message_1, i.Error_message_2, i.Warning_code, i.Load_de_rating_message, i.data_validity,
                      s.sensor_type, s.sensor_type_id FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' AND s.area_id = :area_id";
        $stmt = $db->prepare($sql_error);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $errorInvertor = [];
        foreach ($result as $row) {
            // if ($row["Operation_mode"] != 0 || $row["Error_message_1"] != 0 || $row["Error_message_2"] != 0 || $row["Warning_code"] != 0 || $row["Load_de_rating_message"] != 0) {
            //     $errorInvertor[] = $row["name"];
            // }
            $Operation_mode = strrev(decbin($row["Operation_mode"]));  // 判斷Operation_mode只有0x05代碼才顯示變流器NG其餘Operation_mode的代碼不顯示NG
            if (isset($Operation_mode[4]) && $Operation_mode[4] == '1') {
                $errorInvertor[] = $row["name"];
            } else if ($row["Error_message_1"] != 0 || $row["Error_message_2"] != 0 || $row["Warning_code"] != 0 || $row["Load_de_rating_message"] != 0) {
                $errorInvertor[] = $row["name"];
            }
        }
        return $errorInvertor;
    }
    // PR
    function get_pr($db, $area_id) {
        $power_result = power($db, $area_id);
        $volume = getData($db, $area_id);
        $cumulative_solar = cumulative($db, $area_id);
        $power_today = ($power_result["Energy_today"] != 0 && $volume["capacity"] != 0) ? $power_result["Energy_today"] / $volume["capacity"] : 0;   // 日平均
        // $power_today = $power_result["Energy_today"] / $volume["capacity"]; 
        $pr = $cumulative_solar != 0 ? ($power_today / $cumulative_solar) * 100 : 0;   // PR

        // var_dump($volume["capacity"]);   // O
        // var_dump($power_result["Energy_today"]);
        // var_dump($pr);
        return $pr;
    }
    // 總計PR
    function total_pr($db, $area_ids) {
        $total_capacity = 0;
        $weighted_pr = 0;
        $area_ids = is_array($area_ids) ? $area_ids : array($area_ids);
    
        foreach ($area_ids as $area_id) {
            $volume = getData($db, $area_id);
            $total_capacity += $volume["capacity"];
        }

        foreach ($area_ids as $area_id) {
            $pr = get_pr($db, $area_id);
            $volume = getData($db, $area_id);
            $weighted_pr += ($volume["capacity"] / $total_capacity) * $pr;
        }
        return $weighted_pr;
    }
    // 累積日照
    function cumulative($db, $area_id) {
        $today = date("Y-m-d");
        // $sql_cumulative = "SELECT pl.id, pl.pyranometer_id, pl.solar_irradiance, pl.data_validity, pl.add_date, s.sensor_type, s.sensor_type_id FROM pyranometer_log pl
        //                    JOIN sensor s ON pl.pyranometer_id = s.sensor_type_id WHERE s.sensor_type = 'pyranometer' AND s.area_id = :area_id
        //                    AND DATE (pl.add_date) = :today AND pl.data_validity = 1 GROUP BY pl.add_date ORDER BY pl.add_date ASC";
        $sql_cumulative = "SELECT pl.id, pl.pyranometer_id, pl.solar_irradiance, pl.data_validity, pl.add_date, s.sensor_type, s.sensor_type_id 
                           FROM pyranometer_log pl JOIN sensor s ON pl.pyranometer_id = s.sensor_type_id 
                           WHERE s.sensor_type = 'pyranometer' AND s.area_id = :area_id AND DATE (pl.add_date) = :today AND pl.data_validity = 1 
                           GROUP BY pl.id, pl.pyranometer_id, pl.solar_irradiance, pl.data_validity, pl.add_date, s.sensor_type, s.sensor_type_id 
                           ORDER BY pl.add_date ASC";
        $stmt = $db->prepare($sql_cumulative);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->bindParam(":today", $today);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cumulative_solar = 0;
        $before_solar = null;
        $before_time = null;
        // var_dump(count($result));
        foreach ($result as $row) {
            $solar_irradiance = $row["solar_irradiance"];
            if ($before_solar !== null && $before_time !== null) {
                $time_diff = strtotime($row["add_date"]) - strtotime($before_time);
                $solar_change = (($solar_irradiance - 4) * 125) + (($before_solar - 4) * 125);
                $cumulative_solar += ($solar_change * $time_diff) / 2 / 3600 / 1000;   // 累積日照 
                // var_dump($solar_change);        
                // var_dump($time_diff);             
            }
            $before_solar = $solar_irradiance;
            $before_time = $row["add_date"];
            // var_dump($cumulative_solar);   
            // 日照強度 = (Iout - 4) * 125 > (45536 - 4) * 125            data_validity = 1才撈計算
        }
        return $cumulative_solar;
    }
?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>監控總覽</h3>
        </div>
        <?php // 總計~Area2 切換按鈕 ?>
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <?php
                $area_maintain = getData($db, 1);
                $area_store = getData($db, 2);
                $area_rolled = getData($db, 3);
            ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">廠區發電總計</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area1a-tab" data-bs-toggle="pill" data-bs-target="#pills-Area1a" type="button" role="tab" aria-controls="pills-Area1a" aria-selected="false"><?php echo $area_maintain["name"]; ?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area1b-tab" data-bs-toggle="pill" data-bs-target="#pills-Area1b" type="button" role="tab" aria-controls="pills-Area1b" aria-selected="false"><?php echo $area_store["name"]; ?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area2-tab" data-bs-toggle="pill" data-bs-target="#pills-Area2" type="button" role="tab" aria-controls="pills-Area2" aria-selected="false"><?php echo $area_rolled["name"]; ?></button>
            </li>
        </ul>

       
        <?php // 總計~Area2 切換內容 ?>
        <div class="tab-content" id="pills-tabContent">
            <?php // 總計內容 ?>
            <div class="row tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="row justify-content-center">
                            <img src="./assets/img/area0.jpg" class="d-block col col-xl-6" alt="">
                        </div>                   
                        <?php 
                            $date_yesterday = date("Y-m-d", strtotime("-1 day"));
                            $total_capacity = getTotalCapacity($db);
                            $area_ids = area($db);
                            $totally_active_power = 0;
                            $energy_today = 0;
                            $pr = 0;
                            $coe = 0;
                            $yesterday_power = 0;
                            $yesterday_power_hour = 0;

                            foreach ($area_ids as $area_id) {
                                $power = power($db, $area_id);
                                $data = getData($db, $area_id);
                                $yesterday = yesterday($db, $area_id, $date_yesterday);

                                $totally_active_power = $totally_active_power + $power["totally_active_power"];
                                $energy_today = $energy_today + $power["Energy_today"];
                                $coe = $coe + $power["Energy_today"] * $data["coe"];
                                $yesterday_power = $yesterday_power + $yesterday["energy_today"];
                                if ($data["capacity"] != 0) {
                                    $yesterday_power_hour = $yesterday_power_hour + $yesterday["energy_today"] / $data["capacity"];
                                }
                            }
                            $yesterday_power_hour = $yesterday_power / $total_capacity;
                            $total_pr = 0;
                            foreach ($area_ids as $area_id) {
                                $total_pr = total_pr($db, $area_ids);
                                // var_dump($total_pr);
                            }
                        ?>
                        <div class="row">
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo isset($total_capacity) ? number_format($total_capacity, 2) : ""; ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo isset($totally_active_power) ? number_format($totally_active_power * 0.1 / 1000, 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo isset($energy_today) ? number_format($energy_today, 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo isset($energy_today) && isset($total_capacity) ? number_format($energy_today / $total_capacity, 2) : ""; ?> kWh/kWp</div></div>                           
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power) ? number_format($yesterday_power, 2) : ""; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yesterday_power_hour) ? number_format($yesterday_power_hour, 2) : ""; ?>kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo isset($total_pr) ? number_format($total_pr, 2) : ""; ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo isset($coe) ? number_format($coe, 2) : ""; ?> kg</div></div>
                        </div>
                                            
            </div>
            <?php // Area1a 1.維護廠房內容 ?>
            <div class="row tab-pane fade" id="pills-Area1a" role="tabpanel" aria-labelledby="pills-Area1a-tab">
            <div class="row justify-content-center">
                            <img src="./assets/img/area1.jpg" class="d-block col col-xl-6" alt="">
                        </div>      
                    <div class="map-title">
                        <h3 class="bg-info rounded-circle">1</h3>
                        <span class="fs-3"><?php echo $area_maintain["name"]; // 維護廠房 ?></span>
                    </div>
                    <?php 
                        $date_yesterday = date('Y-m-d', strtotime('-1 day'));
                        $area_maintain = getData($db, 1);
                        $power_maintain = power($db, 1); 
                        $pyranometer = get_pyranometer($db, 1);
                        $thermometer = get_thermometer($db, 1);
                        $yesterday_power = yesterday($db, 1, $date_yesterday);
                        $pr = get_pr($db, 1);
                        $yest_powerh = $yesterday_power["energy_today"] / $area_maintain["capacity"];   // 昨日發電小時
                        $solar_balance = $power_maintain["Energy_today"] / $area_maintain["capacity"];   // 日平均發電量
                        // var_dump($pr);
                        // var_dump($date_yesterday);
                    ?>
                    <div class="row">
                    <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo isset($area_maintain["capacity"]) ? number_format($area_maintain["capacity"], 2) : ""; ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo isset($power_maintain["totally_active_power"]) ? number_format($power_maintain["totally_active_power"] * 0.1 / 1000, 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo isset($power_maintain["Energy_today"]) ? number_format($power_maintain["Energy_today"], 2) : ""; ?> kWh</div></div> 
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo isset($power_maintain["Energy_today"]) && isset($area_maintain["capacity"]) ? number_format($power_maintain["Energy_today"] / $area_maintain["capacity"], 2) : ""; ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 2) : ""; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 2) : ""; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo isset($pr) ? number_format($pr, 2) : ""; ?>%</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo isset($power_maintain["Energy_today"]) && isset($area_maintain["coe"]) ? number_format($power_maintain["Energy_today"] * $area_maintain["coe"], 2) : ""; ?> 公斤</div></div>
                        <?php // 溫度計 ?>    
                        <?php foreach ($thermometer as $temp_info): ?>
                            <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                            <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo isset($temp) ? number_format($temp, 2) : ""; ?> &#176;C</div></div>
                        <?php endforeach; ?>
                        <?php // 日照計 ?>                        
                        <?php foreach ($pyranometer as $solar_irradiance_info): ?>
                            <?php $solar_irradiance = ($solar_irradiance_info["solar_irradiance"] - 4) * 125 == 0 ? 0 : ($solar_irradiance_info["solar_irradiance"] - 4) * 125; ?>
                        <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo isset($solar_irradiance) ? number_format($solar_irradiance, 2) : ""; ?> W/㎡</div></div>
                        <?php endforeach; ?>
                        <?php
                            // 有NG顯示,無NG隱藏欄位
                            $errorMaintain = error($db, 1);
                            if (!empty($errorMaintain)) {
                        ?>
                        <div class="monitor5"><i class=" fas fa-exclamation-triangle"></i><br> NG<div class="monitor-footer">
                            <?php
                                // 有NG顯示,無NG隱藏欄位
                                foreach($errorMaintain as $maintain) {
                                    echo "$maintain</br>";
                                }
                            ?>
                        </div></div>  
                        <?php } ?>
                    </div>                    
            </div>
            <?php // Area1b 2.備品儲區內容 ?>
            <div class="row tab-pane fade" id="pills-Area1b" role="tabpanel" aria-labelledby="pills-Area1b-tab">
            <div class="row justify-content-center">
                            <img src="./assets/img/area2.jpg" class="d-block col col-xl-6" alt="">
                        </div>      

                <div class="map-title">
                    <h3 class="bg-info rounded-circle">2</h3>
                    <span class="fs-3"><?php echo $area_store["name"]; // 設備處備品儲區 ?></span>
                </div>
                <?php
                    $date_yesterday = date("Y-m-d", strtotime("-1 day"));
                    $area_store = getData($db, 2); 
                    $power_store = power($db, 2); 
                    $pyranometer = get_pyranometer($db, 2);
                    $thermometer = get_thermometer($db, 2);
                    $yesterday_power = yesterday($db, 2, $date_yesterday);
                    $pr = get_pr($db, 2);
                    $yest_powerh = $yesterday_power["energy_today"] / $area_store["capacity"];
                ?>
                <div class="row">
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo isset($area_store["capacity"]) ? number_format($area_store["capacity"], 2) : ""; ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo isset($power_store["totally_active_power"]) ? number_format($power_store["totally_active_power"] * 0.1 / 1000, 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo isset($power_store["Energy_today"]) ? number_format($power_store["Energy_today"], 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo isset($power_store["Energy_today"]) && isset($area_store["capacity"]) ? number_format($power_store["Energy_today"] / $area_store["capacity"], 2) : ""; ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 2) : ""; ?> kWh</div></div>  
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 2) : ""; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo isset($pr) ? number_format($pr, 2) : ""; ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo isset($power_store["Energy_today"]) && isset($area_store["coe"]) ? number_format($power_store["Energy_today"] * $area_store["coe"], 2) : ""; ?> kg</div></div>
                    <?php // 溫度計 ?>                
                    <?php foreach ($thermometer as $temp_info): ?>   
                        <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                    <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo isset($temp) ? number_format($temp, 2) : ""; ?> &#176;C</div></div>                    
                    <?php endforeach; ?>
                    <?php // 日照計 ?>               
                    <?php foreach ($pyranometer as $solar_irradiance_info): ?> 
                        <?php $solar_irradiance = ($solar_irradiance_info["solar_irradiance"] - 4) * 125 == 0 ? 0 : ($solar_irradiance_info["solar_irradiance"] - 4) * 125; ?>   
                    <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo isset($solar_irradiance) ? number_format($solar_irradiance, 2) : ""; ?> W/㎡</div></div>
                    <?php endforeach; ?>
                    <?php
                        // 有NG顯示,無NG隱藏欄位
                        $errorStore = error($db, 2);
                        if (!empty($errorStore)) {
                    ?>
                    <div class="monitor5"><i class=" fas fa-exclamation-triangle"></i><br> NG<div class="monitor-footer">
                        <?php
                            // 有NG顯示,無NG隱藏欄位
                            foreach ($errorStore as $store) {
                                echo "$store</br>";
                            }
                        ?>
                    </div></div>
                    <?php } ?>
                </div>
            </div>
            <?php // Area2 3.太陽能發電廠內容 ?>
            <div class="row tab-pane fade" id="pills-Area2" role="tabpanel" aria-labelledby="pills-Area2-tab">
            <div class="row justify-content-center">
                            <img src="./assets/img/area3.jpg" class="d-block col col-xl-6" alt="">
                        </div>      
                        <div class="map-title">
                            <h3 class="bg-info rounded-circle">3</h3>
                            <span class="fs-3"><?php echo $area_rolled["name"]; // 軋鋼西側廠房 ?></span>
                        </div>
                       <?php 
                        $date_yesterday = date("Y-m-d", strtotime("-1 day"));
                        $area_rolled = getData($db, 3);
                        $power_rolled = power($db, 3); 
                        $pyranometer = get_pyranometer($db, 3);
                        $thermometer = get_thermometer($db, 3);
                        $yesterday_power = yesterday($db, 3, $date_yesterday);
                        $pr = get_pr($db, 3);
                        $yest_powerh = $yesterday_power["energy_today"] / $area_rolled["capacity"];
                       ?>
                        <div class="row">
                        <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo isset($area_rolled["capacity"]) ? number_format($area_rolled["capacity"], 2) : ""; ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo isset($power_rolled["totally_active_power"]) ? number_format($power_rolled["totally_active_power"]*0.1 / 1000, 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo isset($power_rolled["Energy_today"]) ? number_format($power_rolled["Energy_today"], 2) : ""; ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo isset($power_rolled["Energy_today"]) && isset($area_rolled["capacity"]) ? number_format($power_rolled["Energy_today"] / $area_rolled["capacity"], 2) : ""; ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 2) : ""; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 2) : ""; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo isset($pr) ? number_format($pr, 2) : ""; ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo isset($power_rolled["Energy_today"]) && isset($area_rolled["coe"]) ? number_format($power_rolled["Energy_today"] * $area_rolled["coe"], 2) : ""; ?> kg</div></div>
                            <?php // 溫度計 ?>
                            <?php foreach ($thermometer as $temp_info): ?>
                                <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                            <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo isset($temp) ? number_format($temp, 2) : ""; ?> &#176;C</div></div>
                            <?php endforeach; ?>
                            <?php // 日照計 ?>
                            <?php foreach ($pyranometer as $solar_irradiance_info): ?>
                                <?php $solar_irradiance = ($solar_irradiance_info["solar_irradiance"] - 4) * 125 == 0 ? 0 : ($solar_irradiance_info["solar_irradiance"] - 4) * 125; ?>
                            <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo isset($solar_irradiance) ? number_format($solar_irradiance, 2) : ""; ?> W/㎡</div></div>
                            <?php endforeach; ?>
                            <?php
                                // 有NG顯示,無NG隱藏欄位
                                $errorRolled = error($db, 3);
                                if (!empty($errorRolled)) {
                            ?>
                            <div class="monitor5"><i class=" fas fa-exclamation-triangle"></i><br> NG<div class="monitor-footer">
                                <?php
                                    // 有NG顯示,無NG隱藏欄位
                                    foreach ($errorRolled as $rolled) {
                                        echo "$rolled</br>";
                                    }
                                ?>
                            </div></div> 
                            <?php } ?>
                        </div>                      
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
<script>
    $(document).ready(function(){
        let show_map_id = $('#pills-Area1a-tab,#pills-Area1b-tab,#pills-Area2-tab');
        let not_show_map_id = $('#pills-home-tab');
        let area_map = $('#area-map');

        not_show_map_id.on('shown.bs.tab', function (event) {
            area_map.addClass('d-none');
        });

        show_map_id.on('shown.bs.tab', function (event) {
            area_map.removeClass('d-none');
        });

    });
</script>   
