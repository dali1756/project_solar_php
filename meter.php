<?php 
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    header("Refresh: 300");
    include("head.php");

    // function get_dc($db, $area_id) {
    //     $sql_dc = "SELECT s.sensor_type_id, COUNT(*), SUM(dc.Voltage) AS voltage, SUM(dc.Current) AS current, SUM(dc.Energy) AS energy, SUM(dc.Power) AS power
    //                FROM dc_electricity_meter dc JOIN sensor s ON dc.id = s.sensor_type_id JOIN area ON s.area_id = :area_id
    //                WHERE s.sensor_type = 'dc_electricity_meter' GROUP BY s.sensor_type_id, area.id";
    //     $stmt = $db->prepare($sql_dc);
    //     $stmt->bindParam(":area_id", $area_id);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return $result;
    // }
    // function get_ac($db, $area_id) {
    //     $sql_ac = "SELECT s.sensor_type_id, COUNT(*), SUM(ac.ULN_AVG) AS ULN_AVG, SUM(ac.ULL_AVG) AS ULL_AVG, SUM(ac.I_AVG) AS I_AVG, SUM(ac.PSUM) as PSUM
    //                FROM ac_electricity_meter ac JOIN sensor s ON ac.id = s.sensor_type_id JOIN area ON s.area_id = :area_id
    //                WHERE s.sensor_type = 'ac_electricity_meter' GROUP BY s.sensor_type_id, area.id";
    //     $stmt = $db->prepare($sql_ac);
    //     $stmt->bindParam(":area_id", $area_id);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return $result;
    // }
    // $dc_1 = get_dc($db, 1);
    // $ac_1 = get_ac($db, 1);
    // $dc_2 = get_dc($db, 3);
    // $ac_2 = get_ac($db, 3);

    // // 區域名稱
    // function get_area($db, $id) {
    //     $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
    //     $stmt = $db->prepare($sql);
    //     $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return $result ? $result["name"] : "";
    // }
    // $twoArea = get_area($db, 1). " & ". get_area($db, 2);
    // $Area = get_area($db, 3);


    class ElectricityMeter {
        private $db;
        public function __construct($db) {
            $this->db = $db;
        }
        public function getDC($area_id) {
            $sql_dc = "SELECT s.sensor_type_id, COUNT(*), SUM(dc.Voltage) AS voltage, SUM(dc.Current) AS current, SUM(dc.Energy) AS energy, SUM(dc.Power) AS power
                       FROM dc_electricity_meter dc JOIN sensor s ON dc.id = s.sensor_type_id JOIN area ON s.area_id = :area_id
                       WHERE s.sensor_type = 'dc_electricity_meter' GROUP BY s.sensor_type_id, area.id";
            $stmt = $this->db->prepare($sql_dc);
            $stmt->bindParam(":area_id", $area_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        public function getAC($area_id) {
            $sql_ac = "SELECT s.sensor_type_id, COUNT(*), SUM(ac.ULN_AVG) AS ULN_AVG, SUM(ac.ULL_AVG) AS ULL_AVG, SUM(ac.I_AVG) AS I_AVG, SUM(ac.PSUM) as PSUM
                       FROM ac_electricity_meter ac JOIN sensor s ON ac.id = s.sensor_type_id JOIN area ON s.area_id = :area_id
                       WHERE s.sensor_type = 'ac_electricity_meter' GROUP BY s.sensor_type_id, area.id";
            $stmt = $this->db->prepare($sql_ac);
            $stmt->bindParam(":area_id", $area_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        public function getArea($id) {
            $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result["name"] : "";
        }
    }
    $meter = new ElectricityMeter($db);
    $dc_1 = $meter->getDC(1);
    $ac_1 = $meter->getAC(1);
    $dc_2 = $meter->getDC(3);
    $ac_2 = $meter->getAC(3);

    $area_id_1 = 1;
    $area_id_2 = 2;
    $area_id_3 = 3;
    $area_name_1 = $meter->getArea($area_id_1);
    $area_name_2 = $meter->getArea($area_id_2);
    $area_name_3 = $meter->getArea($area_id_3);
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
                        </span><?php echo isset($dc_1["voltage"]) ? number_format($dc_1["voltage"], 2) : ""; ?>  V<br>
                    電流：<span class = "">
                        </span><?php echo isset($dc_1["current"]) ? number_format($dc_1["current"], 2) : ""; ?> A</div>
                <div class="meter-img"><img src="assets/img/meter2.svg"></div>
                <table class=" table table-bordered">
                    <tr class="box2">
                        <td colspan="4"><?php echo $twoArea; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="title2b">電錶1-直流電DC</td>
                    </tr>
                    <tr>
                        <td class="title3">即時發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($dc_1["power"]) ? number_format($dc_1["power"], 2) : ""; ?>
                        </td>
                        <td> kW</td>
                    </tr>
                    <tr>
                        <td class="title3">累積發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($dc_1["energy"]) ? number_format($dc_1["energy"], 2) : ""; ?>
                        </td>
                        <td> kWh</td>
                    </tr>

                    <tr class="">
                        <td colspan="4" class="title2b">電錶2-交流電AC</td>
                    </tr>
                    <tr>
                        <td class="title3"> 平均相電壓</td>
</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($ac_1["ULN_AVG"]) ? number_format($ac_1["ULN_AVG"] * 0.1, 2) : ""; ?>
                        </td>
                        <td> V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均線電壓</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($ac_1["ULL_AVG"]) ? number_format($ac_1["ULL_AVG"] * 0.1, 2) : ""; ?>
                        </td>
                        <td> V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均電流</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($ac_1["I_AVG"]) ? number_format($ac_1["I_AVG"] * 0.001, 2) : ""; ?>
                        </td>
                        <td> A</td>
                    </tr>
                    <tr>
                        <td class="title3">總有效功率</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($ac_1["PSUM"]) ? number_format($ac_1["PSUM"] / 1000, 2) : ""; ?>
                        </td>
                        <td> kW</td>
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
                        <?php echo isset($dc_2["voltage"]) ? number_format($dc_2["voltage"], 2) : ""; ?>
                    </span>V<br>
                    電流：<span class="">
                        <?php echo isset($dc_2["current"]) ? number_format($dc_2["current"], 2) : ""; ?>
                    </span>A</div>
                <div class="meter-img"><img src="assets/img/meter2.svg"></div>
                <table class="  table table-bordered">
                    <tr class="box2">
                        <td colspan="4"><?php echo $Area; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="title2b">電錶1-直流電DC</td>
                    </tr>
                    <tr>
                        <td class="title3">即時發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($dc_2["power"]) ? number_format($dc_2["power"], 2) : ""; ?>
                        </td>
                        <td>kW</td>
                    </tr>
                    <tr>
                        <td class="title3">累積發電量</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($dc_2["energy"])? number_format($dc_2["energy"], 2) : ""; ?>
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
                            <?php echo isset($ac_2["ULN_AVG"]) ? number_format($ac_2["ULN_AVG"] * 0.1, 2) : ""; ?>
                        </td>
                        <td> V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均線電壓</td>
                        <td>：</td>
                        <td  class="text-end">
                            <?php echo isset($ac_2["ULL_AVG"]) ? number_format($ac_2["ULL_AVG"] * 0.1, 2) : ""; ?>
                        </td>
                        <td> V</td>
                    </tr>
                    <tr>
                        <td class="title3">平均電流</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($ac_2["I_AVG"])? number_format($ac_2["I_AVG"] * 0.001, 2) : ""; ?>
                        </td>
                        <td> A</td>
                    </tr>
                    <tr>
                        <td class="title3">總有效功率</td>
                        <td>：</td>
                        <td class="text-end">
                            <?php echo isset($ac_2["PSUM"]) ? number_format($ac_2["PSUM"] / 1000, 2) : ""; ?>
                        </td>
                        <td> kW</td>
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
