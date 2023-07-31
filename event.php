<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$areaId = isset($_POST["areaId"]) ? $_POST["areaId"] : null;
if(isset($areaId)){
    include_once('db.php');
    $sql_log = "SELECT s.sensor_type_id, s.name 
                FROM sensor s 
                INNER JOIN area a ON s.area_id = a.id 
                WHERE s.sensor_type = 'invertor' AND a.id = :area_id";
    $stmt = $db->prepare($sql_log);
    $stmt->bindParam(":area_id", $areaId, PDO::PARAM_INT);
    $stmt->execute();
    $invertors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($invertors);die();
     // 將結果轉換為 JSON 格式
    $json = json_encode($invertors);
    // 指定返回的是 JSON 数据
    header('Content-Type: application/json');
    // 输出 JSON 数据
    echo $json;
    $_SESSION['invertors'] = $invertors;
    // 终止脚本执行
    exit;
}
include('head.php');
$area = isset($_POST["area"]) ? $_POST["area"] : null;
$invertor_id = isset($_POST["invertor"]) ? $_POST["invertor"] : null;
$year = isset($_POST["year"]) ? $_POST["year"] : null;
$month = isset($_POST["month"]) ? $_POST["month"] : null;
$areaId = isset($_POST["areaId"]) ? $_POST["areaId"] : null;
$date_day = $year . '-' . $month;
// 區域
$sql_area = "SELECT id, name FROM area";
$stmt = $db->prepare($sql_area);
$stmt->execute();
$areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 故障查詢
$sql_fault = "SELECT fi.*, s.name AS sensor_name
              FROM fault_inquiry fi
              LEFT JOIN sensor s ON fi.invertor_id = s.sensor_type_id
              WHERE s.sensor_type_id = :invertor_id
              AND s.sensor_type = 'invertor'
              AND YEAR(fi.fault_date) = :year
              AND MONTH(fi.fault_date) = :month";
$stmt_list = $db->prepare($sql_fault);
$stmt_list->bindParam(':invertor_id', $invertor_id, PDO::PARAM_INT);
$stmt_list->bindParam(':year', $year, PDO::PARAM_INT);
$stmt_list->bindParam(':month', $month, PDO::PARAM_INT);
$stmt_list->execute();
$fault = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>故障查詢</h3>
        </div>
        <div class="col-lg-12">
            <div class="card-body">
                <form method="post" action="event.php">
                    <div class="">
                        <div class="row ">
                            <div class="col-md-3 mb-3">
                                <label for="area" class="form-label">區域</label>
                                <select id="area" name="area" class="form-select" required onchange="updateInvertorOptions()">
                                    <option value="" selected>請選擇...</option>
                                    <?php
                                    foreach ($areas as $area_row) {
                                        $selected = $area == $area_row["id"] ? "selected" : "";
                                        echo "<option value='{$area_row['id']}' $selected>{$area_row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="invertor" class="form-label">變流器名稱</label>
                                <select id="invertor" name="invertor" class="form-select" required>
                                    <?php if(isset($_SESSION['invertors'])) {
                                        $invertors = $_SESSION['invertors'];
                                        ?>
                                        <option value="" >請選擇...</option>
                                    <?php
                                        // 动态生成变流器选项
                                        foreach ($invertors as $invertor) {
                                            $selected = $invertor_id == $invertor["sensor_type_id"] ? "selected" : "";
                                            echo "<option value='{$invertor['sensor_type_id']}' $selected >{$invertor['name']}</option>";
                                        }
                                    ?>
                                    <?php }else{?>
                                        <option value="" selected>請選擇...</option>
                                    <?php }?>
                                    
                                </select>
                            </div>
                            <div class="col-md-3 mb-3 ">
                                <label for="year" class="form-label"> 年份 </label>
                                    <select name="year" class="form-select" required>
                                        <option value="" selected>請選擇...</option>
                                        <?php
                                        $currentYear = date('Y');
                                        $startYear = 2023;  
                                        for ($selectedYear = $startYear; $selectedYear <= $currentYear; $selectedYear++) {
                                            $selected = ($selectedYear == $year) ? 'selected' : ''; // 检查是否为所选年份
                                            echo '<option value="' . $selectedYear . '" ' . $selected . '>' . $selectedYear . '</option>';
                                        }
                                        ?>
                                    </select>
                            </div>
                            <div class="col-md-3 mb-3 ">
                                <label for="month" class="form-label"> 月份 </label>
                                <select name="month"  class="form-select" required>
                                    <option value="" selected>請選擇...</option>
                                    <?php
                                      for ($selectedMonth = 1; $selectedMonth <= 12; $selectedMonth++) {
                                        $selected = ($selectedMonth == $month) ? 'selected' : ''; // 检查是否为所选年份
                                        echo '<option value="' . $selectedMonth . '" ' . $selected . '>' . $selectedMonth . '</option>';
                                      }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type='submit' class='btn btn-primary action_btn' id='search-btn'><i class="fas fa-search"></i> 查詢</button>
                        </div>
                    </div>
                </form>
                <?php if(!empty($area) && !empty($invertor_id) && !empty($year) && !empty($month) && !empty(count($fault))){?>
                <div class="page-title">
                    <h3>查詢結果 </h3>
                </div>
                <div class="box box-primary">
                    <div class="box-body">
                    <div class="text-end">
                      <button type='button' onclick="callPHPFunction()" class='btn btn-primary action_btn'><i class="fas fa-download"></i> 匯出</button> 
                    </div>
                        <div class="table-responsive ">
                            <div class="event">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>變流器名稱</th>
                                            <th>發生時間</th>
                                            <th>錯誤類別 </th>
                                            <th>代碼</th>                                            
                                            <th>事件</th>
                                            <th>結束時間</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            foreach ($fault as $fault_row) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $fault_row['sensor_name']?>
                                            </td>
                                            <td>
                                                <?php echo $fault_row['fault_date']?>
                                            </td>
                                            <td><?php echo $fault_row['fault_class']?></td>  
                                            <td><?php echo $fault_row['bit']?></td>                                            
                                            <td>
                                                <?php echo $fault_row['event']?>
                                            </td>  
                                            <td><?php echo $fault_row['recover_date']?></td> 
                                        </tr>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php  
                    }elseif(!empty($area) && !empty($invertor_id) && !empty($year) && !empty($month) && empty(count($fault))){
                ?>
                    <div class="alert alert-danger" role="alert">查無資料，請重新查詢！</div>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<div id="waitloading" class="w-100 d-none"></div>
<script src="./assets/js/loading.js"></script>
<script>
    $('#search-btn').click(function() {
        // 顯示 LoadingMask
        var  value1 = $('[name="area"]').val();
        var  value2 = $('[name="invertor"]').val();
        var  value3 = $('[name="year"]').val();
        var  value4 = $('[name="month"]').val();
        if(value1 !== "" && value2 !== "" && value3 !== "" && value4 !== ""){
            // 顯示 LoadingMask
            LoadingMask('#waitloading');
        }
        
    });
    function updateInvertorOptions() {
    var areaId = document.getElementById("area").value;
    var invertorSelect = document.getElementById("invertor");
    if (areaId !== "") {
        // 创建 AJAX 请求对象
        var xhttp = new XMLHttpRequest();
        // 设置请求完成时的回调函数
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                var invertors = JSON.parse(this.responseText); // 解析返回的 JSON 数据
                // 清空原有选项
                invertorSelect.innerHTML = "";

                // 添加默认选项
                var defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.text = "請選擇...";
                invertorSelect.appendChild(defaultOption);
                // 动态生成变流器选项
                for (var i = 0; i < invertors.length; i++) {
                    var option = document.createElement("option");
                    option.value = invertors[i].sensor_type_id;
                    option.text = invertors[i].name;
                    invertorSelect.appendChild(option);
                }
            }
        };
        // 发送 AJAX 请求
        xhttp.open("POST", "", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("areaId=" + areaId);
    }
}
    function callPHPFunction() {
        var data = {
            value1: $('[name="invertor"]').val(),
            value2: $('[name="year"]').val(),
            value3: $('[name="month"]').val(),
        };

        $.ajax({
            url: 'exceldownload_event.php', // 將請求發送到的 PHP 文件路径
            method: 'POST',
            data: data,
            beforeSend:function(){LoadingMask('#waitloading')},
            complete:function(){ClearMask('#waitloading','')},
            success: function(response) {
                // 创建 Blob 对象
                var blob = new Blob([response], { type: 'text/csv' });
                // 创建数据链接
                var downloadLink = document.createElement('a');
                downloadLink.href = window.URL.createObjectURL(blob);
                downloadLink.download = '故障查詢.csv'; // 下载文件的名称

                // 添加到页面并模拟点击
                document.body.appendChild(downloadLink);
                downloadLink.click();

                // 清理数据链接对象
                window.URL.revokeObjectURL(downloadLink.href);
                document.body.removeChild(downloadLink);
            },
            error: function(xhr, status, error) {
                console.error(response);
            }
        });
    }
</script>
<?php include('footer.php'); ?>