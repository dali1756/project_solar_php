<?php include('head.php'); ?>
    <div class="content">
        <div class="container">
            <div class="page-title">
                <h3>歷史記錄 </h3>
            </div>
            <div class="col-lg-12">
                <label for="" class="form-label"> 查詢日期 </label>
                                <input type="text" class="form-control datepicker-here" data-language="zh"
                                    aria-describedby="datepicker" placeholder="請選擇日期"><br>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="content">
                                                <div class="head"> 
                                                    <h5 class="mb-0">變流器1-PV輸入功率(Kw)</h5>
                                                    <p class="text-muted">2023-02-09 00:00:00 To 2023-02-09 24:00:00</p>
                                                </div>
                                                <div class="canvas-wrapper">
                                                    <canvas class="chart" id="trafficflow"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
            </div>

            <div class="col-lg-12">
                <div class="col-md-12">
                    <div class="card">
                        <div class="content">
                            <div class="head"> 
                                <h5 class="mb-0">發電量(Kw)</h5>
                                <p class="text-muted">2023-02-09 00:00:00 To 2023-02-09 24:00:00</p>
                            </div>
                            <div class="canvas-wrapper">
                                <canvas class="chart" id="myChart"
                                    width="400" height="400" 
                                ></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

            
    </div>
</div>
    <!-- Loading -->
<div id="waitloading" class="w-100 d-none"></div>
<script src="assets/js/search_table.js"></script>
<?php include('footer.php'); ?>
<script>
const ctx = document.getElementById('myChart').getContext('2d');
function SearchData1(url,success_function){
    $.ajax({
        url:url,
        type:'GET',
        // data:,
        dataType:'json',
        // beforeSend:function(){LoadingMask('#waitloading')},
        // complete:function(){ClearMask('#waitloading','#search-data');}
    })
    .then(
        function(data){success_function(data)},
        function(err){      
            alert(`連線失敗\n
                HTTP狀態代碼訊息:${err.statusText}\n
                服務器返回訊息:${err.responseText}
            `);
            console.log(`連線失敗，抓不到資料\n
                當前狀態：${err.readyState}\n
                HTTP狀態代碼:${err.status}\n
                HTTP狀態代碼訊息:${err.statusText}\n
                服務器返回訊息:${err.responseText}
            `);
        }
    )
}

let test = (data)=>{
    console.log('data',data);
    const data_2016 = data;
    const cfg2 = {
            // type: 'bar',
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'machine1',
                        data: data,
                        backgroundColor:'rgb(226 138 138)',
                        // 折線顏色
                        borderColor: 'rgb(226 138 138)',
                        borderWidth: 3,
                        // 圓點背景色、大小
                        pointBackgroundColor:'rgb(226 138 138)',
                        pointRadius:5,
                        parsing: {
                            xAxisKey: 'machine1',
                            yAxisKey: 'x'
                        }
                    },
                    {
                        label: 'machine2',
                        data: data,
                        // 圖示底色
                        backgroundColor:'lightblue',
                        // 折線樣式
                        borderColor: 'lightblue',
                        borderWidth: 3,
                        // 圓點背景色、大小
                        pointBackgroundColor:'lightblue',
                        pointRadius:5,
                        parsing: {
                            xAxisKey: 'machine2',
                            yAxisKey: 'x'
                        }
                    },
                    {
                        label: 'machine3',
                        data: data,
                        backgroundColor:'blue',
                        // 圖示底色
                        backgroundColor:'darkseagreen',
                        // 折線樣式
                        borderColor: 'darkseagreen',
                        borderWidth: 3,
                        // 圓點背景色、大小
                        pointBackgroundColor:'darkseagreen',
                        pointRadius:5,
                        parsing: {
                            xAxisKey: 'machine3',
                            yAxisKey: 'x'
                        }
                    }
                ]
            },
            options:{
                // 設定圖表改為橫向呈現
                indexAxis: 'y',
                // 操作滑鼠hover同時顯示所有線當前該節點位置
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                            legend: {
                                // 圖示排放位置
                                // position: 'top',
                                labels:{font: {size: 16}}
                            },
                            title: {
                                display: true,
                                text: '發電量',
                                color: 'black',
                                font: {size: 18}
                            }
                }
            }
        };
    myChart = new Chart(ctx,cfg2);
}

    $(document).ready(function(){
            SearchData1('./assets/api/test_power_data.json',test);
    });
</script>
