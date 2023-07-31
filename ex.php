<!-- meter.php -->
<?php 
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    header("Refresh: 300");
    include("head.php");

    // 維護廠房 + 設備處備品儲區 - DC AC
    $sql_dc_area1 = "SELECT s.sensor_type_id, COUNT(*), SUM(dc.Voltage) AS Voltage, SUM(dc.Current) AS Current, SUM(dc.Energy) AS Energy, SUM(dc.Power) AS Power 
                     FROM sensor s JOIN dc_electricity_meter dc ON s.sensor_type_id = dc.id JOIN area ON s.area_id = area.id
                     WHERE s.sensor_type='dc_electricity_meter' AND s.sensor_type_id = 1 GROUP BY s.sensor_type_id, area.id";
    $result_dc_area1 = $db->query($sql_dc_area1);
    $data_dc_area1 = $result_dc_area1->fetchAll(PDO::FETCH_ASSOC);

    $sql_ac_area1 = "SELECT s.sensor_type_id, COUNT(*), SUM(ac.ULN_AVG) AS ULN_AVG, SUM(ac.ULL_AVG) AS ULL_AVG, SUM(ac.I_AVG) AS I_AVG, SUM(ac.PSUM) AS PSUM 
                     FROM sensor s JOIN ac_electricity_meter ac ON s.sensor_type_id = ac.id JOIN area ON s.area_id = area.id
                     WHERE s.sensor_type = 'ac_electricity_meter' AND s.sensor_type_id = 1 GROUP BY s.sensor_type_id, area.id";
    $result_ac_area1 = $db->query($sql_ac_area1);
    $data_ac_area1 = $result_ac_area1->fetchAll(PDO::FETCH_ASSOC);

    $data_dc_area1 = is_array($data_dc_area1) ? $data_dc_area1 : array();
    $data_ac_area1 = is_array($data_ac_area1) ? $data_ac_area1 : array();
    $data_dc_1_2 = $data_dc_area1;
    $data_ac_1_2 = $data_ac_area1;

    // 軋鋼西側廠房 - DC AC
    $sql_dc_area3 = "SELECT s.sensor_type_id, COUNT(*), SUM(dc.Voltage) AS Voltage, SUM(dc.Current) AS Current, SUM(dc.Energy) AS Energy, SUM(dc.Power) AS Power
                     FROM sensor s JOIN dc_electricity_meter dc ON s.sensor_type_id = dc.id JOIN area ON s.area_Id = area.id
                     WHERE s.sensor_type = 'dc_electricity_meter' AND s.sensor_type_id = 2 GROUP BY s.sensor_type_id, area.id";
    $result_dc_area3 = $db->query($sql_dc_area3);
    $data_dc_area3 = $result_dc_area3->fetchAll(PDO::FETCH_ASSOC);

    $sql_ac_area3 = "SELECT s.sensor_type_id, COUNT(*), SUM(ac.ULN_AVG) as ULN_AVG, SUM(ac.ULL_AVG) AS ULL_AVG, SUM(ac.I_AVG) AS I_AVG, SUM(ac.PSUM) AS PSUM
                     FROM sensor s JOIN ac_electricity_meter ac ON s.sensor_type_id = ac.id JOIN area ON s.area_id = area.id
                     WHERE s.sensor_type = 'ac_electricity_meter' AND s.sensor_type_id = 2 GROUP BY s.sensor_type_id, area.id";
    $result_ac_area3 = $db->query($sql_ac_area3);
    $data_ac_area3 = $result_ac_area3->fetchAll(PDO::FETCH_ASSOC);

    $data_dc_area3 = is_array($data_dc_area3) ? $data_dc_area3 : array();
    $data_ac_area3 = is_array($data_ac_area3) ? $data_ac_area3 : array();
    $data_dc_3 = $data_dc_area3;
    $data_ac_3 = $data_ac_area3;

    function get_area($db, $id) {   // 區域名稱
        $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["name"] : "";
    }
    $area_id_1 = 1;
    $area_id_2 = 2;
    $area_id_3 = 3;
    $area_name_1 = get_area($db, $area_id_1);
    $area_name_2 = get_area($db, $area_id_2);
    $area_name_3 = get_area($db, $area_id_3);
    $area_names = $area_name_1. " & ". $area_name_2;

?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>直/交流電錶 </h3>
        </div>

        <div class="row ">

            <div class="col-lg-6">
                <div class="meter-img-small"><img src="assets/img/meter3.svg"></div>
                <div class="meter-bg1">
                    電壓：<span class="">        
                        </span><?php echo isset($data_dc_1_2[0]) ? number_format($data_dc_1_2[0]["Voltage"], 2) : ""; ?> V<br>
                    電流：<span class = "">
                        </span><?php echo isset($data_dc_1_2[0]) ? number_format($data_dc_1_2[0]["Current"], 2) : ""; ?> A</div>
                <div class="meter-img"><img src="assets/img/meter2.svg"></div>
                <table class=" table table-bordered">
                    <tr class="box2">
                        <td colspan="4"><?php echo $area_names; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="title2b">電錶1-直流電DC</td>
                    </tr>
                    <tr>
                        <td class="title3">即時發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_dc_1_2[0]) ? number_format($data_dc_1_2[0]["Power"], 2) : ""; ?>
                        </td>
                        <td>kW</td>
                    </tr>
                    <tr>
                        <td class="title3">累積發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_dc_1_2[0]) ? number_format($data_dc_1_2[0]["Energy"], 2) : ""; ?>
                        </td>
                        <td>kWh</td>
                    </tr>

                    <tr class="">
                        <td colspan="4" class="title2b">電錶2-交流電AC</td>
                    </tr>
                    <tr>
                        <td class="title3"> 平均相電壓</td>
</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($data_ac_1_2[0]) ? number_format($data_ac_1_2[0]["ULN_AVG"]*0.1, 2) : ""; ?>
                        </td>
                        <td>V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均線電壓</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($data_ac_1_2[0]) ? number_format($data_ac_1_2[0]["ULL_AVG"]*0.1, 2) : ""; ?>                        
                        </td>
                        <td>V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均電流</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_ac_1_2[0]) ? number_format($data_ac_1_2[0]["I_AVG"]*0.001, 2) : ""; ?>                      
                        </td>
                        <td>A</td>
                    </tr>
                    <tr>
                        <td class="title3">總有效功率</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_ac_1_2[0]) ? number_format($data_ac_1_2[0]["PSUM"]/1000, 2) : ""; ?>                    
                        </td>
                        <td>kW</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-6 ">
                <div class="meter-img-small"><img src="assets/img/meter3.svg"></div>
                <div class="meter-bg1">
                    電壓：<span class="">
                    <?php echo isset($data_dc_3[0]) ? number_format($data_dc_3[0]["Voltage"], 2) : ""; ?>
                    </span>V<br>
                    電流：<span class="">
                    <?php echo isset($data_dc_3[0]) ? number_format($data_dc_3[0]["Current"], 2) : ""; ?>
                    </span>A</div>
                <div class="meter-img"><img src="assets/img/meter2.svg"></div>
                <table class="  table table-bordered">
                    <tr class="box2">
                        <td colspan="4"><?php echo $area_name_3; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="title2b">電錶1-直流電DC</td>
                    </tr>
                    <tr>
                        <td class="title3">即時發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_dc_3[0]) ? number_format($data_dc_3[0]["Power"], 2) : ""; ?>
                        </td>
                        <td>kW</td>
                    </tr>
                    <tr>
                        <td class="title3">累積發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_dc_3[0]) ? number_format($data_dc_3[0]["Energy"], 2) : ""; ?>
                        </td>
                        <td>kWh</td>
                    </tr>

                    <tr class="">
                        <td colspan="4" class="title2b">電錶2-交流電AC</td>
                    </tr>
                    <tr>
                        <td class="title3"> 平均相電壓</td>
</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($data_ac_3[0]) ? number_format($data_ac_3[0]["ULN_AVG"]*0.1, 2) : ""; ?>
                        </td>
                        <td>V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均線電壓</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($data_ac_3[0]) ? number_format($data_ac_3[0]["ULL_AVG"]*0.1, 2) : ""; ?>                        
                        </td>
                        <td>V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均電流</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_ac_3[0]) ? number_format($data_ac_3[0]["I_AVG"]*0.001, 2) : ""; ?>                      
                        </td>
                        <td>A</td>
                    </tr>
                    <tr>
                        <td class="title3">總有效功率</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($data_ac_3[0]) ? number_format($data_ac_3[0]["PSUM"]/1000, 2) : ""; ?>                     
                        </td>
                        <td>kW</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php include('footer.php'); ?>





<!-- index-view-all.php -->
<?php 
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
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
        $sql_power = "SELECT SUM(i.totally_active_power) AS totally_active_power, SUM(i.Energy_today) AS Energy_today, SUM(i.Energy_total) AS Energy_total,
                      i.update_date, MIN(i.update_date) FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
                      AND s.area_id = :area_id GROUP BY s.area_id";
        $stmt = $db->prepare($sql_power);
        $stmt->bindParam(':area_id', $area_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $day_count = date("t");   // 判斷月份天數28~31
    
        $capacity = getData($db, $area_id)['capacity'];
        if ($capacity > 0) {
            $result['daily_average_power'] = $result['Energy_today'] / $capacity;
        } else {
            $result['daily_average_power'] = 0;
        }
        
        return $result;
    }   
    // 區域 昨日發電量,昨日發電小時
    function yesterday($db, $area_id, $date_yesterday) {
        $sql_yesterday = "SELECT SUM(il.Energy_today) AS energy_today, il.add_date, il.invertor_id, s.area_id, s.sensor_type, s.sensor_type_id
                          FROM invertor_log il JOIN sensor s ON il.invertor_id = s.sensor_type_id
                          WHERE s.sensor_type = 'invertor' AND s.area_id = :area_id AND DATE(il.add_date) = :date_yesterday ORDER BY il.add_date DESC";
        $stmt = $db->prepare($sql_yesterday);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->bindParam(":date_yesterday", $date_yesterday, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $capacity = getData($db, $area_id)['capacity'];
        if ($capacity > 0) {
            $result['daily_average_power'] = $result['energy_today'] / $capacity;
        } else {
            $result['daily_average_power'] = 0;
        }
    
        return $result;
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
    // 模板溫度
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
            if ($row["Operation_mode"] != 0 || $row["Error_message_1"] != 0 || $row["Error_message_2"] != 0 || $row["Warning_code"] != 0 || $row["Load_de_rating_message"] != 0) {
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
    //     total_pr(2,$result["total_capacity"])
    // 累積日照
    function cumulative($db, $area_id) {
        $sql_cumulaative = "SELECT pl.id, pl.pyranometer_id, pl.solar_irradiance, pl.add_date, s.sensor_type, s.sensor_type_id FROM pyranometer_log pl
                            JOIN sensor s ON pl.pyranometer_id = s.sensor_type_id WHERE s.sensor_type = 'pyranometer' AND s.area_id = :area_id AND pl.add_date <= :time2 AND pl.add_date >= :time1 GROUP BY pl.add_date
                            ORDER BY pl.add_date ASC";
        $stmt = $db->prepare($sql_cumulaative);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->bindParam(":time2", '2023-06-27');
        $stmt->bindParam(":time1", '2023-06-26');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cumulative_solar = 0;
        $before_solar = null;
        $before_time = null;
        var_dump(count($result));

        foreach ($result as $row) {
            $solar_irradiance = $row["solar_irradiance"];
            if ($before_solar !== null && $before_time !== null) {
                // $time_diff = strtotime($before_time) - strtotime($row["add_date"]);
                $time_diff = strtotime($row["add_date"]) - strtotime($before_time);
                $solar_change = $solar_irradiance + $before_solar;
                $cumulative_solar += ($solar_change * $time_diff) / 2 / 3600 / 1000;   // 累積日照
                // var_dump($cumulative_solar);
                // var_dump($solar_change);
            }
            $before_solar = $solar_irradiance;
            $before_time = $row["add_date"];
            // var_dump($cumulative_solar); 
        }
        return $cumulative_solar;
    }
?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>監控總覽</h3>
        </div>
        <!-- 總計~Area2 切換按鈕 -->
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <?php
                $area_name_1 = getData($db, 1);
                $area_name_2 = getData($db, 2);
                $area_name_3 = getData($db, 3);
            ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">廠區發電總計</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area1a-tab" data-bs-toggle="pill" data-bs-target="#pills-Area1a" type="button" role="tab" aria-controls="pills-Area1a" aria-selected="false"><?php echo $area_name_1["name"]; ?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area1b-tab" data-bs-toggle="pill" data-bs-target="#pills-Area1b" type="button" role="tab" aria-controls="pills-Area1b" aria-selected="false"><?php echo $area_name_2["name"]; ?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area2-tab" data-bs-toggle="pill" data-bs-target="#pills-Area2" type="button" role="tab" aria-controls="pills-Area2" aria-selected="false"><?php echo $area_name_3["name"]; ?></button>
            </li>
        </ul>

        <!-- Area1a ~ Area2 共用地圖-->
        <div id="area-map" class="row justify-content-center d-none">
            <div class="d-block col col-xl-8 position-relative" >
                <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                <!-- <div class="map-button">
                    <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                    <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                    <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                    <div class="map-area1">
                        <p class="rounded-circle">A</p>
                        <p class="rounded-circle">B</p>
                        <p class="rounded-circle">C</p>
                        <p class="rounded-circle">D</p>
                        <p class="rounded-circle">E</p>
                        <p class="rounded-circle">F</p>
                    </div>
                    <div class="map-area2">
                        <p class="rounded-circle">H</p>
                        <p class="rounded-circle">I</p>
                    </div>
                    <ul class="map-area3">
                        <li><p class="rounded-circle">J</p></li>
                        <li><p class="rounded-circle">K</p></li>
                        <li><p class="rounded-circle">L</p></li>
                        <li><p class="rounded-circle">M</p></li>
                        <li><p class="rounded-circle">N</p></li>
                        <li><p class="rounded-circle">O</p></li>
                        <li><p class="rounded-circle">P</p></li>
                        <li><p class="rounded-circle">Q</p></li>
                    </ul>

                </div> -->
            </div>
        </div>
        <!-- 總計~Area2 切換內容 -->
        <div class="tab-content" id="pills-tabContent">
            <!-- 總計內容 -->
            <div class="row tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="row justify-content-center">
                            <img src="./assets/img/solar_place.jpg" class="d-block col col-xl-6" alt="">
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
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($total_capacity, 2); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($totally_active_power * 0.1 / 1000, 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($energy_today, 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($energy_today / $total_capacity, 2); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo number_format($yesterday_power, 2); ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo number_format($yesterday_power_hour, 2); ?>kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($total_pr, 4); ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($coe, 2); ?> kg</div></div>
                        </div>
                                            
            </div>
            <!-- Area1a 1.維護廠房內容 -->
            <div class="row tab-pane fade" id="pills-Area1a" role="tabpanel" aria-labelledby="pills-Area1a-tab">
                    <div class="row justify-content-center d-none">
                        <div class="d-block col col-xl-8 position-relative" >
                            <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                            <div class="map-button">
                                <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                                <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                                <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                            </div>
                        </div>
                    </div>
                    <div class="map-title">
                        <h3 class="bg-info rounded-circle">1</h3>
                        <span class="fs-3"><?php echo $area_name_1["name"]; ?></span>
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
                    ?>
                    <div class="row">
                    <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($area_maintain["capacity"], 2); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($power_maintain["totally_active_power"] * 0.1 / 1000, 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($power_maintain["Energy_today"], 2); ?> kWh</div></div> 
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($power_maintain["Energy_today"] / $area_maintain["capacity"], 2); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 2) : 0; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 2) : 0; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($pr, 4); ?>%</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($power_maintain["Energy_today"] * $area_maintain["coe"], 2); ?> 公斤</div></div>
                        <!-- 溫度計 -->    
                        <?php foreach ($thermometer as $temp_info): ?>
                            <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                            <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo number_format($temp, 2); ?> &#176;C</div></div>
                        <?php endforeach; ?>
                        <!-- 日照計 -->                        
                        <?php foreach ($pyranometer as $solar_irradiance_info): ?>
                            <?php $solar_irradiance = $solar_irradiance_info["solar_irradiance"] == 0 ? 0 : $solar_irradiance_info["solar_irradiance"]; ?>
                        <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo number_format($solar_irradiance, 2); ?> W/㎡</div></div>
                        <?php endforeach; ?>
                        <?php
                            // 有NG顯示,無NG隱藏欄位
                            $errorMaintain = error($db, 1);
                            if (!empty($errorMaintain)) {
                        ?>
                        <div class="monitor5"><i class=" fas fa-exclamation-triangle"></i><br> NG<div class="monitor-footer">
                            <?php
                                // $errorMaintain = error($db, 1);
                                // foreach ($errorMaintain as $maintain) {
                                //     echo "$maintain</br>";
                                // }

                                // 有NG顯示,無NG隱藏欄位
                                foreach($errorMaintain as $maintain) {
                                    echo "$maintain</br>";
                                }
                            ?>
                        </div></div>  
                        <?php } ?>
                    </div>                    
            </div>
            <!-- Area1b 2.備品儲區內容 -->
            <div class="row tab-pane fade" id="pills-Area1b" role="tabpanel" aria-labelledby="pills-Area1b-tab">
                <div class="row justify-content-center d-none">
                    <div class="d-block col col-xl-8 position-relative" >
                        <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                        <div class="map-button">
                            <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                            <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                            <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                        </div>
                    </div>
                </div>

                <div class="map-title">
                    <h3 class="bg-info rounded-circle">2</h3>
                    <span class="fs-3">設備處備品儲區</span>
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
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($area_store["capacity"], 2); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($power_store["totally_active_power"] * 0.1 / 1000, 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($power_store["Energy_today"], 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($power_store["Energy_today"] / $area_store["capacity"], 2); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 2) : 0; ?> kWh</div></div>  
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 2) : 0; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($pr, 2); ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($power_store["Energy_today"] * $area_store["coe"], 2); ?> kg</div></div>
                    <!-- 溫度計 -->                
                    <?php foreach ($thermometer as $temp_info): ?>   
                        <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                    <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo number_format($temp, 2); ?> &#176;C</div></div>                    
                    <?php endforeach; ?>
                    <!-- 日照計 -->               
                    <?php foreach ($pyranometer as $solar_irradiance_info): ?> 
                        <?php $solar_irradiance = $solar_irradiance_info["solar_irradiance"] == 0 ? : $solar_irradiance_info["solar_irradiance"]; ?>   
                    <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo number_format($solar_irradiance, 2); ?> W/㎡</div></div>
                    <?php endforeach; ?>
                    <?php
                        // 有NG顯示,無NG隱藏欄位
                        $errorStore = error($db, 2);
                        if (!empty($errorStore)) {
                    ?>
                    <div class="monitor5"><i class=" fas fa-exclamation-triangle"></i><br> NG<div class="monitor-footer">
                        <?php
                            // $errorStore = error($db, 2);
                            // foreach ($errorStore as $store) {
                            //     echo "$store</br>";
                            // }

                            // 有NG顯示,無NG隱藏欄位
                            foreach ($errorStore as $store) {
                                echo "$store</br>";
                            }
                        ?>
                    </div></div>
                    <?php } ?>
                </div>
            </div>
            <!-- Area2 3.太陽能發電廠內容 -->
            <div class="row tab-pane fade" id="pills-Area2" role="tabpanel" aria-labelledby="pills-Area2-tab">
                        <div class="row justify-content-center d-none">
                            <div class="d-block col col-xl-8 position-relative" >
                                <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                                <div class="map-button">
                                    <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                                    <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                                    <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                                </div>
                            </div>
                        </div>
                        <div class="map-title">
                            <h3 class="bg-info rounded-circle">3</h3>
                            <span class="fs-3">軋鋼西側廠房</span>
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
                        <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($area_rolled["capacity"], 2); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($power_rolled["totally_active_power"]*0.1 / 1000, 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($power_rolled["Energy_today"], 2); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($power_rolled["Energy_today"] / $area_rolled["capacity"], 2); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 2) : 0; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 2) : 0; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($pr, 2); ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($power_rolled["Energy_today"] * $area_rolled["coe"], 2); ?> kg</div></div>
                            <!-- 溫度計 -->
                            <?php foreach ($thermometer as $temp_info): ?>
                                <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                            <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php $temp_info["name"]; ?><div class="monitor-footer"><?php echo number_format($temp, 2); ?> &#176;C</div></div>
                            <?php endforeach; ?>
                            <!-- 日照計 -->
                            <?php foreach ($pyranometer as $solar_irradiance_info): ?>
                                <?php $solar_irradiance = $solar_irradiance_info["solar_irradiance"] == 0 ? 0 : $solar_irradiance_info["solar_irradiance"]; ?>
                            <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo number_format($solar_irradiance, 2); ?> W/㎡</div></div>
                            <?php endforeach; ?>
                            <?php
                                // 有NG顯示,無NG隱藏欄位
                                $errorRolled = error($db, 3);
                                if (!empty($errorRolled)) {
                            ?>
                            <div class="monitor5"><i class=" fas fa-exclamation-triangle"></i><br> NG<div class="monitor-footer">
                                <?php
                                    // $errorRolled = error($db, 3);
                                    // foreach ($errorRolled as $rolled) {
                                    //     echo "$rolled</br>";
                                    // }

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





<!-- converter-a.php -->
<?php
    header("Refresh: 300");
    include("head.php");

    function get_inverter($db, $id = null)
    {
        if ($id === null) {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id 
                    WHERE s.sensor_type = 'invertor' AND s.area_id = 1";
            $stmt = $db->prepare($sql);
        } else {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id 
                    WHERE s.sensor_type = 'invertor' AND s.area_id = 1 AND i.id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // area
    function get_area($db, $id)
    {
        $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["name"] : "";
    }

    $selected = isset($_GET["inverter"]) ? (int) $_GET["inverter"] : 0;
    $inverters = [];
    if ($selected > 0) {
        $inverters = get_inverter($db, $selected);
    } else {
        $inverters = get_inverter($db);
    }

    $area_id = 1;
    $area_name = get_area($db, $area_id);

    // 變流器編號 廠牌型號
    function get_maintain_device($db) {
        $sql_maintain_device = "SELECT s.hardware_device, s.name, s.sensor_type_id, i.id FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id 
                                WHERE s.sensor_type = 'invertor' AND s.area_id = 1";
        $stmt = $db->prepare($sql_maintain_device);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $maintain_device = get_maintain_device($db);

    function get_maintain($db) {
        $sql_maintain = "SELECT i.id, i.L1_phase_voltage, i.L1_phase_current, i.L1_power, i.L1_AC_frequency, i.L2_phase_voltage, i.L2_phase_current, i.L2_power,
                         i.L2_AC_frequency, i.L3_phase_voltage, i.L3_phase_current, i.L3_power, i.L3_AC_frequency, i.1st_input_voltage, i.1st_input_current, 
                         i.1st_input_power, i.2nd_input_voltage, i.2nd_input_current, i.2nd_input_power, i.3rd_input_voltage, i.3rd_input_current, i.3rd_input_power, 
                         i.4th_input_voltage, i.4th_input_current, i.4th_input_power, i.Energy_today, i.Energy_total, i.totally_active_power, 
                         i.internal_temperature, s.sensor_type_id, s.sensor_type FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id 
                         WHERE s.sensor_type = 'invertor' AND s.area_id = 1";
        $stmt_maintain = $db->prepare($sql_maintain);
        $stmt_maintain->execute();
        return $stmt_maintain->fetchAll(PDO::FETCH_ASSOC);
    }
    $invertor_maintain = get_maintain($db);

    function get_statuses($db, $area_id) {
        $sql_statuses = "SELECT i.id, i.Operation_mode, i.Error_message_1, i.Error_message_2, i.Warning_code, i.Load_de_rating_message, i.data_validity,
                         s.sensor_type_id, s.sensor_type FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor'
                         AND s.area_id = :area_id";
        $stmt = $db->prepare($sql_statuses);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>變流器-<?php echo $area_name; ?></h3>
        </div>
        <div class="col-lg-12">
        <div> <span class="ps">提醒:下列表格超出範圍時可橫向/直向移動</span><br>    
                <i class="fas fa-circle gray"></i> 斷線 <i class=" fas fa-circle green"></i> 正常 <i
                    class=" fas fa-circle orange"></i> 告警 <i class=" fas fa-circle red"></i> 錯誤 <i
                    class=" fas fa-circle pink"></i> 狀態異常
            </div>
            <div class="row">
                <div class="invertor">
                    <table>
                        <thead>
                            <tr>
                                <th>變流器編號</th>
                                <?php
                                    $statuses = get_statuses($db, 1);
                                    foreach ($maintain_device as $index => $row) {
                                        $two_note_error = $statuses[$index]["Error_message_1"] | $statuses[$index]["Error_message_2"];
                                        $notes = [
                                            strrev(decbin($statuses[$index]["Operation_mode"])),
                                            strrev(decbin($two_note_error)),
                                            strrev(decbin($statuses[$index]["Warning_code"])),
                                            strrev(decbin($statuses[$index]["Load_de_rating_message"])),
                                            $statuses[$index]["data_validity"] == 1 ? "" : "1"
                                        ];
                                        $colors = ["green", "red", "orange", "pink", "gray"];
                                        $error = [];
                                        foreach ($notes as $note_index => $note) {
                                            for ($i = 0; $i < strlen($note); $i++) {
                                                if ($note[$i] == "1") {
                                                    $error[] = "<i class='fas fa-circle " . $colors[$note_index] . "'></i>";
                                                    break;
                                                }
                                            }
                                        }
                                        $errors = implode(" ", $error);
                                        echo "<th>". $errors. $row["name"]. "</th>";
                                    }


                                    // $statuses = get_statuses($db, 1);
                                    // foreach ($maintain_device as $index => $row) {
                                    //     $two_note_error = $statuses[$index]["Error_message_1"] | $statuses[$index]["Error_message_2"];
                                    //     $notes = [
                                    //         strrev(decbin($statuses[$index]["Operation_mode"])),
                                    //         strrev(decbin($two_note_error)),
                                    //         strrev(decbin($statuses[$index]["Warning_code"])),
                                    //         strrev(decbin($statuses[$index]["Load_de_rating_message"])),
                                    //     ];
                                    //     $gray = $statuses[$index]["data_validity"];
                                    //     $colors = ["red", "orange", "pink", "gray"];
                                    //     $error = [];
                                    //     $noError = true;
                                    //     foreach ($notes as $note_index => $note) {
                                    //         for ($i = 0; $i < strlen($note); $i++) {
                                    //             if ($note[$i] == "1") {
                                    //                 if (isset($colors[$note_index])) {
                                    //                     $error[] = "<i class = 'fas fa-circle". $colors[$note_index]. "'></i>";
                                    //                     $noError = false;
                                    //                     break;
                                    //                 }
                                    //             }
                                    //         }
                                    //     }
                                    //     if ($gray != 1) {
                                    //         $error[] = "<i class = 'fas fa-circle gray'></i>";
                                    //         $noError = false;
                                    //     }
                                    //     if ($noError) {
                                    //         $error[] = "<i class = 'fas fa-circle green'></i>";
                                    //     }
                                    //     $errors = implode("", $error);
                                    //     echo "<th>". $errors. $row["name"]. "</th>";
                                    // }
                                ?>                                                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>廠牌型號 </td>
                                <?php
                                    foreach ($maintain_device as $row) {
                                        echo "<td>". $row["hardware_device"]. "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>今日發電量kWh </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["Energy_today"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>總發電量kWh </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["Energy_total"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>即時發電量kW </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["totally_active_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>轉換效益% </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        $dc_input_power = ($row["1st_input_power"] + $row["2nd_input_power"] + $row["3rd_input_power"] + $row["4th_input_power"]) * 0.1;
                                        $ac_output_power = ($row["L1_power"] + $row["L2_power"] + $row["L3_power"]) * 0.1;
                                        $conversion_efficiency = 0;
                                        if ($dc_input_power > 0) {
                                            $conversion_efficiency = number_format(($ac_output_power / $dc_input_power) / 1000, 4);
                                        }
                                        echo "<td>$conversion_efficiency</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>內部溫度&#176;C </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["internal_temperature"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串直流電壓V </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["1st_input_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串直流電流A </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["1st_input_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["1st_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串直流電壓V </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["2nd_input_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串直流電流A </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["2nd_input_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["2nd_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串直流電壓V </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["3rd_input_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串直流電流A </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["3rd_input_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["3rd_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串直流電壓V </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["4th_input_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串直流電流A </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["4th_input_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["4th_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流電壓V</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L1_phase_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流電流A</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L1_phase_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流功率kW</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L1_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L1_AC_frequency"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流電壓V</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L2_phase_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流電流A</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L2_phase_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流功率kW</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L2_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L2_AC_frequency"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流電壓V</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L3_phase_voltage"]*0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流電流A</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L3_phase_current"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流功率kW</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L3_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_maintain as $row) {
                                        echo "<td>". number_format($row["L3_AC_frequency"]*0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note1</td>
                                <?php
                                    $modes = [
                                        "0x00","0x01","0x02","0x03","0x05",
                                        "0x07","0x08"
                                    ];
                                    $statuses = get_statuses($db, 1);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note1 = strrev(decbin($status["Operation_mode"]));
                                        for ($i = 0; $i < strlen($note1); $i++) {
                                            if ($note1[$i] == "1") {
                                                echo $modes[$i];
                                                if ($i < strlen($note1) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note2</td>
                                <?php
                                    $modes_note2 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 1);
                                        foreach ($statuses as $status) {
                                            echo "<td>";
                                            $note2 = strrev(decbin($status["Error_message_1"]));
                                            for ($i = 0; $i < strlen($note2); $i++) {
                                                if ($note2[$i] == "1") {
                                                    echo $modes_note2[$i];
                                                    if ($i < strlen($note2) - 1) {
                                                        echo "<br>";
                                                    }
                                                }
                                            }
                                            echo "</td>";
                                        }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note3</td>
                                <?php
                                    $modes_note3 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 1);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note3 = strrev(decbin($status["Error_message_2"]));
                                        for ($i = 0; $i < strlen($note3); $i++) {
                                            if ($note3[$i] == "1") {
                                                echo $modes_note3[$i];
                                                if ($i < strlen($note3) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note4</td>
                                <?php
                                    $modes_note4 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 1);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note4 = strrev(decbin($status["Warning_code"]));
                                        for ($i = 0; $i < strlen($note4); $i++) {
                                            if ($note4[$i] == "1") {
                                                echo $modes_note4[$i];
                                                if ($i < strlen($note4) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note5</td>
                                <?php
                                    $modes_note5 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 1);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note5 = strrev(decbin($status["Load_de_rating_message"]));
                                        for ($i = 0; $i < strlen($note5); $i++) {
                                            if ($note5[$i] == "1") {
                                                echo $modes_note5[$i];
                                                if ($i < strlen($note5) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>





<!-- converter-b.php -->
<?php
    header("Refresh: 300");
    include("head.php");

    function get_inverter($db, $id = null)
    {
        if ($id === null) {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id 
                    WHERE s.sensor_type = 'invertor' AND s.area_id = 2";
            $stmt = $db->prepare($sql);
        } else {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id 
                    WHERE s.sensor_type = 'invertor' AND s.area_id = 2 AND i.id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // area
    function get_area($db, $id)
    {
        $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["name"] : "";
    }

    $selected = isset($_GET["inverter"]) ? (int) $_GET["inverter"] : 0;
    $inverters = [];
    if ($selected > 0) {
        $inverters = get_inverter($db, $selected);
    } else {
        $inverters = get_inverter($db);
    }

    $area_id = 2;
    $area_name = get_area($db, $area_id);

    // 變流器編號 廠牌型號
    function get_store_device($db) {
        $sql_store_device = "SELECT s.hardware_device, s.name, s.sensor_type_id, i.id FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id 
                              WHERE s.sensor_type = 'invertor' AND s.area_id = 2";
        $stmt = $db->prepare($sql_store_device);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $store_device = get_store_device($db);

    function get_store($db) {
        $sql_store = "SELECT i.id, i.L1_phase_voltage, i.L1_phase_current, i.L1_power, i.L1_AC_frequency, i.L2_phase_voltage, i.L2_phase_current, i.L2_power, 
                      i.L2_AC_frequency, i.L3_phase_voltage, i.L3_phase_current, i.L3_power, i.L3_AC_frequency, i.1st_input_voltage, i.1st_input_current, 
                      i.1st_input_power, i.2nd_input_voltage, i.2nd_input_current, i.2nd_input_power, i.3rd_input_voltage, i.3rd_input_current, i.3rd_input_power, 
                      i.4th_input_voltage, i.4th_input_current, i.4th_input_power, i.Energy_today, i.Energy_total, i.totally_active_power, 
                      i.internal_temperature, s.sensor_type_id, s.sensor_type FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor'
                      AND s.area_id = 2";
        $stmt_store = $db->prepare($sql_store);
        $stmt_store->execute();
        return $stmt_store->fetchAll(PDO::FETCH_ASSOC);
    }
    $invertor_store = get_store($db);

    function get_statuses($db, $area_id) {
        $sql_statuses = "SELECT i.id, i.Operation_mode, i.Error_message_1, i.Error_message_2, i.Warning_code, i.Load_de_rating_message, i.data_validity, 
                         s.sensor_type_id, s.sensor_type FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
                         AND s.area_id = :area_id";
        $stmt = $db->prepare($sql_statuses);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>變流器-<?php echo $area_name; ?></h3>
        </div>
        <div class="col-lg-12">
        <div> <span class="ps">提醒:下列表格超出範圍時可橫向/直向移動</span><br>    
                <i class="fas fa-circle gray"></i> 斷線 <i class=" fas fa-circle green"></i> 正常 <i
                    class=" fas fa-circle orange"></i> 告警 <i class=" fas fa-circle red"></i> 錯誤 <i
                    class=" fas fa-circle pink"></i> 狀態異常
            </div>
            <div class="row">
                <div class="invertor">
                    <table>
                        <thead>
                            <tr>
                                <th>變流器編號</th>
                                <?php
                                    $statuses = get_statuses($db, 2);
                                    foreach ($store_device as $index => $row) {
                                        $two_note_error = $statuses[$index]["Error_message_1"] | $statuses[$index]["Error_message_2"];
                                        $notes = [
                                            strrev(decbin($statuses[$index]["Operation_mode"])),
                                            strrev(decbin($two_note_error)),
                                            strrev(decbin($statuses[$index]["Warning_code"])),
                                            strrev(decbin($statuses[$index]["Load_de_rating_message"])),
                                            $statuses[$index]["data_validity"] == 1 ? "" : "1"
                                        ];
                                        $colors = ["green", "red", "orange", "pink", "gray"];
                                        $error = [];
                                        foreach ($notes as $note_index => $note) {
                                            for ($i = 0; $i < strlen($note); $i++) {
                                                if ($note[$i] == "1") {
                                                    $error[] = "<i class='fas fa-circle " . $colors[$note_index] . "'></i>";
                                                    break;
                                                }
                                            }
                                        }
                                        $errors = implode(" ", $error);
                                        echo "<th>". $errors. $row["name"]. "</th>";
                                    }


                                    // $statuses = get_statuses($db, 2);
                                    // foreach ($store_device as $index => $row) {
                                    //     $two_note_error = $statuses[$index]["Error_message_1"] | $statuses[$index]["Error_message_2"];
                                    //     $notes = [
                                    //         strrev(decbin($statuses[$index]["Operation_mode"])),
                                    //         strrev(decbin($two_note_error)),
                                    //         strrev(decbin($statuses[$index]["Warning_code"])),
                                    //         strrev(decbin($statuses[$index]["Load_de_rating_message"])),
                                    //     ];
                                    //     $gray = $statuses[$index]["data_validity"];
                                    //     $colors = ["red", "orange", "pink", "gray"];
                                    //     $error = [];
                                    //     $noError = true;
                                    //     foreach ($notes as $note_index => $note) {
                                    //         for ($i = 0; $i < strlen($note); $i++) {
                                    //             if ($note[$i] == "1") {
                                    //                 if (isset($colors[$note_index])) {
                                    //                     $error[] = "<i class = 'fas fa-circle". $colors[$note_index]. "'></i>";
                                    //                     $noError = false;
                                    //                     break;
                                    //                 }
                                    //             }
                                    //         }
                                    //     }
                                    //     if ($gray != 1) {
                                    //         $error[] = "<i class = 'fas fa-circle gray'></i>";
                                    //         $noError = false;
                                    //     }
                                    //     if ($noError) {
                                    //         $error[] = "<i class = 'fas fa-circle green'></i>";
                                    //     }
                                    //     $errors = implode("", $error);
                                    //     echo "<th>". $errors. $row["name"]. "</th>";
                                    // }
                                ?>                                                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>廠牌型號 </td>
                                <?php
                                    foreach ($store_device as $row) {
                                        echo "<td>". $row["hardware_device"]. "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>今日發電量kWh </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["Energy_today"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>總發電量kWh </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["Energy_total"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>即時發電量kW </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["totally_active_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>轉換效益% </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        $dc_input_power = ($row["1st_input_power"] + $row["2nd_input_power"] + $row["3rd_input_power"] + $row["4th_input_power"]) * 0.1;
                                        $ac_output_power = ($row["L1_power"] + $row["L2_power"] + $row["L3_power"]) * 0.1;
                                        $conversion_efficiency = 0;
                                        if ($dc_input_power > 0) {
                                            $conversion_efficiency = number_format(($ac_output_power / $dc_input_power) / 1000, 4); 
                                        }
                                        echo "<td>$conversion_efficiency</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>內部溫度&#176;C </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["internal_temperature"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串直流電壓V </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["1st_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串直流電流A </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["1st_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["1st_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串直流電壓V </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["2nd_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串直流電流A </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["2nd_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["2nd_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串直流電壓V </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["3rd_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串直流電流A </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["3rd_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["3rd_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串直流電壓V </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["4th_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串直流電流A </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["4th_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["4th_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流電壓V</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L1_phase_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流電流A</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L1_phase_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流功率kW</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L1_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L1_AC_frequency"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流電壓V</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L2_phase_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流電流A</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L2_phase_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流功率kW</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L2_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L2_AC_frequency"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流電壓V</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L3_phase_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流電流A</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L3_phase_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流功率kW</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L3_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_store as $row) {
                                        echo "<td>". number_format($row["L3_AC_frequency"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note1</td>
                                <?php
                                    $modes = [
                                        "0x00","0x01","0x02","0x03","0x05",
                                        "0x07","0x08"
                                    ];
                                    $statuses = get_statuses($db, 2);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note1 = strrev(decbin($status["Operation_mode"]));
                                        for ($i = 0; $i < strlen($note1); $i++) {
                                            if ($note1[$i] == "1") {
                                                echo $modes[$i];
                                                if ($i < strlen($note1) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                        // test 斷線不顯示note1~5
                                    // foreach ($statuses as $status) {
                                    //     echo "<td>";
                                    //     if ($status["data_validity"] == 1) {
                                    //         $note1 = strrev(decbin($status["Operation_mode"]));
                                    //         for ($i = 0; $i < strlen($note1); $i++) {
                                    //             if ($note1[$i] == "1") {
                                    //                 echo $mode[$i];
                                    //                 if ($i < strlen($note1) - 1){
                                    //                     echo "<br>";
                                    //                 }
                                    //             }
                                    //         }
                                    //     }
                                    //     echo "</td>";
                                    // }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note2</td>
                                <?php
                                    $modes_note2 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 2);
                                        foreach ($statuses as $status) {
                                            echo "<td>";
                                            $note2 = strrev(decbin($status["Error_message_1"]));
                                            for ($i = 0; $i < strlen($note2); $i++) {
                                                if ($note2[$i] == "1") {
                                                    echo $modes_note2[$i];
                                                    if ($i < strlen($note2) - 1) {
                                                        echo "<br>";
                                                    }
                                                }
                                            }
                                            echo "</td>";
                                        }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note3</td>
                                <?php
                                    $modes_note3 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 2);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note3 = strrev(decbin($status["Error_message_2"]));
                                        for ($i = 0; $i < strlen($note3); $i++) {
                                            if ($note3[$i] == "1") {
                                                echo $modes_note3[$i];
                                                if ($i < strlen($note3) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note4</td>
                                <?php
                                    $modes_note4 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 2);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note4 = strrev(decbin($status["Warning_code"]));
                                        for ($i = 0; $i < strlen($note4); $i++) {
                                            if ($note4[$i] == "1") {
                                                echo $modes_note4[$i];
                                                if ($i < strlen($note4) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note5</td>
                                <?php
                                    $modes_note5 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 2);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note5 = strrev(decbin($status["Load_de_rating_message"]));
                                        for ($i = 0; $i < strlen($note5); $i++) {
                                            if ($note5[$i] == "1") {
                                                echo $modes_note5[$i];
                                                if ($i < strlen($note5) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <!-- <tr>
                                <td class="warning">斷線</td>
                                <?php           
                                    $statuses = get_statuses($db, 2);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $data_validity = $status["data_validity"];
                                        if ($data_validity != 1) {
                                            echo $data_validity;
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>





<!-- convertre-c.php -->
<?php
    header("Refresh: 300");
    include("head.php");

    function get_inverter($db, $id = null)
    {
        if ($id === null) {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id 
                    WHERE s.sensor_type = 'invertor' AND s.area_id = 3";
            $stmt = $db->prepare($sql);
        } else {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id 
                    WHERE s.sensor_type = 'invertor' AND s.area_id = 3 AND i.id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // area
    function get_area($db, $id)
    {
        $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["name"] : "";
    }

    $selected = isset($_GET["inverter"]) ? (int) $_GET["inverter"] : 0;
    $inverters = [];
    if ($selected > 0) {
        $inverters = get_inverter($db, $selected);
    } else {
        $inverters = get_inverter($db);
    }

    $area_id = 3;
    $area_name = get_area($db, $area_id);

    // 變流器編號 廠牌型號
    function get_rolled_device($db) {
        $sql_rolled_device = "SELECT s.hardware_device, s.name, s.sensor_type_id, i.id FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id 
                              WHERE s.sensor_type = 'invertor' AND s.area_id = 3";
        $stmt = $db->prepare($sql_rolled_device);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $rolled_device = get_rolled_device($db);

    function get_rolled($db) {
        $sql_rolled = "SELECT i.id, i.L1_phase_voltage, i.L1_phase_current, i.L1_power, i.L1_AC_frequency, i.L2_phase_voltage, i.L2_phase_current, i.L2_power, 
                      i.L2_AC_frequency, i.L3_phase_voltage, i.L3_phase_current, i.L3_power, i.L3_AC_frequency, i.1st_input_voltage, i.1st_input_current, 
                      i.1st_input_power, i.2nd_input_voltage, i.2nd_input_current, i.2nd_input_power, i.3rd_input_voltage, i.3rd_input_current, i.3rd_input_power, 
                      i.4th_input_voltage, i.4th_input_current, i.4th_input_power, i.Energy_today, i.Energy_total, i.totally_active_power, 
                      i.internal_temperature, s.sensor_type_id, s.sensor_type FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
                      AND s.area_id = 3";
        $stmt_rolled = $db->prepare($sql_rolled);
        $stmt_rolled->execute();
        return $stmt_rolled->fetchAll(PDO::FETCH_ASSOC);
    }
    $invertor_rolled = get_rolled($db);

    function get_statuses($db, $area_id) {
        $sql_statuses = "SELECT i.id, i.Operation_mode, i.Error_message_1, i.Error_message_2, i.Warning_code, i.Load_de_rating_message, i.data_validity, 
                         s.sensor_type_id, s.sensor_type FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor'
                         AND s.area_id = :area_id";
        $stmt = $db->prepare($sql_statuses);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>變流器-<?php echo $area_name; ?></h3>
        </div>
        <div class="col-lg-12">
        <div> <span class="ps">提醒:下列表格超出範圍時可橫向/直向移動</span><br>    
                <i class="fas fa-circle gray"></i> 斷線 <i class=" fas fa-circle green"></i> 正常 <i
                    class=" fas fa-circle orange"></i> 告警 <i class=" fas fa-circle red"></i> 錯誤 <i
                    class=" fas fa-circle pink"></i> 狀態異常
            </div>
            <div class="row">
                <div class="invertor">
                    <table>
                        <thead>
                            <tr>
                                <th>變流器編號</th>
                                <?php
                                    $statuses = get_statuses($db, 3);
                                    foreach ($rolled_device as $index => $row) {
                                        $two_note_error = $statuses[$index]["Error_message_1"] | $statuses[$index]["Error_message_2"];
                                        $notes = [
                                            strrev(decbin($statuses[$index]["Operation_mode"])),
                                            strrev(decbin($two_note_error)),
                                            strrev(decbin($statuses[$index]["Warning_code"])),
                                            strrev(decbin($statuses[$index]["Load_de_rating_message"])),
                                            $statuses[$index]["data_validity"] == 1 ? "" : "1"
                                        ];
                                        $colors = ["green", "red", "orange", "pink", "gray"];
                                        $error = [];
                                        foreach ($notes as $note_index => $note) {
                                            for ($i = 0; $i < strlen($note); $i++) {
                                                if ($note[$i] == "1") {
                                                    $error[] = "<i class='fas fa-circle " . $colors[$note_index] . "'></i>";
                                                    break;
                                                }
                                            }
                                        }
                                        $errors = implode(" ", $error);
                                        echo "<th>". $errors. $row["name"]. "</th>";
                                    }

                                    // 無代碼亮綠燈,有代碼不亮綠燈
                                    // $statuses = get_statuses($db, 3);
                                    // foreach ($rolled_device as $index => $row) {
                                    //     $two_note_error = $statuses[$index]["Error_message_1"] | $statuses[$index]["Error_message_2"];
                                    //     $notes = [
                                    //         strrev(decbin($statuses[$index]["Operation_mode"])),
                                    //         strrev(decbin($two_note_error)),
                                    //         strrev(decbin($statuses[$index]["Warning_code"])),
                                    //         strrev(decbin($statuses[$index]["Load_de_rating_message"])),
                                    //     ];
                                    //     $gray = $statuses[$index]["data_validity"];
                                    //     $colors = ["red", "orange", "pink", "gray"];
                                    //     $error = [];
                                    //     $noError = true;  
                                    //     foreach ($notes as $note_index => $note) {
                                    //         for ($i = 0; $i < strlen($note); $i++) {
                                    //             if ($note[$i] == "1") {
                                    //                 if (isset($colors[$note_index])) {
                                    //                     $error[] = "<i class='fas fa-circle " . $colors[$note_index] . "'></i>";
                                    //                     $noError = false;
                                    //                     break;
                                    //                 }
                                    //             }
                                    //         }
                                    //     }
                                    //     if ($gray != 1) {   // 如果data_validity不為1，顯示灰色燈
                                    //         $error[] = "<i class='fas fa-circle gray'></i>";
                                    //         $noError = false;
                                    //     }
                                    //     if ($noError) {   // 如果所有狀態檢查皆無異常，則顯示綠色燈
                                    //         $error[] = "<i class='fas fa-circle green'></i>";
                                    //     }
                                    //     $errors = implode(" ", $error);
                                    //     echo "<th>". $errors. $row["name"]. "</th>";
                                    // }
                                ?>                                                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>廠牌型號 </td>
                                <?php
                                    foreach ($rolled_device as $row) {
                                        echo "<td>". $row["hardware_device"]. "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>今日發電量kWh </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["Energy_today"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>總發電量kWh </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["Energy_total"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>即時發電量kW </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["totally_active_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>轉換效益% </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        $dc_input_power = (($row["1st_input_power"] * 0.1 / 1000) + ($row["2nd_input_power"] * 0.1 / 1000) + ($row["3rd_input_power"] * 0.1 / 1000) + ($row["4th_input_power"] * 0.1 / 1000));
                                        $ac_output_power = (($row["L1_power"] * 0.1 / 1000) + ($row["L2_power"] * 0.1 / 1000) + ($row["L3_power"] * 0.1 / 1000));
                                        // $conversion_efficiency = 0;
                                        // if ($dc_input_power > 0) {
                                        //     $conversion_efficiency = number_format(($ac_output_power / $dc_input_power) * 100, 5);
                                        // }
                                        $conversion_efficiency = number_format(($ac_output_power / $dc_input_power) * 100, 2);
                                        // var_dump($dc_input_power);
                                        // echo "</br>";
                                        // var_dump($ac_output_power);
                                        // var_dump(($row["L1_power"] * 0.1 / 1000));
                                        // echo "</br>";
                                        // var_dump($row["L2_power"]);
                                        // echo "</br>";
                                        // var_dump($row["L3_power"]);
                                        echo "<td>$conversion_efficiency</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>內部溫度&#176;C </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["internal_temperature"], 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串直流電壓V </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["1st_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串直流電流A </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["1st_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>1串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["1st_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串直流電壓V </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["2nd_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串直流電流A </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["2nd_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>2串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["2nd_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串直流電壓V </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["3rd_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串直流電流A </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["3rd_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>3串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["3rd_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串直流電壓V </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["4th_input_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串直流電流A </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["4th_input_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>4串輸入功率kW </td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["4th_input_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流電壓V</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L1_phase_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流電流A</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L1_phase_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流功率kW</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L1_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L1交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L1_AC_frequency"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流電壓V</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L2_phase_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流電流A</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L2_phase_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流功率kW</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L2_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L2交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L2_AC_frequency"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流電壓V</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L3_phase_voltage"] * 0.1, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流電流A</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L3_phase_current"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流功率kW</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L3_power"] * 0.1 / 1000, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td>L3交流頻率Hz</td>
                                <?php
                                    foreach ($invertor_rolled as $row) {
                                        echo "<td>". number_format($row["L3_AC_frequency"] * 0.01, 4). "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note1</td>
                                <?php
                                    $modes = [
                                        "0x00","0x01","0x02","0x03","0x05",
                                        "0x07","0x08"
                                    ];
                                    $statuses = get_statuses($db, 3);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note1 = strrev(decbin($status["Operation_mode"]));
                                        for ($i = 0; $i < strlen($note1); $i++) {
                                            if ($note1[$i] == "1") {
                                                echo $modes[$i];
                                                if ($i < strlen($note1) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                        // test 斷線不顯示note1~5
                                    // foreach ($statuses as $status) {
                                    //     echo "<td>";
                                    //     if ($status["data_validity"] == 1) {
                                    //         $note1 = strrev(decbin($status["Operation_mode"]));
                                    //         for ($i = 0; $i < strlen($note1); $i++) {
                                    //             if ($note1[$i] == "1") {
                                    //                 echo $mode[$i];
                                    //                 if ($i < strlen($note1) - 1){
                                    //                     echo "<br>";
                                    //                 }
                                    //             }
                                    //         }
                                    //     }
                                    //     echo "</td>";
                                    // }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note2</td>
                                <?php
                                    $modes_note2 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 3);
                                        foreach ($statuses as $status) {
                                            echo "<td>";
                                            $note2 = strrev(decbin($status["Error_message_1"]));
                                            for ($i = 0; $i < strlen($note2); $i++) {
                                                if ($note2[$i] == "1") {
                                                    echo $modes_note2[$i];
                                                    if ($i < strlen($note2) - 1) {
                                                        echo "<br>";
                                                    }
                                                }
                                            }
                                            echo "</td>";
                                        }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note3</td>
                                <?php
                                    $modes_note3 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 3);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note3 = strrev(decbin($status["Error_message_2"]));
                                        for ($i = 0; $i < strlen($note3); $i++) {
                                            if ($note3[$i] == "1") {
                                                echo $modes_note3[$i];
                                                if ($i < strlen($note3) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note4</td>
                                <?php
                                    $modes_note4 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 3);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note4 = strrev(decbin($status["Warning_code"]));
                                        for ($i = 0; $i < strlen($note4); $i++) {
                                            if ($note4[$i] == "1") {
                                                echo $modes_note4[$i];
                                                if ($i < strlen($note4) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td class="warning">Note5</td>
                                <?php
                                    $modes_note5 = [
                                        "0","1","2","3","4",
                                        "5","6","7","8","9",
                                        "10","11","12","13","14",
                                        "15"
                                    ];
                                    $statuses = get_statuses($db, 3);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $note5 = strrev(decbin($status["Load_de_rating_message"]));
                                        for ($i = 0; $i < strlen($note5); $i++) {
                                            if ($note5[$i] == "1") {
                                                echo $modes_note5[$i];
                                                if ($i < strlen($note5) - 1) {
                                                    echo "<br>";
                                                }
                                            }
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr>
                            <!-- <tr>
                                <td class="warning">斷線</td>
                                <?php           
                                    $statuses = get_statuses($db, 3);
                                    foreach ($statuses as $status) {
                                        echo "<td>";
                                        $data_validity = $status["data_validity"];
                                        if ($data_validity != 1) {
                                            echo $data_validity;
                                        }
                                        echo "</td>";
                                    }
                                ?>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>

























































<?php 
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
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
        $sql_power = "SELECT SUM(i.totally_active_power) AS totally_active_power, SUM(i.Energy_today) AS Energy_today, SUM(i.Energy_total) AS Energy_total,
                      i.update_date, MIN(i.update_date) FROM invertor i JOIN sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
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
        $sql_yesterday = "SELECT SUM(il.Energy_today) AS energy_today, il.add_date, il.invertor_id, s.area_id, s.sensor_type, s.sensor_type_id
                          FROM invertor_log il JOIN sensor s ON il.invertor_id = s.sensor_type_id
                          WHERE s.sensor_type = 'invertor' AND s.area_id = :area_id AND DATE(il.add_date) = :date_yesterday ORDER BY il.add_date DESC";
        $stmt = $db->prepare($sql_yesterday);
        $stmt->bindParam(":area_id", $area_id);
        $stmt->bindParam(":date_yesterday", $date_yesterday, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $capacity = getData($db, $area_id)['capacity'];
        $result["daily_average_power"] = $result["energy_today"] / $capacity;
        return $result;
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
    // 模板溫度
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
            if ($row["Operation_mode"] != 0 || $row["Error_message_1"] != 0 || $row["Error_message_2"] != 0 || $row["Warning_code"] != 0 || $row["Load_de_rating_message"] != 0) {
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
    //     total_pr(2,$result["total_capacity"])
    // 累積日照
    function cumulative($db, $area_id) {
        $today = date("Y-m-d");
        $sql_cumulative = "SELECT pl.id, pl.pyranometer_id, pl.solar_irradiance, pl.add_date, s.sensor_type, s.sensor_type_id FROM pyranometer_log pl
                           JOIN sensor s ON pl.pyranometer_id = s.sensor_type_id WHERE s.sensor_type = 'pyranometer' AND s.area_id = :area_id
                           AND DATE (pl.add_date) = :today GROUP BY pl.add_date ORDER BY pl.add_date ASC";
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
                $solar_change = $solar_irradiance + $before_solar;
                $cumulative_solar += ($solar_change * $time_diff) / 2 / 3600 / 1000;   // 累積日照 
                // var_dump($solar_change);        
                // var_dump($time_diff);             
            }
            $before_solar = $solar_irradiance;
            $before_time = $row["add_date"];
            // var_dump($cumulative_solar);   
        }
        return $cumulative_solar;
    }
?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>監控總覽</h3>
        </div>
        <!-- 總計~Area2 切換按鈕 -->
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <?php
                $area_name_1 = getData($db, 1);
                $area_name_2 = getData($db, 2);
                $area_name_3 = getData($db, 3);
            ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">廠區發電總計</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area1a-tab" data-bs-toggle="pill" data-bs-target="#pills-Area1a" type="button" role="tab" aria-controls="pills-Area1a" aria-selected="false"><?php echo $area_name_1["name"]; ?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area1b-tab" data-bs-toggle="pill" data-bs-target="#pills-Area1b" type="button" role="tab" aria-controls="pills-Area1b" aria-selected="false"><?php echo $area_name_2["name"]; ?></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-Area2-tab" data-bs-toggle="pill" data-bs-target="#pills-Area2" type="button" role="tab" aria-controls="pills-Area2" aria-selected="false"><?php echo $area_name_3["name"]; ?></button>
            </li>
        </ul>

        <!-- Area1a ~ Area2 共用地圖-->
        <div id="area-map" class="row justify-content-center d-none">
            <div class="d-block col col-xl-8 position-relative" >
                <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                <!-- <div class="map-button">
                    <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                    <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                    <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                    <div class="map-area1">
                        <p class="rounded-circle">A</p>
                        <p class="rounded-circle">B</p>
                        <p class="rounded-circle">C</p>
                        <p class="rounded-circle">D</p>
                        <p class="rounded-circle">E</p>
                        <p class="rounded-circle">F</p>
                    </div>
                    <div class="map-area2">
                        <p class="rounded-circle">H</p>
                        <p class="rounded-circle">I</p>
                    </div>
                    <ul class="map-area3">
                        <li><p class="rounded-circle">J</p></li>
                        <li><p class="rounded-circle">K</p></li>
                        <li><p class="rounded-circle">L</p></li>
                        <li><p class="rounded-circle">M</p></li>
                        <li><p class="rounded-circle">N</p></li>
                        <li><p class="rounded-circle">O</p></li>
                        <li><p class="rounded-circle">P</p></li>
                        <li><p class="rounded-circle">Q</p></li>
                    </ul>

                </div> -->
            </div>
        </div>
        <!-- 總計~Area2 切換內容 -->
        <div class="tab-content" id="pills-tabContent">
            <!-- 總計內容 -->
            <div class="row tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="row justify-content-center">
                            <img src="./assets/img/solar_place.jpg" class="d-block col col-xl-6" alt="">
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
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($total_capacity, 4); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($totally_active_power * 0.1 / 1000, 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($energy_today, 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($energy_today / $total_capacity, 4); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo number_format($yesterday_power, 4); ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo number_format($yesterday_power_hour, 4); ?>kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($total_pr, 4); ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($coe, 4); ?> kg</div></div>
                        </div>
                                            
            </div>
            <!-- Area1a 1.維護廠房內容 -->
            <div class="row tab-pane fade" id="pills-Area1a" role="tabpanel" aria-labelledby="pills-Area1a-tab">
                    <div class="row justify-content-center d-none">
                        <div class="d-block col col-xl-8 position-relative" >
                            <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                            <div class="map-button">
                                <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                                <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                                <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                            </div>
                        </div>
                    </div>
                    <div class="map-title">
                        <h3 class="bg-info rounded-circle">1</h3>
                        <span class="fs-3"><?php echo $area_name_1["name"]; ?></span>
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
                    ?>
                    <div class="row">
                    <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($area_maintain["capacity"], 4); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($power_maintain["totally_active_power"] * 0.1 / 1000, 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($power_maintain["Energy_today"], 4); ?> kWh</div></div> 
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($power_maintain["Energy_today"] / $area_maintain["capacity"], 4); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 4) : 0; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 4) : 0; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($pr, 4); ?>%</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($power_maintain["Energy_today"] * $area_maintain["coe"], 4); ?> 公斤</div></div>
                        <!-- 溫度計 -->    
                        <?php foreach ($thermometer as $temp_info): ?>
                            <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                            <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo number_format($temp, 4); ?> &#176;C</div></div>
                        <?php endforeach; ?>
                        <!-- 日照計 -->                        
                        <?php foreach ($pyranometer as $solar_irradiance_info): ?>
                            <?php $solar_irradiance = $solar_irradiance_info["solar_irradiance"] == 0 ? 0 : $solar_irradiance_info["solar_irradiance"]; ?>
                        <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo number_format($solar_irradiance, 4); ?> W/㎡</div></div>
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
            <!-- Area1b 2.備品儲區內容 -->
            <div class="row tab-pane fade" id="pills-Area1b" role="tabpanel" aria-labelledby="pills-Area1b-tab">
                <div class="row justify-content-center d-none">
                    <div class="d-block col col-xl-8 position-relative" >
                        <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                        <div class="map-button">
                            <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                            <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                            <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                        </div>
                    </div>
                </div>

                <div class="map-title">
                    <h3 class="bg-info rounded-circle">2</h3>
                    <span class="fs-3">設備處備品儲區</span>
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
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($area_store["capacity"], 4); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($power_store["totally_active_power"] * 0.1 / 1000, 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($power_store["Energy_today"], 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($power_store["Energy_today"] / $area_store["capacity"], 4); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 4) : 0; ?> kWh</div></div>  
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 4) : 0; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($pr, 4); ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($power_store["Energy_today"] * $area_store["coe"], 4); ?> kg</div></div>
                    <!-- 溫度計 -->                
                    <?php foreach ($thermometer as $temp_info): ?>   
                        <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                    <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php echo $temp_info["name"]; ?><div class="monitor-footer"><?php echo number_format($temp, 4); ?> &#176;C</div></div>                    
                    <?php endforeach; ?>
                    <!-- 日照計 -->               
                    <?php foreach ($pyranometer as $solar_irradiance_info): ?> 
                        <?php $solar_irradiance = $solar_irradiance_info["solar_irradiance"] == 0 ? : $solar_irradiance_info["solar_irradiance"]; ?>   
                    <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo number_format($solar_irradiance, 4); ?> W/㎡</div></div>
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
            <!-- Area2 3.太陽能發電廠內容 -->
            <div class="row tab-pane fade" id="pills-Area2" role="tabpanel" aria-labelledby="pills-Area2-tab">
                        <div class="row justify-content-center d-none">
                            <div class="d-block col col-xl-8 position-relative" >
                                <img src="./assets/img/solar_place.jpg" class="col-12" alt="">
                                <div class="map-button">
                                    <button type="button" class="btn btn-info rounded-circle py-1">1</button>
                                    <button type="button" class="btn btn-info rounded-circle py-1">2</button>
                                    <button type="button" class="btn btn-info rounded-circle py-1">3</button>
                                </div>
                            </div>
                        </div>
                        <div class="map-title">
                            <h3 class="bg-info rounded-circle">3</h3>
                            <span class="fs-3">軋鋼西側廠房</span>
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
                        <div class="monitor1"><i class=" fas fa-clock"></i><br> 裝置容量<div class="monitor-footer"><?php echo number_format($area_rolled["capacity"], 4); ?> kWp</div></div>
                            <div class="monitor1"><i class=" fas fa-solar-panel"></i><br> 即時發電量<div class="monitor-footer"><?php echo number_format($power_rolled["totally_active_power"]*0.1 / 1000, 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-bolt"></i><br> 今日發電量<div class="monitor-footer"><?php echo number_format($power_rolled["Energy_today"], 4); ?> kWh</div></div>
                            <div class="monitor1"><i class=" fas fa-clock"></i><br> 今日發電小時<div class="monitor-footer"><?php echo number_format($power_rolled["Energy_today"] / $area_rolled["capacity"], 4); ?> kWh/kWp</div></div>                            
                            <div class="monitor2"><i class=" fas fa-chart-bar"></i><br> 昨日發電量<div class="monitor-footer"><?php echo isset($yesterday_power["energy_today"]) ? number_format($yesterday_power["energy_today"], 4) : 0; ?> kWh</div></div>
                            <div class="monitor2"><i class=" fas fa-history"></i><br> 昨日發電小時<div class="monitor-footer"><?php echo isset($yest_powerh) ? number_format($yest_powerh, 4) : 0; ?> kWh/kWp</div></div>
                            <div class="monitor4"><i class=" fas fa-tachometer-alt"></i><br> PR值<div class="monitor-footer"><?php echo number_format($pr, 4); ?> %</div></div>
                            <div class="monitor4"><i class=" fas fa-industry"></i><br> 碳排量<div class="monitor-footer"><?php echo number_format($power_rolled["Energy_today"] * $area_rolled["coe"], 4); ?> kg</div></div>
                            <!-- 溫度計 -->
                            <?php foreach ($thermometer as $temp_info): ?>
                                <?php $temp = $temp_info["temperature"] == 0 ? 0 : $temp_info["temperature"]; ?>
                            <div class="monitor3"><i class=" fas fa-temperature-high"></i><br><?php $temp_info["name"]; ?><div class="monitor-footer"><?php echo number_format($temp, 4); ?> &#176;C</div></div>
                            <?php endforeach; ?>
                            <!-- 日照計 -->
                            <?php foreach ($pyranometer as $solar_irradiance_info): ?>
                                <?php $solar_irradiance = $solar_irradiance_info["solar_irradiance"] == 0 ? 0 : $solar_irradiance_info["solar_irradiance"]; ?>
                            <div class="monitor3"><i class=" fas fa-sun"></i><br><?php echo $solar_irradiance_info["name"]; ?><div class="monitor-footer"><?php echo number_format($solar_irradiance, 4); ?> W/㎡</div></div>
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