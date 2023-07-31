<?php 
    // session_start();
    // if (!isset($_SESSION["login_user"])) {
    //     header("location: login.php");
    //     exit;
    // }
    include('head.php');

    $sql1 = "SELECT
            area1.name,
            electric2.accumulated_electricity AS accumulated_electricity,
            electric1.instantaneous_power AS instantaneous_power_1,
            electric2.instantaneous_power AS instantaneous_power_2,
            electric1.daily_input_power AS daily_input_power,
            electric1.voltage AS dc_voltage,
            electric1.electric_current AS dc_electric_current,
            electric2.voltage AS ac_voltage,
            electric2.electric_current AS ac_electric_current
            FROM invertor inver1 JOIN area area1 ON inver1.id BETWEEN 1 AND 11 AND area1.id = 1
            LEFT JOIN electricity_meter electric1 ON electric1.id = 1
            LEFT JOIN electricity_meter electric2 ON electric2.id = 2";

    $sql2 = "SELECT
            area2.name,
            electric4.accumulated_electricity AS accumulated_electricity,
            electric3.instantaneous_power AS instantaneous_power_3,
            electric4.instantaneous_power AS instantaneous_power_4,
            electric3.daily_input_power AS daily_input_power,
            electric3.voltage AS dc_voltage,
            electric3.electric_current AS dc_electric_current,
            electric4.voltage AS ac_voltage,
            electric4.electric_current AS ac_electric_current
            FROM invertor inver2 JOIN area area2 ON inver2.id BETWEEN 12 AND 32 AND area2.id = 2
            LEFT JOIN electricity_meter electric4 ON electric4.id = 4
            LEFT JOIN electricity_meter electric3 ON electric3.id = 3";

    $result1 = $db->query($sql1);
    $data1 = $result1->fetchAll(PDO::FETCH_ASSOC);
    $result2 = $db->query($sql2);
    $data2 = $result2->fetchAll(PDO::FETCH_ASSOC);

    // $sql1 維護廠房 備品儲區
    $area1_acc = round($data1[0]["accumulated_electricity"], 2);              // 累積用電量
    $area1_inst_1 = round($data1[0]["instantaneous_power_1"], 2);             // 即時發電
    $area1_inst_2 = round($data1[0]["instantaneous_power_2"], 2);             // 即時用電
    $area1_daily = round($data1[0]["daily_input_power"], 2);                  // 今日發電
    $area1_dc_voltage = round($data1[0]["dc_voltage"], 2);                    // 直流電壓
    $area1_dc_electric_current = round($data1[0]["dc_electric_current"], 2);  // 直流電流
    $area1_ac_voltage = round($data1[0]["ac_voltage"], 2);                    // 交流電壓
    $area1_ac_electric_current = round($data1[0]["ac_electric_current"], 2);  // 交流電流

    // sql2 太陽能發電廠
    $area2_acc = round($data2[0]["accumulated_electricity"], 2);
    $area2_inst_3 = round($data2[0]["instantaneous_power_3"], 2);
    $area2_inst_4 = round($data2[0]["instantaneous_power_4"], 2);
    $area2_daily = round($data2[0]["daily_input_power"], 2);
    $area2_dc_voltage = round($data2[0]["dc_voltage"], 2);
    $area2_dc_electric_current = round($data2[0]["dc_electric_current"], 2);
    $area2_ac_voltage = round($data2[0]["ac_voltage"], 2);
    $area2_ac_electricity_current = round($data2[0]["ac_electric_current"], 2);

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
    $area_names = $area_name_1. "&". $area_name_2;


?>

<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>廠區圖面 </h3>
        </div>

        <div class="row ">

            <div class="col-lg-12">
            <a href="pdf/唐榮第一期-審訖圖面.pdf" target="_blanK" ><div class="monitor6"><i class=" fas fa-map-marked-alt"></i><br> 唐榮第一期-審訖圖面</div></a>
             <a href="pdf/唐榮第二期-審訖圖面.pdf" target="_blanK" ><div class="monitor6"><i class=" fas fa-map-marked-alt"></i><br> 唐榮第二期-審訖圖面</div></a>

            </div>
        </div>
    </div>
</div>

</div>
<?php include('footer.php'); ?>