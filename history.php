<?php 
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  // 需經過帳密.機器人驗證才能進入頁面
  // session_start();
  // if (!isset($_SESSION["login_user"])) {
  //   header("location: login.php");
  //   exit;
  // }

  include('head.php'); 

  $area = isset($_GET["area"]) ? $_GET["area"] : null;
  $invertor_id = isset($_GET["invertor"]) ? $_GET["invertor"] : null;
  $date = isset($_GET["date"]) ? $_GET["date"] : null;

  // 查詢結果
  $sql = "SELECT DISTINCT add_date, pv_input_power FROM invertor_log WHERE invertor_id = ? AND DATE(add_date) = ? ORDER BY add_date ASC";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(1, $invertor_id, PDO::PARAM_STR);
  $stmt->bindParam(2, $date, PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $hourly_log = array();

  foreach ($result as $row) {
    $hour = date("H", strtotime($row["add_date"]));
    if (!isset($hourly_log[$hour])) {
      $hourly_log[$hour] = array(
        "datetime" => $date. " ". $hour. ":00:00",
        "power_sum" => 0,
        "records" => array(),
      );
    }
    $hourly_log[$hour]["power_sum"] = $hourly_log[$hour]["power_sum"] + $row["pv_input_power"];
    $hourly_log[$hour]["records"][] = $row;
  }

  foreach ($hourly_log as $hour => $log) {
    usort($log["records"], function ($a, $b) {
      return strtotime($a["add_date"]) - strtotime($b["add_date"]);
    });
  }

  // 區域
  $sql_area = "SELECT id, name FROM area";
  $stmt = $db->prepare($sql_area);
  $stmt->execute();
  $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 變流器名稱
  $sql_log = "SELECT DISTINCT s.sensor_type_id, s.name FROM sensor s WHERE s.sensor_type = 'invertor' AND s.area_id = :area_id";
  $stmt = $db->prepare($sql_log);
  $stmt->bindParam(":area_id", $area, PDO::PARAM_INT);
  $stmt->execute();
  $invertors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style type="text/css">

</style>


		
<div class="content">
    <div class="container">
        <div class="page-title">
            <h3>歷史記錄 </h3>
        </div>       

        <div class="col-lg-12">
            <div class="card-body">
                <form action="history.php">
                    <div class="">
                    <div class="row ">
                    <div class="col-md-4 mb-3 ">
                       
                       <label for="area" class="form-label">區域</label>
                       <select name="area" id = "area" class="form-select" onchange="this.form.submit()" required>
                           <option value="" selected>請選擇...</option>
                           <?php
                            foreach ($areas as $area_row) {
                              $selected = $area == $area_row["id"] ? "selected" : "";
                              echo "<option value='{$area_row['id']}' $selected>{$area_row['name']}</option>";
                            }
                           ?>
                       </select>
                   </div>
                      <div class="col-md-4 mb-3 ">
                       
                            <label for="invertor" class="form-label">變流器名稱</label>
                            <select name="invertor" class="form-select" required>
                                <option value="" selected>請選擇...</option>
                                <?php
                                  foreach ($invertors as $invertor_row) {
                                    $selected = $invertor_id == $invertor_row["sensor_type_id"] ? "selected" : "";
                                    echo "<option value = '{$invertor_row['sensor_type_id']}' $selected>{$invertor_row['name']}</option>";
                                  }
                                ?>
                            </select>
                        </div>
                      
                        <div class="col-md-4 mb-3 ">
                            <label for="date" class="form-label"> 查詢日期 </label>
                            <input name = "date" type="text" class="form-control datepicker-here" data-language="zh"
                                aria-describedby="datepicker" placeholder="請選擇日期" required value = "<?= $date ?>">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type='submit' onclick="validateForm()"
                            class='btn btn-primary action_btn'><i class="fas fa-search"></i> 查詢</button>
                    </div>
                    </div>
                </form>
            </div>
            <?php if ($area && $invertor_id && $date) : ?>
            <div id="display" class="">
                <div class="page-title">
                    <h3>查詢結果 
                        
                    </h3>
                </div>
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table-responsive history">

                        <table  width="100%" class="table">
                        <tr>
                                        <th>#</th>
                                        <th>日期/時間</th>
                                        <th>輸入電量(Kw)</th>
                                        <th>輸入功率(Kw)</th>
                                        <th>詳細資訊</th>
                                    </tr>     
                                    <?php
                                      $count = 1;
                                      // foreach ($hourly_log as $hour => $log) {
                                      foreach ($hourly_log as $log) {
                                    ?>   
  <tr>
    <td><?php echo $count; ?></td>
    <td><?php echo $log["datetime"]; ?></td>
    <td><?php echo $log["power_sum"]; ?></td>
    <td>111</td>
    <td><a class="showmore teal"><i class="fas fa-plus-square  "></i>  More</a></td>
  </tr>
  <tr class="detail">
    <td colspan="5">
      <div class="history">
        <table class="table  detail-bg ">
          <?php
            foreach ($log["records"] as $record):
          ?>
          <tr>
          <td></td>
             
            <td ><?php echo $record["add_date"]; ?></td>
            <td ><?php echo $record["pv_input_power"]; ?></td>   
            <td >111</td>
            <td></td>          
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </td>
  </tr>
  <?php $count++; } ?>
  <tr>
    <?php endif; ?>
  </tr>
</table>
                         
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

		
</div>
</div>
<script>
$(function() {
  $('a.showmore').click(function(e) {
    e.preventDefault();
    // We break and store the result so we can use it to hide
    // the row after the slideToggle is closed
    var targetrow = $(this).closest('tr').next('.detail');
    targetrow.show().find('div').slideToggle('slow', function(){
      if (!$(this).is(':visible')) {
        targetrow.hide();
      }
    });
  });
});
</script>
<?php include('footer.php'); ?>