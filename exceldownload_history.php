<?php
function exceldownload()
{
    require 'vendor/autoload.php';
    include_once('db.php');
    $judge = isset($_POST['value']) ? $_POST['value'] : null;//判斷 1.報表(每日) 2.報表(每月) 3.報表(每年)
    $area = isset($_POST['value1']) ? $_POST['value1'] : null;//地區id
    $year = isset($_POST['value2']) ? $_POST['value2'] : null;//年或日
    $month = isset($_POST['value3']) ? $_POST['value3'] : null;//月份
    $date_day = $year . '-' . $month;
    // 創建一个新的 Excel 對象
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    // 獲取默認的工作表
    $sheet = $spreadsheet->getActiveSheet();
    // 區域
    $sql_area = "SELECT id,name FROM area";
    $stmt = $db->prepare($sql_area);
    $stmt->execute();
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // 區域資訊 1.案場名稱  2.容量(kWp)
    if ($area == 'all') {
        $total_capacity = 0; 
        $areaIds = [1, 2, 3];  // 區域id
        $areaList = [];  // 存儲查詢的陣列
        foreach ($areaIds as $areaId) {
            $sql_area_list = "SELECT id, name, capacity FROM area WHERE id = :area_id";
            $stmt_list = $db->prepare($sql_area_list);
            $stmt_list->bindParam(':area_id', $areaId, PDO::PARAM_INT);
            $stmt_list->execute();
            $areaData = $stmt_list->fetch(PDO::FETCH_ASSOC);
            $total_capacity = $total_capacity+$areaData['capacity'];
            if ($areaData) {
                $areaList[] = $areaData;  // 將查詢結果加入陣列
            }
        }
    } else {
        $sql_area_list = "SELECT id, name, capacity FROM area WHERE id = :area_id";
        $stmt_list = $db->prepare($sql_area_list);
        $stmt_list->bindParam(':area_id', $area, PDO::PARAM_INT);
    }
    $stmt_list->execute();
    $areas_list = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
    // 月份判斷
    $daysInMonth =date("t", strtotime($date_day.'-1'));
    if(isset($_POST['value2'])){
        $add_date_count = "SELECT id,add_date as judge_date FROM invertor_log WHERE DATE(`add_date`) =  :st_date
                            GROUP BY DATE_FORMAT(`add_date`, '%Y-%m-%d %H')
                           "; // 搜尋筆數
        $stmt_invertor_count = $db->prepare($add_date_count);
        $stmt_invertor_count->bindParam(':st_date', $_POST['value2'], PDO::PARAM_STR);
        $stmt_invertor_count->execute();
        $add_date_count = $stmt_invertor_count->fetchall(PDO::FETCH_ASSOC);
    }
    function Power_GenerationRows_d($db, $i, $date_day_hour, $area_id, $ex_total_energy)
    {
        //global $db;
        $ex_date_day_hour = date("Y-m-d",strtotime($date_day_hour));
        $sql_power_list = " SELECT SUM(il.Energy_today) AS total_energy
                            FROM invertor_log il
                            JOIN sensor s ON il.invertor_id = s.sensor_type_id
                            WHERE s.sensor_type = 'invertor'
                            AND s.area_id = :area_id
                            AND il.add_date <= :date_hour AND il.add_date > :ex_date
                            GROUP BY il.add_date
                            order by il.add_date desc limit 1
                            ";
        $stmt_power_list = $db->prepare($sql_power_list);
        $stmt_power_list->bindParam(':date_hour', $date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':ex_date', $ex_date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt_power_list->execute();
        $power_generation_list = $stmt_power_list->fetchAll(PDO::FETCH_ASSOC);
        $total_energy = 0;
        $ex_total_energy = 0;
        if(count($power_generation_list)>0){
            foreach ($power_generation_list as $power_row) {
            if ($i == 0) {
                $total_energy = !empty($power_row['total_energy']) ? $power_row['total_energy'] : 0;
            } else {
                $total_energy = $power_row['total_energy'] - $ex_total_energy;
            }
            $ex_total_energy = $power_row['total_energy'];
            }
            return [$total_energy,$ex_total_energy];
        }else{
            return [$total_energy,$ex_total_energy];
        }
    }
    function Cumulative_SunshineRows_d($db, $i, $date_day_hour, $area_id, $ex_sunshine, $ex_sunshine_add_date, $total_energy, $area_capacity)
    {
        //global $db;
        $ex_date_day_hour = date("Y-m-d",strtotime($date_day_hour));
        $sql_power_list = " SELECT sum(pl.solar_irradiance) as solar_irradiance , pl.add_date
                            FROM pyranometer_log pl
                            JOIN sensor s ON s.sensor_type_id = pl.pyranometer_id
                            WHERE s.area_id = :area_id
                            AND s.sensor_type='pyranometer'
                            AND pl.data_validity = 1
                            AND pl.add_date <= :date_hour
                            AND pl.add_date >= :ex_date
                            GROUP by pl.add_date
                            ";
        $stmt_power_list = $db->prepare($sql_power_list);
        $stmt_power_list->bindParam(':date_hour', $date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':ex_date', $ex_date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt_power_list->execute();
        $cumulative_sunshine_list = $stmt_power_list->fetchAll(PDO::FETCH_ASSOC);
        $ans = 0;   //總量
        $DMY = 0;   //日平均發電
        $PR = 0;
        $ex_sunshine = 0; //前一個日照量
        $ex_sunshine_add_date = 0;  //前一個時間
        $all_total_sunshine =0; //總日照量
        if(count($cumulative_sunshine_list)>0){
            foreach ($cumulative_sunshine_list as $sunshine_row) {
                if ($sunshine_row['solar_irradiance'] == 0 || $ex_sunshine==0) {
                    $total_sunshine = 0;
                } else {
                    $all_total_sunshine = ($sunshine_row['solar_irradiance']+$ex_sunshine-8)*125;
                    $total_sunshine = $all_total_sunshine*(strtotime($sunshine_row['add_date'])-strtotime($ex_sunshine_add_date))/2/3600/1000;
                }
                $ans = $ans + $total_sunshine;
                if(!empty($total_energy) && !empty($ans)){
                    $DMY = $total_energy/$area_capacity;
                    $PR  = $DMY/$ans*100;
                    $PR  = number_format(round($PR, 4) , 2);
                }
                $ex_sunshine = $sunshine_row['solar_irradiance'];
                $ex_sunshine_add_date = $sunshine_row['add_date'];
            }
            return [$ans,$ex_sunshine,$ex_sunshine_add_date,$PR];
        }else{
            return [$ans,$ex_sunshine,$ex_sunshine_add_date,$PR];
        }
    }
    function Power_GenerationRows_m($db, $i, $date_day_hour, $area_id)
    {
        //global $db;
        $ex_date_day_hour = date("Y-m-d",strtotime($date_day_hour));
        $sql_power_list = "SELECT SUM(il.Energy_today) AS total_energy
                            FROM invertor_log il
                            JOIN sensor s ON il.invertor_id = s.sensor_type_id
                            WHERE s.sensor_type = 'invertor'
                            AND s.area_id = :area_id
                            AND il.add_date <= :date_hour AND il.add_date > :ex_date
                            GROUP BY il.add_date
                            order by il.add_date desc limit 1
                            ";
        $stmt_power_list = $db->prepare($sql_power_list);
        $stmt_power_list->bindParam(':date_hour', $date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':ex_date', $ex_date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt_power_list->execute();
        $power_generation_list = $stmt_power_list->fetchAll(PDO::FETCH_ASSOC);
        if(count($power_generation_list)>0){
            foreach ($power_generation_list as $power_row) {
                $total_energy = !empty($power_row['total_energy']) ? $power_row['total_energy'] : 0;
            }
            return $total_energy;
        }else{
            return 0;
        }
    }
    function Cumulative_SunshineRows_m($db, $i, $date_day_hour, $area_id, $ex_sunshine, $ex_sunshine_add_date, $total_energy, $area_capacity)
    {
        //global $db;
        $ex_date_day_hour = date("Y-m-d",strtotime($date_day_hour));
        $time = '23:59:59'; // 特定时间
        $date_day_hour = date('Y-m-d H:i:s', strtotime($date_day_hour . ' ' . $time ));
        $sql_power_list = " SELECT sum(pl.solar_irradiance) as solar_irradiance , pl.add_date
                            FROM pyranometer_log pl
                            JOIN sensor s ON s.sensor_type_id = pl.pyranometer_id
                            WHERE s.area_id = :area_id
                            AND s.sensor_type='pyranometer'
                            AND pl.data_validity = 1
                            AND pl.add_date <= :date_hour
                            AND pl.add_date >= :ex_date
                            GROUP by pl.add_date
                            ";
        $stmt_power_list = $db->prepare($sql_power_list);
        $stmt_power_list->bindParam(':date_hour', $date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':ex_date', $ex_date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt_power_list->execute();
        $cumulative_sunshine_list = $stmt_power_list->fetchAll(PDO::FETCH_ASSOC);
        $ans = 0;   //總量
        $DMY = 0;   //日平均發電
        $PR = 0;
        $ex_sunshine = 0; //前一個日照量
        $ex_sunshine_add_date = 0;  //前一個時間
        $all_total_sunshine =0; //總日照量
        if(count($cumulative_sunshine_list)>0){
            foreach ($cumulative_sunshine_list as $sunshine_row) {
                if ($sunshine_row['solar_irradiance'] == 0 || $ex_sunshine==0) {
                    $total_sunshine = 0;
                } else {
                    $all_total_sunshine = ($sunshine_row['solar_irradiance']+$ex_sunshine-8)*125;
                    $total_sunshine = $all_total_sunshine*(strtotime($sunshine_row['add_date'])-strtotime($ex_sunshine_add_date))/2/3600/1000;
                }
                $ans = $ans + $total_sunshine;
                if(!empty($total_energy) && !empty($ans)){
                    $DMY = $total_energy/$area_capacity;
                    $PR  = $DMY/$ans*100;
                    $PR  = number_format(round($PR, 4) , 2);
                }
                $ex_sunshine = $sunshine_row['solar_irradiance'];
                $ex_sunshine_add_date = $sunshine_row['add_date'];
            }
            return [$ans,$ex_sunshine,$ex_sunshine_add_date,$PR];
        }else{
            return [$ans,$ex_sunshine,$ex_sunshine_add_date,$PR];
        }
    }
    function Power_GenerationRows_y($db, $year, $area_id)
    {
        //global $db;
        $startDateTime = $year.'-01-01';
        $endDateTime = $year.'-12-31';
        //計算每一天的最後一筆發電量
        $sql_power_list =  "SELECT subquery.total_energy
                            FROM ( 
                                SELECT SUM(il.Energy_today) AS total_energy, il.add_date FROM invertor_log il JOIN sensor s ON il.invertor_id = s.sensor_type_id WHERE s.sensor_type = 'invertor' 
                                AND s.area_id = :area_id 
                                AND DATE(il.add_date) 
                                BETWEEN :startDate
                                AND :endDate 
                                AND TIME(il.add_date) <= '23:59:59' 
                                GROUP BY il.add_date 
                                ORDER BY il.add_date DESC ) 
                            AS subquery 
                            GROUP BY DATE(subquery.add_date) ";
        $stmt_power_list = $db->prepare($sql_power_list);
        $stmt_power_list->bindParam(':startDate', $startDateTime, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':endDate', $endDateTime, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt_power_list->execute();
        $result = $stmt_power_list->fetchAll(PDO::FETCH_ASSOC);
        //$total_energy = isset($result['total_energy']) ? $result['total_energy'] : 0;
        $total_energy=0;
        if(count($result)>0){
            foreach ($result as $result_row) {
                $total_energy += $result_row['total_energy'];
            }
        }
        return $total_energy;
    }
    function Cumulative_SunshineRows_y($db, $i, $date_day_hour, $area_id, $ex_sunshine, $ex_sunshine_add_date, $total_energy, $area_capacity)
    {
        //global $db;
        $ex_date_day_hour = date("Y-m-d",strtotime($date_day_hour));
        $time = '23:59:59'; // 特定时间
        $date_day_hour = date('Y-m-d H:i:s', strtotime($date_day_hour . ' ' . $time ));
        $sql_power_list = " SELECT sum(pl.solar_irradiance) as solar_irradiance , pl.add_date
                            FROM pyranometer_log pl
                            JOIN sensor s ON s.sensor_type_id = pl.pyranometer_id
                            WHERE s.area_id = :area_id
                            AND s.sensor_type='pyranometer'
                            AND pl.data_validity = 1
                            AND pl.add_date <= :date_hour
                            AND pl.add_date >= :ex_date
                            GROUP by pl.add_date
                            ";
        $stmt_power_list = $db->prepare($sql_power_list);
        $stmt_power_list->bindParam(':date_hour', $date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':ex_date', $ex_date_day_hour, PDO::PARAM_STR);
        $stmt_power_list->bindParam(':area_id', $area_id, PDO::PARAM_INT);
        $stmt_power_list->execute();
        $cumulative_sunshine_list = $stmt_power_list->fetchAll(PDO::FETCH_ASSOC);
        $ans = 0;   //總量
        $DMY = 0;   //日平均發電
        $PR = 0;
        $ex_sunshine = 0; //前一個日照量
        $ex_sunshine_add_date = 0;  //前一個時間
        $all_total_sunshine =0; //總日照量
        if(count($cumulative_sunshine_list)>0){
            foreach ($cumulative_sunshine_list as $sunshine_row) {
                if ($sunshine_row['solar_irradiance'] == 0 || $ex_sunshine==0) {
                    $total_sunshine = 0;
                } else {
                    $all_total_sunshine = ($sunshine_row['solar_irradiance']+$ex_sunshine-8)*125;
                    $total_sunshine = $all_total_sunshine*(strtotime($sunshine_row['add_date'])-strtotime($ex_sunshine_add_date))/2/3600/1000;
                }
                $ans = $ans + $total_sunshine;
                
                $ex_sunshine = $sunshine_row['solar_irradiance'];
                $ex_sunshine_add_date = $sunshine_row['add_date'];
            }
            return [$ans,$ex_sunshine,$ex_sunshine_add_date];
        }else{
            return [$ans,$ex_sunshine,$ex_sunshine_add_date];
        }
    }
    // 設置表頭
    $sheet->setCellValue('A1', "#");
    $sheet->setCellValue('B1', '日期/時間');
    $sheet->setCellValue('C1', '案場名稱');
    $sheet->setCellValue('D1', '容量(kWp)');
    $sheet->setCellValue('E1', '發電量(kWh)');
    $sheet->setCellValue('F1', '累積日照量(kWh/m2)');
    $sheet->setCellValue('G1', 'PR(%)');
    $ans=0;
    // 填充数据
    $row = 2;
    switch ($judge) {
        case "1":
            if (count($areas_list) > 0) {
                if ($area == 'all') {
                        $ex_total_energy=0;
                        $ex_total_energy =0;
                        $ex_sunshine =0;
                        $ex_sunshine_add_date =0;
                        $PR = 0;
                        for ($i = 0; $i < count($add_date_count); $i++) {
                        $date_day_hour = date("Y-m-d H:00:00", strtotime($add_date_count[$i]['judge_date']));
                        $sheet->setCellValue('A' . $row, ($i+1));
                        $sheet->setCellValue('B' . $row, $date_day_hour);
                        $sheet->setCellValue('C' . $row, '全部');
                        $sheet->setCellValue('D' . $row, $total_capacity);
                        // 發電量(kWh)資訊
                        $all_total_energy = 0;
                        for ($ii = 0; $ii < count($areas); $ii++) {
                            $total_energy = Power_GenerationRows_d($db,$i,$date_day_hour, $ii , $ex_total_energy);
                            $ex_total_energy = $total_energy[1];
                            $all_total_energy =$all_total_energy + $ex_total_energy;
                        }
                        $sheet->setCellValue('E' . $row, $all_total_energy);
                        $all_total_sunshine = 0;
                        for ($ii = 0; $ii < count($areas); $ii++) {
                            $total_sunshine = Cumulative_SunshineRows_d($db,$i, $date_day_hour, ($ii+1), $ex_sunshine, $ex_sunshine_add_date, $all_total_energy, $total_capacity);
                            $ex_sunshine = $total_sunshine[1];
                            $ex_sunshine_add_date = $total_sunshine[2];
                            $all_total_sunshine =$all_total_sunshine + $total_sunshine[0];
                        }
                        $all_total_sunshine = !empty($all_total_sunshine) ? number_format(round($all_total_sunshine, 4), 2) : 0;
                        $all_total_sunshine = !empty($all_total_sunshine) ? str_replace(",","",$all_total_sunshine) : 0;
                        $sheet->setCellValueExplicit('F' . $row, $all_total_sunshine,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        if(!empty($all_total_energy) && !empty($all_total_sunshine)){
                            $PR =  $all_total_energy/$all_total_sunshine/$total_capacity*100;
                            $PR  = number_format(round($PR, 4) , 2);
                        }else{
                            $PR  = 0;
                        }
                        $sheet->setCellValueExplicit('G' . $row, $PR,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $row++; // 在內層循環结束後增加行號
                    }
                }else{
                    $total_energy = 0;
                    $ex_total_energy =0;
                    $ex_sunshine =0;
                    $ex_sunshine_add_date =0;
                    for ($i = 0; $i < count($add_date_count); $i++) {
                        $date_day_hour = date("Y-m-d H:00:00", strtotime($add_date_count[$i]['judge_date']));
                        $sheet->setCellValue('A' . $row, $i+1);
                        $sheet->setCellValue('B' . $row, $date_day_hour);
                        foreach ($areas_list as $row_data) {
                            $sheet->setCellValue('C' . $row, $row_data['name']);
                            $sheet->setCellValue('D' . $row, $row_data['capacity']);
                        } 
                        // 發電量(kWh)資訊
                        $total_energy = Power_GenerationRows_d($db, $i, $date_day_hour, $area, $ex_total_energy);
                        $ex_total_energy = $total_energy[1];
                        $sheet->setCellValue('E' . $row, $total_energy[1]);
                        $total_sunshine = Cumulative_SunshineRows_d($db, $i, $date_day_hour, $area, $ex_sunshine, $ex_sunshine_add_date, $total_energy[0], $row_data['capacity']);
                        $ex_sunshine = $total_sunshine[1];
                        $ex_sunshine_add_date = $total_sunshine[2];
                        $all_total_sunshine = !empty($total_sunshine[0]) ? number_format(round($total_sunshine[0], 4), 2) : 0;
                        $all_total_sunshine = !empty($all_total_sunshine) ? str_replace(",","",$all_total_sunshine) : 0;
                        $PR = !empty($total_sunshine[3]) ? number_format(round($total_sunshine[3], 4), 2) : 0;
                        $sheet->setCellValueExplicit('F' . $row, $all_total_sunshine,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit('G' . $row, $PR,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $row++; // 在內層循環结束後增加行號
                    }
                }
            }else {
                echo '0';
            }
            break;
        case "2":
            if (count($areas_list) > 0) {
                $ex_total_energy = 0;
                $ex_sunshine =0;
                $ex_sunshine_add_date =0;
                $PR = 0;
                $all_total_sunshine = 0;
                if ($area == 'all') {
                    for ($i = 0; $i < $daysInMonth; $i++) {
                        $date_day_show = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($date_day)));
                        $time = '23:59:59'; // 特定时间
                        $date_day_calculate = date('Y-m-d H:i:s', strtotime($date_day . ' ' . $time . ' + ' . $i . ' day'));
                        $sheet->setCellValue('A' . $row, ($i+1));
                        $sheet->setCellValue('B' . $row, $date_day_show);
                        $sheet->setCellValue('C' . $row, '全部');
                        $sheet->setCellValue('D' . $row, $total_capacity);
                        // 發電量(kWh)資訊
                        $all_total_energy = 0;
                        for ($ii = 0; $ii < count($areas); $ii++) {
                            $total_energy = Power_GenerationRows_m($db,$i,$date_day_calculate, $ii);
                            $all_total_energy =$all_total_energy + $total_energy;
                        }
                        $sheet->setCellValue('E' . $row, $all_total_energy);
                        $all_total_sunshine = 0;
                        for ($ii = 0; $ii < count($areas); $ii++) {
                            $total_sunshine = Cumulative_SunshineRows_m($db,$i, $date_day_show, ($ii+1), $ex_sunshine, $ex_sunshine_add_date, $all_total_energy, $total_capacity);
                            $ex_sunshine = $total_sunshine[1];
                            $ex_sunshine_add_date = $total_sunshine[2];
                            $all_total_sunshine =$all_total_sunshine + $total_sunshine[0];
                        }
                        $all_total_sunshine = !empty($all_total_sunshine) ? number_format(round($all_total_sunshine, 4), 2) : 0;
                        $all_total_sunshine = !empty($all_total_sunshine) ? str_replace(",","",$all_total_sunshine) : 0;
                        $sheet->setCellValueExplicit('F' . $row, $all_total_sunshine,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        if(!empty($all_total_energy) && !empty($all_total_sunshine)){
                            $PR =  $all_total_energy/$all_total_sunshine/$total_capacity*100;
                            $PR  = number_format(round($PR, 4) , 2);
                        }else{
                            $PR=0;
                        }
                        $sheet->setCellValueExplicit('G' . $row, $PR,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $row++; // 在內層循環结束後增加行號
                    }
                    
                }else{
                    $ex_total_energy =0;
                    $ex_sunshine =0;
                    $ex_sunshine_add_date =0;
                    $all_total_sunshine = 0;
                    $PR =0;
                    for ($i = 0; $i < $daysInMonth; $i++) {
                        $date_day_show = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($date_day)));
                        $time = '23:59:59'; // 特定时间
                        $date_day_calculate = date('Y-m-d H:i:s', strtotime($date_day . ' ' . $time . ' + ' . $i . ' day'));
                        $sheet->setCellValue('A' . $row, ($i+1));
                        $sheet->setCellValue('B' . $row, $date_day_show);
                        foreach ($areas_list as $row_data) {
                            $sheet->setCellValue('C' . $row, $row_data['name']);
                            $sheet->setCellValue('D' . $row, $row_data['capacity']);
                        } 
                        // 發電量(kWh)資訊
                        $total_energy = Power_GenerationRows_m($db,$i,$date_day_calculate, $area);
                        $sheet->setCellValue('E' . $row, $total_energy);
                        $total_sunshine = Cumulative_SunshineRows_m($db,$i, $date_day_show, $area, $ex_sunshine, $ex_sunshine_add_date, $total_energy, $row_data['capacity']);
                        $ex_sunshine = $total_sunshine[1];
                        $ex_sunshine_add_date = $total_sunshine[2];
                        $all_total_sunshine = !empty($total_sunshine[0]) ? number_format(round($total_sunshine[0], 4), 2) : 0;
                        $all_total_sunshine = !empty($all_total_sunshine) ? str_replace(",","",$all_total_sunshine) : 0;
                        $PR = !empty($total_sunshine[3]) ? number_format(round($total_sunshine[3], 4), 2) : 0;
                        $sheet->setCellValueExplicit('F' . $row, $all_total_sunshine,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $sheet->setCellValueExplicit('G' . $row, $PR,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                        $row++; // 在內層循環结束後增加行號
                    }
                }
            }else {
                echo '0';
            }
            break;
        case "3":
            if (count($areas_list) > 0) {
                $ex_total_energy = 0;
                if ($area == 'all') {
                        $sheet->setCellValue('A' . $row, 1);
                        $sheet->setCellValue('B' . $row, $year);
                        $sheet->setCellValue('C' . $row, '全部');
                        $sheet->setCellValue('D' . $row, $total_capacity);
                        // 發電量(kWh)資訊
                        $all_total_energy = 0;
                        for ($ii = 0; $ii < count($areas); $ii++) {
                            $total_energy = Power_GenerationRows_y($db,$year,($ii+1));
                            $all_total_energy =$all_total_energy + $total_energy;
                        }
                        $sheet->setCellValue('E' . $row, $all_total_energy);
                        $ex_sunshine =0;
                        $ex_sunshine_add_date =0;
                        $all_total_sunshine =0;
                        $all_total_sunshine1= 0;
                        $daysInYear =date('z');
                        for ($ii = 0; $ii < count($areas); $ii++) {
                            for ($i = 0; $i < $daysInYear; $i++) {
                                $startDateTime = $year.'-01-01';
                                $time = '23:59:59'; // 特定时间
                                $date_day_show = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($startDateTime)));
                                $total_sunshine = Cumulative_SunshineRows_y($db,$i, $date_day_show, ($ii+1), $ex_sunshine, $ex_sunshine_add_date, $total_energy, $total_capacity);
                                $ex_sunshine = $total_sunshine[1];
                                $ex_sunshine_add_date = $total_sunshine[2];
                                $all_total_sunshine =$all_total_sunshine + $total_sunshine[0];
                            }
                        }
                        $all_total_sunshine1 = $all_total_sunshine1 + $all_total_sunshine;
                        $all_total_sunshine1 = !empty($all_total_sunshine1) ? number_format(round($all_total_sunshine1, 4), 2) : 0;
                        $all_total_sunshine1 = !empty($all_total_sunshine1) ? str_replace(",","",$all_total_sunshine1) : 0;
                        $sheet->setCellValueExplicit('F' . $row, $all_total_sunshine1,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $PR = 0;
                        if(!empty($all_total_energy) && !empty($all_total_sunshine1)){
                            $PR = ((int)$all_total_energy/(int)$daysInYear/(float)$total_capacity/(float)$all_total_sunshine1)*100;
                            $PR  = number_format(round($PR, 4) , 2);
                            $PR = $PR!=0 ? $PR : 0;
                        }
                        $sheet->setCellValueExplicit('G' . $row, $PR,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                        $row++; // 在在內層循環结束後增加行號
                    
                }else{
                        $sheet->setCellValue('A' . $row, '1');
                        $sheet->setCellValue('B' . $row, $year);
                        foreach ($areas_list as $row_data) {
                            $sheet->setCellValue('C' . $row, $row_data['name']);
                            $sheet->setCellValue('D' . $row, $row_data['capacity']);
                        } 
                        // 發電量(kWh)資訊
                        $total_energy = Power_GenerationRows_y($db,$year,$area);
                        $sheet->setCellValue('E' . $row, $total_energy);
                        $ex_sunshine =0;
                        $ex_sunshine_add_date =0;
                        $all_total_sunshine =0;
                        $daysInYear =date('z');
                        for ($i = 0; $i < $daysInYear; $i++) {
                            $startDateTime = $year.'-01-01';
                            $time = '23:59:59'; // 特定时间
                            $date_day_show = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($startDateTime)));
                            $total_sunshine = Cumulative_SunshineRows_y($db,$i, $date_day_show, $area, $ex_sunshine, $ex_sunshine_add_date, $total_energy, $row_data['capacity']);
                            $ex_sunshine = $total_sunshine[1];
                            $ex_sunshine_add_date = $total_sunshine[2];
                            $all_total_sunshine =$all_total_sunshine + $total_sunshine[0];
                        }
                        $all_total_sunshine = !empty($all_total_sunshine) ? number_format(round($all_total_sunshine, 4), 2) : 0;
                        $all_total_sunshine = !empty($all_total_sunshine) ? str_replace(",","",$all_total_sunshine) : 0;
                        $sheet->setCellValueExplicit('F' . $row, $all_total_sunshine,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        $PR = 0;

                        if(!empty($total_energy) && !empty($all_total_sunshine)){
                            $PR = ($total_energy/$daysInYear/$row_data['capacity']/$all_total_sunshine)*100;
                            $PR  = number_format(round($PR, 4) , 2);
                            $PR = $PR!=0 ? $PR : 0;
                        }
                        $sheet->setCellValueExplicit('G' . $row, $PR,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                        $row++; // 在內層循環结束後增加行號
                }
            }else {
                echo '0';
            }
            break;
        default:
            echo '0';
    }
    // 保存 Excel 文件
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    //$writer->save('./example.xlsx');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="統計報表.csv"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
    $writer->setUseBOM(true);
    $writer->save('php://output');
}

// 调用函数
exceldownload();

?>