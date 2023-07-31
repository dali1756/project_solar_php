<?php
    // session_start();
    // if (!isset($_SESSION["login_user"])) {
    //     header("location: login.php");
    //     exit;
    // }

    include("head.php");
    
    function get_inverter($db, $id = null) {
        if ($id === null) {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' AND s.area_id = 3";
            $stmt = $db->prepare($sql);
        } else {
            $sql = "SELECT s.*, i.* FROM solar_energy.invertor i LEFT JOIN solar_energy.sensor s ON i.id = s.sensor_type_id WHERE s.sensor_type = 'invertor' AND s.area_id = 3 AND i.id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_area($db, $id) {
        $sql = "SELECT name FROM solar_energy.area WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result["name"] : "";
    }

    $selected = isset($_GET["inverter"]) ? (int)$_GET["inverter"] : 0;
    $inverters = [];
    if ($selected > 0) {
        $inverters = get_inverter($db, $selected);
    } else {
        $inverters = get_inverter($db);
    }

    $area_id = 3;
    $area_name = get_area($db, $area_id);
?>

<div class = "content">
    <div class = "container">
        <div class = "page-title">
            <h3><?php echo $area_name; ?></h3>
        </div>
        <div class = "col-lg-12">
            <div class = "row">
                <div class = "col-md-12 table-bordered">
                    <div class = "card">
                        <div class = "content">
                            <form class = "needs-validation" novalidate accept-charset = "utf-8">
                                <div class = "row">
                                    <label class = "col-sm-2 form-label lb-title" for = "變流器">選擇變流器</label>
                                    <div class = "col-sm-10">
                                        <select name = "inverter" class = "form-select" required onchange="this.form.submit()">
                                            <option value = "" <?php echo $selected == 0 ? "selected" : ""; ?>>顯示全部</option>
                                            <?php
                                                $all_inverters = get_inverter($db);
                                                for ($i = 0; $i < count($all_inverters); $i++) {
                                                    $selected_attr = ($all_inverters[$i]["id"] == $selected) ? "selected" : "";
                                                    $inverter_name = $all_inverters[$i]["name"];
                                                    echo "<option value = \"{$all_inverters[$i]["id"]}\" {$selected_attr}>{$inverter_name}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class = "row">
                <?php for($i = 0; $i < count($inverters); $i++): ?>
                <div class = "col-md-3 table-bordered">
                    <table class = "table">
                        <tr class = "box1">
                            <td colspan = "4"><?php echo $inverters[$i]["name"]; ?></td>
                        </tr>
                        <tr>
                            <td colspan = "4" class = "title2">太陽能端</td>
                        </tr>
                        <tr>
                            <td class = "title3">PV輸入電壓</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["pv_input_voltage"] >= -300 && $inverters[$i]["pv_input_voltage"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["pv_input_voltage"]; ?>
                            </td>
                            <td>V</td>
                        </tr>
                        <tr>
                            <td class = "title3">PV輸入功率</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["pv_input_power"] >= -300 && $inverters[$i]["pv_input_power"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["pv_input_power"]; ?>
                            </td>
                            <td>KW</td>
                        </tr>
                        <tr>
                            <td colspan = "4" class = "title2">市電端</td>
                        </tr>
                        <tr>
                            <td class = "title3">AC輸入電壓</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["ac_input_voltage"] >= -300 && $inverters[$i]["ac_input_voltage"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["ac_input_voltage"]; ?>
                            </td>
                            <td>V</td>
                        </tr>
                        <tr>
                            <td class = "title3">AC輸入功率</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["ac_input_frequency"] >= -300 && $inverters[$i]["ac_input_frequency"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["ac_input_frequency"]; ?>
                            </td>
                            <td>Hz</td>
                        </tr>
                        <tr>
                            <td colspan = "4" class = "title2">負載端</td>
                        </tr>
                        <tr>
                            <td class = "title3">AC輸出電壓</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["ac_output_voltage"] >= -300 && $inverters[$i]["ac_output_voltage"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["ac_output_voltage"]; ?>
                            </td>
                            <td>V</td>
                        </tr>
                        <tr>
                            <td class = "title3">AC輸出功率</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["ac_output_power"] >= -300 && $inverters[$i]["ac_output_power"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["ac_output_power"]; ?>
                            </td>
                            <td>Kw</td>
                        </tr>
                        <tr>
                            <td class = "title3">AC輸出頻率</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["ac_output_frequency"] >= -300 && $inverters[$i]["ac_output_frequency"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["ac_output_frequency"]; ?>
                            </td>
                            <td>Hz</td>
                        </tr>
                        <tr>
                            <td class = "title3">AC輸出百分比</td>
                            <td>:</td>
                            <td class = "<?php echo ($inverters[$i]["ac_output_percent"] >= -300 && $inverters[$i]["ac_output_percent"] <= 100) ? "warning" : "text-end"; ?>">
                                <?php echo $inverters[$i]["ac_output_percent"]; ?>
                            </td>
                            <td>%</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <?php if(($i + 1) % 50 == 0): ?>
            </div>
            <?php endif; ?>
            <?php endfor; ?>           
        </div>
    </div>
</div>
<?php include('footer.php'); ?>