<?php
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

<style>
   
.invertor{
    width: 100%;
    overflow:auto;
    height:500px;  /* 设置固定高度 */
    padding: 0;
    margin: 0;
}
.invertor td, th {
    /* 设置td,th宽度高度 */
    border:1px solid gray;
    width:150px;
    height:30px;
    text-align: center;
    border: 1px solid #333;
}
.invertor th {
    background-color:lightblue;
}
.invertor table {
    table-layout: fixed;
    width: 200px; /* 固定宽度 */
}
.invertor td:first-child, th:first-child {
    position:sticky;
    left:0; /* 首行永远固定在左侧 */
    z-index:1;
    background-color:lightpink;
    text-align: left;
    padding-left: 10px;
}
.invertor thead tr th {
    position:sticky;
    top:0; /* 列首永远固定在头部  */
    
}
.invertor th:first-child{
    z-index:2;
    background-color:lightblue;
}
  </style>


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
               
   <div> <i class="fas fa-circle white"></i>斷線 <i class=" fas fa-circle green"></i> 正常<i class=" fas fa-circle orange"></i> 告警<i class=" fas fa-circle red"></i> 錯誤<i class=" fas fa-circle pink"></i> 狀態異常</div>
        <div class="invertor">
            
            <table>
                <thead>
                    <tr>
                        <th>變流器編號</th>
                        <th><i class=" fas fa-circle green"></i>A001</th>
                        <th><i class=" fas fa-circle green"></i>A002</th>
                        <th><i class=" fas fa-circle green"></i>A003</th>
                        <th><i class=" fas fa-circle green"></i>A004</th>
                        <th><i class=" fas fa-circle green"></i>A005</th>
                        <th><i class=" fas fa-circle green"></i>A006</th>                       
                    </tr>
                </thead>
                <tbody>
                    <tr >
                        <td>廠牌型號 </td>
                        <td>PV-75000T-U</td>
                        <td>PV-75000T-U</td>
                        <td>PV-75000T-U</td>
                        <td>PV-75000T-U</td>
                        <td>PV-75000T-U</td>
                        <td>PV-75000T-U</td>
                     
                    </tr>
                    <tr >
                        <td>今日發電量KWh </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>總發電量KWh </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>即時發電量kW </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>轉換效益% </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>內部溫度&#176;C </td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                       <td>0</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>1串直流電壓V </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>1串直流電流A </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr>  <tr >
                        <td>1串輸入功率kW </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td>
                     
                    </tr> 
                     <tr >
                        <td>2串直流電壓V </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>2串直流電流A </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>2串輸入功率kW </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>3串直流電壓V </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>3串直流電流A </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>3串輸入功率kW </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>4串直流電壓V </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>4串直流電流A </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>4串輸入功率kW </td>
                        <td>224</td>
                        <td>8.2</td>
                        <td>60</td>
                       <td>224</td>
                       <td>8.4</td>
                        <td>0</td> 
                    </tr>
                    <tr >
                        <td>L1交流電壓V</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L1交流電流A</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L1交流功率kW</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L1交流頻率Hz</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L2交流電壓V</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L2交流電流A</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L2交流功率kW</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L2交流頻率Hz</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L3交流電壓V</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L3交流電流A</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L3交流功率kW</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                    <tr >
                        <td>L3交流頻率Hz</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td> 
                    </tr>
                </tbody>
            </table>
       
            
            
        </div>   
            </div>
                 
        </div>
    </div>
</div>
<?php include('footer.php'); ?>