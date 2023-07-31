<?php 

	include_once('../../config/db.php');

	include_once('hardwarelist_utility.php');

	// include('../chk_log_in.php');

	$chk_time = 1800; // 30 mins 
	$now_time = date('Y-m-d H:i:s');
	
	$sql_kw   = "";
	
	$floor    = $_POST['floor'];

	$exclu_room = array('Q6214', 'Q6215'); // 前端排除硬體錯誤
	$status_arr = getReaderData();
	$rd_error   = getReaderDeviceError($status_arr);
	$md_error   = getMeterDeviceError($status_arr);
	$md_error_220   = getMeterDeviceError220($status_arr);
	$pm_error   = getPowerMeterError($status_arr);
	$mr_status  = getMeterRelayStatus($status_arr);

	$rd_counts  = count($status_arr);


	if($floor != '') {
		$sql_kw = " AND floor = '{$floor}'";
	}

	//$status_arr = getReaderData(); // 重複,上面已執行
	
	//echo 'status_arr:<BR>';
	//echo 'status_arr count:'.count($status_arr).'<BR>';
	//print_r($status_arr[1]);
	for($i=1;$i<count($status_arr);$i++) {
		//print_r($status_arr[$i]);
		//echo '<BR><BR>';
		/*
		echo 'dong:'.$status_arr[$i][$i]['dong'].'<BR>';
		echo 'center_id:'.$status_arr[$i][$i]['center_id'].'<BR>';
		echo 'ReaderDeviceError:<BR>';
		print_r($status_arr[$i][$i]['ReaderDeviceError']);
		echo '<BR>';
		echo 'MeterDeviceError:<BR>';
		print_r($status_arr[$i][$i]['MeterDeviceError']);
		echo '<BR>';
		echo 'PowerMeterError:<BR>';
		print_r($status_arr[$i][$i]['PowerMeterError']);
		echo '<BR>';
		echo '<BR>';
		*/
	}
	
	/*
    echo 'status_arr count:'.count($status_arr).'<BR>';
	echo 'rd_error count:'.count($rd_error).'<BR>';
    */
	/*	
	echo 'rd_error:<BR>';
	print_r($rd_error);
	echo '<BR>';
	*/
	//$status_map = getMeterRelayStatus($status_arr); // 2022-08-09 此處未使用

	//$sql = "SELECT dong FROM room WHERE 1 {$sql_kw} GROUP BY dong";
	$sql = "SELECT dong FROM room GROUP BY dong";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$dong_arr = $rs->fetchAll();

	if($floor != '') {
		$sql_kw = " AND floor = '{$floor}' GROUP BY floor";
	}
	else
	{
	//	$sql_kw = " GROUP BY floor= 'B1' desc , floor Asc";
		$sql_kw = " GROUP BY floor";
	}
	
	$sql = "SELECT floor FROM room WHERE 1 {$sql_kw} ";
	$rs  = $PDOLink->prepare($sql);
	$rs->execute();
	$floor_arr = $rs->fetchAll();
	
	$old_dong = '';
	$old_center_id = '';
	$dong_1 = 1;
	$dong_2 = 1;
	foreach($dong_arr as $d_v) 
	{
		foreach($floor_arr as $f_v)
		{
			$tail_flag = false;
			$sql = "SELECT * FROM room WHERE dong = '{$d_v['dong']}' AND floor = '{$f_v['floor']}' ORDER BY floor,center_id, meter_id, `name` ";
			//echo 'sql:'.$sql.'<BR>';
			$rs  = $PDOLink->prepare($sql);
			$rs->execute();
			$room_arr = $rs->fetchAll();
			
			foreach($room_arr as $v) 
			{
				$dong  = $v['dong'];
				$center_id  = $v['center_id'];
				if($dong_2 > 1 && (($dong != $old_dong) || ($center_id != $old_center_id))) $dong_1++;
				$meter_id   = $v['meter_id'];
                /*
				echo 'dong_2:'.$dong_2.'<BR>';
				echo 'dong_1:'.$dong_1.'<BR>';
				*/
				/*
				echo 'dong:'.$dong.'<BR>';
				echo 'center_id:'.$center_id.'<BR>';
				echo 'meter_id:'.$meter_id.'<BR>';
				echo 'name:'.$v['name'].'<BR>';
                */
                for($j2=1;$j2<$rd_counts;$j2++) {
                    if($rd_error[$j2][$center_id][41] == $dong && $rd_error[$j2][$center_id][42] == $center_id) {
                        /*
                        echo 'rd_no:'.$j2.',';
                        echo 'center_id:'.$center_id.'<BR>';
                        print_r($rd_error[$j2]);
                        echo '<BR>';
                        */
                        $uptime     = $rd_error[$j2][$center_id][43];
                        $status1    = $rd_error[$j2][$center_id][$meter_id];
                        $status2    = $md_error[$j2][$center_id][$meter_id];
                        $status3    = $pm_error[$j2][$center_id][$meter_id];
                        $status4    = $md_error_220[$j2][$center_id][$meter_id];
                        //echo 'status1:'.$status1.'<BR>';
                    }
                }
				//$uptime     = $status_arr[$dong_1][$center_id]['utime'];
				//$status1    = $rd_error[$dong_1][$center_id][$meter_id];
				//$status2    = $md_error[$dong_1][$center_id][$meter_id];
				//$status3    = $pm_error[$dong_1][$center_id][$meter_id];
				// $status4    = $mr_status[$center_id][$meter_id];
				
				/*
				$uptime     = $status_arr[$dong][$center_id]['utime'];
				$status1    = $rd_error[$dong][$center_id][$meter_id];
				$status2    = $md_error[$dong][$center_id][$meter_id];
				$status3    = $pm_error[$dong][$center_id][$meter_id];
				*/
                /*
				echo 'uptime:'.$uptime.'<BR>';
				echo 'status1:<BR>';
				print_r($status1);
				echo '<BR>';
                */
				if($o_f_v != $f_v['floor'] || $o_d_v != $d_v['dong']) {
					
					$o_f_v = $f_v['floor'];
					$o_d_v = $d_v['dong'];
					
					$tail_flag = true;
					
					$new_body .= "	<div class='my-2'>
										<h5 class='text-gray-900 font-weight-bold'>{$d_v['dong']}/{$f_v['floor']}</h5>
										<h5 class='text-gray-900 font-weight-bold'>更新時間:{$uptime}</h5>
									</div>
									<div class='row'>";
				}
				
				if(strtotime($now_time) - strtotime($uptime) > $chk_time) {
					//echo 'check_1:';
					//echo 'name:'.$v['name'].'<BR>';
					$hd1_error  = "Reader<br>";
					$hd2_error  = "Meter 110";
					$hd4_error  = "Meter 220<br>";
					$hd3_error  = "PowerMeter<br>";
					//echo "v['name']:".$v['name'].'<BR>';
					$hd1_error  = in_array($v['name'], $exclu_room) ? '' : $hd1_error;
					//echo "hd1_error:".$hd1_error.'<BR>';
											
					$new_body .= "	<div class='col-6 col-lg-2'>
									  <div class='card card-h mb-4 card-green text-green text-center h-282'>
										<div class='py-3'>
											<h4 class='m-0 font-weight-bold'>{$v['name']}：<span class='text_ng'>NG</span></h4>
										</div>
										<div>
											<p>({$center_id}/{$meter_id})</p>
											<p>({$v['amount']})</p>
										</div>
										<div>
											<span>類型</span><p class='text-left ml-4'>{$hd1_error}{$hd2_error}{$hd4_error}{$hd3_error}</p>
										</div>
									  </div>
									</div>";
				} else {
										
					if($status1 == 1 || $status2 == 1 || $status3 == 1 || $status4 == 1) {	
					
						// if(in_array($v['name'], $exclu_room) & 
							// $status1 == 1 & $status2 == 1 & $status3 == 0) {
							// continue;
						// }
						/*
						echo 'check_2:';
						echo 'name:'.$v['name'].'<BR>';
						echo 'status1:'.$status1.'<BR>';
						echo 'status2:'.$status2.'<BR>';
						echo 'status3:'.$status3.'<BR>';
						*/
							
						$hd1_error  = $status1 == 1 ? "Reader<br>" : "";
						$hd2_error  = $status2 == 1 ? "Meter 110" : "";
						$hd4_error  = $status4 == 1 ? "Meter 220<br>" : "";
						$hd3_error  = $status3 == 1 ? "PowerMeter<br>" : "";
						
						$hd1_error  = in_array($v['name'], $exclu_room) ? '' : $hd1_error;
						
						$new_body .= "	<div class='col-6 col-lg-2'>
										  <div class='card card-h mb-4 card-green text-green text-center h-282'>
											<div class='py-3'>
												<h4 class='m-0 font-weight-bold'>{$v['name']}：<span class='text_ng'>NG</span></h4>
											</div>
											<div>
												<p>({$center_id}/{$meter_id})</p>
												<p>({$v['amount']})</p>
											</div>
											<div>
												<span>類型</span><p class='text-left ml-4'>{$hd1_error}{$hd2_error}&nbsp;{$hd4_error}{$hd3_error}</p>
											</div>
										  </div>
										</div>";
					}
				}
				$dong_2++;
				$old_dong = $dong;
				$old_center_id = $center_id;
			}
			
			if($tail_flag) {
				$new_body .= "</div>";
			}
		}
	}

	echo $new_body;
?>       