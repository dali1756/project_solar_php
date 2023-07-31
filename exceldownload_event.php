<?php
function exceldownload()
{
    require 'vendor/autoload.php';
    include_once('db.php');
    $invertor_id = isset($_POST['value1']) ? $_POST['value1'] : null;;//變流器id
    $year = isset($_POST['value2']) ? $_POST['value2'] : null;;//年
    $month = isset($_POST['value3']) ? $_POST['value3'] : null;//月份
    // 創建一个新的 Excel 對象
    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    // 獲取默認的工作表
    $sheet = $spreadsheet->getActiveSheet();
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
    // 設置表頭
    $sheet->setCellValue('A1', '變流器名稱');
    $sheet->setCellValue('B1', '發生時間');
    $sheet->setCellValue('C1', '錯誤類別');
    $sheet->setCellValue('D1', '代碼');
    $sheet->setCellValue('E1', '事件');
    $sheet->setCellValue('F1', '結束時間');
    // 填充数据
    $row = 2;
    if (count($fault) > 0) {
        foreach ($fault as $fault_row) {
            $sheet->setCellValue('A' . $row, $fault_row['sensor_name']);
            $sheet->setCellValue('B' . $row, $fault_row['fault_date']);
            $sheet->setCellValue('C' . $row, $fault_row['fault_class']);
            $sheet->setCellValue('D' . $row, $fault_row['bit']);
            $sheet->setCellValue('E' . $row, $fault_row['event']);
            $sheet->setCellValue('F' . $row, $fault_row['recover_date']);
            $row++; // 在內層循環结束後增加行號
        } 
    }else {
        echo '0';
    }
           
    // 保存 Excel 文件
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    //$writer->save('./example.xlsx');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="example.csv"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Csv');
    $writer->setUseBOM(true);
    $writer->save('php://output');
}

// 调用函数
exceldownload();

?>