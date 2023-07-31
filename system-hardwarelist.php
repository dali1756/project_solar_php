<?php
    include('includes/header.php');
    include('includes/nav.php');
	include('includes/scripts.php');
		
	$sql = "SELECT dong FROM room GROUP BY dong";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$dong_arr = $rs->fetchAll();
	
//	$sql = "SELECT floor FROM room  GROUP BY floor= 'B1' desc , floor  Asc";
	$sql = "SELECT floor FROM room  GROUP BY floor";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$floor_arr = $rs->fetchAll();
	
	ksort($dong_arr);
	ksort($floor_arr);
?>

  <!-- Begin Page Content -->
  <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="mb-2 font-weight-bold">宿舍硬體系統檢測</h1>
          <!-- Search Card  -->
          <div class="row">
            <div class="col-lg-12">
              <!--
              <p class="text-lg text-center font-weight-bold NG-color">叮嚀：icon按鈕點擊後， 資料排序依據樓層跟房號</p>
              -->
              <!--<div class="card mb-4">-->
                <!--<div class="card-header text-center">檢視系統狀態</div>-->
                <div class="card-body">
                  <ul id="icon_nav_h" class="top_ico_nav clearfix nav nav-pills">
                      <li class="nav-item col-lg-2 offset-lg-1 col-sm-6 my-2">             
                        <a href="#ng" class="nav-link active" id="ng-tab" data-toggle="tab" role="tab"  aria-selected="false">
                          <i class="fas fa-exclamation-triangle fa-6x " role="tablist"></i>
                          <span class="menu_label">NG</span>
                        </a>
                      </li>
                      <li class="nav-item col-lg-2 col-sm-6 my-2">             
                        <a href="#reader" class="nav-link" id="reader-tab" data-toggle="tab" role="tab"  aria-selected="false">
                          <i class="fas fa-door-open fa-6x"></i>
                          <span class="menu_label">Reader</span>
                        </a>
                      </li>
                      <li class="nav-item col-lg-2 col-sm-6 my-2">
                        <a href="#meter" class="nav-link" id="meter-tab" data-toggle="tab" role="tab"  aria-selected="false">
                          <i class="fas fa-solar-panel fa-6x"></i>
                          <span class="menu_label">Meter</span>
                        </a>
                      </li>
                      <li class="nav-item col-lg-2 col-sm-6 my-2">             
                                  <a href="#power-meter" class="nav-link" id="power-meter-tab" data-toggle="tab" role="tab"  aria-selected="false">
                                    <i class="fas fa-tachometer-alt fa-6x"></i>
                                    <span class="menu_label">PowerMeter</span>
                                  </a>
                      </li>
                      <li class="nav-item col-lg-2 col-sm-6 my-2">             
                                  <a href="#meter-relay" class="nav-link" id="meter-relay-tab" data-toggle="tab" role="tab"  aria-selected="false">
                                    <i class="fas fa-power-off fa-6x"></i>
                                    <span class="menu_label">MeterRelay</span>
                                  </a>
                      </li>
                  </ul>
                </div>
              <!--</div>-->
              
            </div>

          </div>
          <!-- Search Card END -->

          <!--Table-->
		  
          <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="ng" role="tabpanel" aria-labelledby="ng-tab">
              
			  <!--NG Table-->
			  	<!--SEARCH 樓層-->
				<div class='col-12'>
						<form method="get">
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
									<div class='col-sm-8 form-inline'> 
										<select class='col form-control selectpicker custom-select-lg' size='1' name='ng_floor' id='ng_floor'>
										<option value=''>全部</option>
										<?php
											foreach($floor_arr as $v) {												
												echo "<option value='{$v['floor']}' {$select}>{$v['floor']}</option>";
											}
										?>
										</select>
									</div>
								</div>

							<br>
							<button type='button' onclick='ng_data_search()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</form>
				</div>
				<br>
				<!-- SEARCH 樓層 END-->
				
				<!-- 各樓NG顯示 -->
				<div id='ng_data_div'></div>
				<!--
				<div class="my-2">
					<h5 class="text-gray-900 font-weight-bold">Q/2F</h5>
					<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
				</div>
			  	<div class="row">
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2101：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter<br>Reader<br>PowerMeter<br>MeterRelay</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2102：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter<br>Reader<br>PowerMeter<br>MeterRelay</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2103：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter<br>MeterRelay</p>
                            </div>

                          </div>
                        </div>
                        
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2104：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter<br>MeterRelay</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2105：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2106：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>
				</div>

				<div class="my-2">
					<h5 class="text-gray-900 font-weight-bold">Q/3F</h5>
					<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
				</div>
			  	<div class="row">
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2101：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter<br>Reader<br>PowerMeter<br>MeterRelay</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2102：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter<br>Reader<br>PowerMeter<br>MeterRelay</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2103：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter<br>MeterRelay</p>
                            </div>

                          </div>
                        </div>
                        
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2104：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter<br>MeterRelay</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2105：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2106：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>
				</div>
				-->
    
				<!-- 各樓NG顯示 END-->
				
			  <!--
              <div class="table-responsive">
                          <table class="table  text-center font-weight-bold">
                            <thead class="thead-green">
                            <tr class="text-center">
                              <th scope="col">類型</th>
                              <th scope="col">房號</th> 
                              <th scope="col">狀態</th>
							  <th scope="col">目前度數</th>
                              <th scope="col">更新時間</th>
							  
                            </tr>
                            </thead>
                            <tbody><?php foreach($ng_table as $v) echo $v ?></tbody>
                          </table>
			  </div>
			  -->
              <!--NG Table END--->
              
            </div>

            <div class="tab-pane fade" id="meter" role="tabpanel" aria-labelledby="meter-tab">
              
			  <!--Meter Table--->
			  	<!--SEARCH 樓層-->
				  <div class='col-12'>
						<form method="get">
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
									<div class='col-sm-8 form-inline'> 
										<select class='col form-control selectpicker custom-select-lg' size='1' name='mt_floor' id='mt_floor'>
										<option value=''>全部</option>
										<?php
											foreach($floor_arr as $v) {												
												echo "<option value='{$v['floor']}' {$select}>{$v['floor']}</option>";
											}
										?>
										</select>
									</div>
								</div>

							<br>
							<button type='button' onclick='mt_data_search()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</form>
				</div>
				<br>
				<!-- SEARCH 樓層 END-->
				
				<!-- 各樓Meter顯示 -->
				<div id='mt_data_div'></div>
				<!--
				<div class="my-2">
					<h5 class="text-gray-900 font-weight-bold">Q/2F</h5>
					<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
				</div>
			  	<div class="row">
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2101：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2102：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2103：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter</p>
                            </div>

                          </div>
                        </div>
                        
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2104：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2105：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2106：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Meter</p>
                            </div>

                          </div>
                        </div>
				</div>
				-->
				<!-- 各樓Meter顯示 END-->			  

			  <!--
              <div class="my-2">
				<h5 class="text-gray-900">2F</h5>
                <h5 class="text-gray-900">更新時間:<?php echo $now_time ?></h5>
				
              </div>
              <div class="table-responsive">
                          <table class="table  text-center font-weight-bold">
                            <thead class="thead-green">
                            <tr class="text-center">
                              <th scope="col">類型</th>
                              <th scope="col">房號</th> 
                              <th scope="col">狀態</th>
							  <th scope="col">目前度數</th>
                              <th scope="col">更新時間</th>
                            </tr>
                            </thead>
                            <tbody><?php foreach($mt_table as $v) echo $v ?></tbody>
                          </table>
			  </div>
											-->
              <!--Meter Table END--->
                            
            </div>
            
            <div class="tab-pane fade" id="reader" role="tabpanel" aria-labelledby="reader-tab">

			  <!--Reader Table--->
			  	<!--SEARCH 樓層-->
				<div class='col-12'>
						<form method="get">
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
									<div class='col-sm-8 form-inline'> 
										<select class='col form-control selectpicker custom-select-lg' size='1' name='rd_floor' id='rd_floor'>
										<option value=''>全部</option>
										<?php
											foreach($floor_arr as $v) {												
												echo "<option value='{$v['floor']}' {$select}>{$v['floor']}</option>";
											}
										?>
										</select>
									</div>
								</div>

							<br>
							<button type='button' onclick='rd_data_search()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</form>
				</div>
				<br>
				<!-- SEARCH 樓層 END-->
				
				<!-- 各樓reader顯示 -->
				<div id='rd_data_div'></div>
				<!--
				<div class="my-2">
					<h5 class="text-gray-900 font-weight-bold">Q/2F</h5>
					<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
				</div>
			  	<div class="row">
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2101：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Reader</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2102：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Reader</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2103：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Reader</p>
                            </div>

                          </div>
                        </div>
                        
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2104：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Reader</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2105：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Reader</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2106：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">Reader</p>
                            </div>

                          </div>
                        </div>
				</div>
				-->
				<!-- 各樓reader顯示 END-->			  

			  <!--
              <div class="my-2">
                <h5 class="text-gray-900">更新時間:<?php echo $now_time ?></h5>
              </div>
              <div class="table-responsive">
                          <table class="table  text-center font-weight-bold">
                            <thead class="thead-green">
                            <tr class="text-center">
							  <th scope="col">類型</th>
                              <th scope="col">房號</th> 
                              <th scope="col">狀態</th>
							  <th scope="col">目前度數</th>
                              <th scope="col">更新時間</th>
                            </tr>
                            </thead>
                            <tbody><?php foreach($rd_table as $v) echo $v ?></tbody>
                          </table>
			  </div>
				-->
              <!--Reader Table END--->

            </div>


            <div class="tab-pane fade" id="meter-relay" role="tabpanel" aria-labelledby="meter-relay-tab">
			  <!--MeterRelay Table--->
			  	<!--SEARCH 樓層-->
                <div class='col-12'>
						<form method="get">
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
									<div class='col-sm-8 form-inline'> 
										<select class='col form-control selectpicker custom-select-lg' size='1' name='mr_floor' id='mr_floor'>
										<option value=''>全部</option>
										<?php
											foreach($floor_arr as $v) {												
												echo "<option value='{$v['floor']}' {$select}>{$v['floor']}</option>";
											}
										?>
										</select>
									</div>
								</div>

							<br>
							<button type='button' onclick='mr_data_search()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</form>
				</div>
				<br>
				<!-- SEARCH 樓層 END-->
				
				<!-- 各樓meter-relay顯示 -->
				<div id='mr_data_div'></div>
				<!--
				<div class="my-2">
					<h5 class="text-gray-900 font-weight-bold">Q/2F</h5>
					<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
				</div>
			  	<div class="row">
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2101：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2102：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2103：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>
                        
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2104：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2105：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2106：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">MeterRelay</p>
                            </div>

                          </div>
                        </div>
				</div>
				-->
				<!-- 各樓meter-relay顯示 END-->




			  <!--
              <div class="my-2">
                <h5 class="text-gray-900">更新時間:<?php echo $now_time ?></h5>
              </div>
              <div class="table-responsive">
                          <table class="table  text-center font-weight-bold">
                            <thead class="thead-green">
                            <tr class="text-center">
							  <th scope="col">類型</th>
                              <th scope="col">房號</th> 
                              <th scope="col">狀態</th>
							  <th scope="col">目前度數</th>
                              <th scope="col">更新時間</th>
                            </tr>
                            </thead>
                            <tbody><?php foreach($mtrd_table as $v) echo $v ?></tbody>
                          </table>
			  </div>
				-->
              <!--MeterRelay Table END--->
			</div>
			
            <div class="tab-pane fade" id="power-meter" role="tabpanel" aria-labelledby="power-meter-tab">
			
			  <!--PowerMeter Table--->
			  	<!--SEARCH 樓層-->
                  <div class='col-12'>
						<form method="get">
								<div class='form-group row'>
									<label for='exampleFormControlInput1' class='col-sm-2 col-form-label label-right pd-top25'>樓層</label>
									<div class='col-sm-8 form-inline'> 
										<select class='col form-control selectpicker custom-select-lg' size='1' name='pm_floor' id='pm_floor'>
										<option value=''>全部</option>
										<?php
											foreach($floor_arr as $v) {												
												echo "<option value='{$v['floor']}' {$select}>{$v['floor']}</option>";
											}
										?>
										</select>
									</div>
								</div>

							<br>
							<button type='button' onclick='pm_data_search()' class='btn btnfont-30 btn-primary2 text-white col-sm-4 offset-sm-4'>查詢</button>
						</form>
				</div>
				<br>
				<!-- SEARCH 樓層 END-->
				
				<!-- 各樓power-meter顯示 -->
				<div id='pm_data_div'></div>
				<!--
				<div class="my-2">
					<h5 class="text-gray-900 font-weight-bold">Q/2F</h5>
					<h5 class="text-gray-900 font-weight-bold">更新時間:<?php echo $now_time ?></h5>
				</div>
			  	<div class="row">
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2101：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2102：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2103：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter</p>
                            </div>

                          </div>
                        </div>
                        
                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2104：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter</p>
                            </div>

                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2105：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter</p>
                            </div>
                          </div>
                        </div>

                        <div class="col-6 col-lg-2">
                          <div class="card card-h mb-4 card-green text-green text-center h-282">
                            <div class="py-3">
                                <h4 class="m-0 font-weight-bold">Q2106：<span class="text-orange">NG</span></h4>
                            </div>
                            <div>
                                <p>(927.4)</p>
                            </div>
                            <div>
                                <span>類型</span>
                                <p class="text-left ml-4">PowerMeter</p>
                            </div>
                          </div>
                        </div>
				</div>
				-->
				<!--各樓power-meter顯示 END-->






			  <!--
              <div class="my-2">
                <h5 class="text-gray-900">更新時間:<?php echo $now_time ?></h5>
			  </div>
              <div class="table-responsive">
                          <table class="table  text-center font-weight-bold">
                            <thead class="thead-green">
                            <tr class="text-center">
							  <th scope="col">類型</th>
                              <th scope="col">房號</th> 
                              <th scope="col">狀態</th>
							  <th scope="col">目前度數</th>
                              <th scope="col">更新時間</th>
                            </tr>
                            </thead>
                            <tbody><?php foreach($pm_table as $v) echo $v ?></tbody>
                          </table>
			  </div>
				-->
              <!--PowerMeter Table END--->
            </div> 			


          </div>
          <!--Table End--->
          <!--TEST END-->

  </div>
  
	<script>
	
	$(document).ready(function(){
		
		ng_data_search();
		
		$('#ng-tab').click(function() {
			ng_data_search();
		});
		
		$('#reader-tab').click(function() {
			rd_data_search();
		});
		
		$('#meter-tab').click(function() {
			mt_data_search();
		});
		
		$('#power-meter-tab').click(function() {
			pm_data_search();
		});
		
		$('#meter-relay-tab').click(function() {
			mr_data_search();
		});
	});
	
	/**
	  ReaderDeiveError 就是　Reader     1 是Ng, 0 是 ok
	  MeterDeviceError 就是　Meter      1 是ok, 0 是 NG
	  PowerMeterError  就是　PowerMeter 1 是Ng, 0 是 ok
	  MeterRelayStatus 就是  MeterRelay 1 是Ng, 0 是 ok	*/
	function ng_data_search() {
	  
		$.ajax({
			url: "model/ng_data_search.php",
			data: { floor: $('#ng_floor').val(), },
			type: 'post',
			success: function(data) {
				$('#ng_data_div').html(data);
			}
		});	  
	}

	function rd_data_search() {
	  
		$.ajax({
			url: "model/rd_data_search.php",
			data: { floor: $('#rd_floor').val(), },
			type: 'post',
			success: function(data) {
				$('#rd_data_div').html(data);
			}
		});	  
	}

	function mt_data_search() {
	  
		$.ajax({
			url: "model/mt_data_search.php",
			data: { floor: $('#mt_floor').val(), },
			type: 'post',
			success: function(data) {
				$('#mt_data_div').html(data);
			}
		});	  
	}

	function pm_data_search() {
	  
		$.ajax({
			url: "model/pm_data_search.php",
			data: { floor: $('#pm_floor').val(), },
			type: 'post',
			success: function(data) {
				$('#pm_data_div').html(data);
			}
		});	  
	}
	
	function mr_data_search() {
	  
		$.ajax({
			url: "model/mr_data_search.php",
			data: { floor: $('#mr_floor').val(), },
			type: 'post',
			success: function(data) {
				$('#mr_data_div').html(data);
			}
		});	  
	}
  
	</script>
  
  <!-- /.container-fluid -->
<?php	
    include('includes/footer.php');
?>