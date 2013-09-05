<?php

require_once 'config.php';
require INC_DIR . 'PHPExcel/PHPExcel.php';

check_login();

$s_date = arg('s_date', 'G', '2009-01-01');
$e_date = arg('e_date', 'G', '2009-03-31');
$job_no = urldecode(arg('job_no', 'G', ''));
$mode = arg('mode', 'G', 'download');

$depts = array();
$sql = "select id, area_name from " . $db_tbl_prefix . "area order by sort_order";
$rs = mysql_query($sql);
while ($r = mysql_fetch_array($rs)) {
    /* 		if ($r['id'] == 4) { //exclude management
      continue;
      } */
    $depts[$r['id']] = array("name" => $r['area_name'], "hr" => 0.0, "job" => array());
}


$users = array();
$sql = "select r.id as id, r.room_name as u_name, a.id as dept_id from " . $db_tbl_prefix . "room r left join " . $db_tbl_prefix . "area a on (r.area_id = a.id) order by r.sort_order";

$rs = mysql_query($sql);
while ($r = mysql_fetch_array($rs)) {
    $users[$r['id']] = array("name" => $r['u_name'], "dept_id" => $r['dept_id'], "hr" => 0.0, "job" => array());
}

$s_date_part = split("-", $s_date);
$start_date = mktime(0, 0, 0, ($s_date_part[1]), ($s_date_part[2]), ($s_date_part[0]));
$e_date_part = split("-", $e_date);
$end_date = mktime(0, 0, 0, ($e_date_part[1]), ($e_date_part[2] + 1), ($e_date_part[0]));

$sql = "SELECT e.start_time, e.end_time, e.room_id, m.cost,e.name AS job_id 
FROM {$db_tbl_prefix}entry e LEFT OUTER JOIN {$db_tbl_prefix}cost m ON m.room_id=e.room_id	AND (e.start_time BETWEEN m.start_time AND m.end_time)
WHERE e.start_time >= $start_date AND e.end_time < $end_date

	" . ( $job_no == "" ? "" : " AND e.name like '$job_no'") . "
ORDER BY e.room_id,e.start_time";

$rs = mysql_query($sql);



$detail_table = array();
while ($r = mysql_fetch_array($rs)) {
    $job_id = $r['job_id'];
    $user_id = $r['room_id'];
    $start_time = $r['start_time'];
    $end_time = $r['end_time'];
    $dept_id = $users[$user_id]['dept_id'];
    if (!isset($r['cost']) || empty($r['cost'])) {
        $cost_per_hour = 0;
    } else {
        $cost_per_hour = $r['cost'];
    }

    $hour = ($end_time - $start_time) / 3600.0;
    $cost = $hour * $cost_per_hour;
    $users[$user_id]['cost_per_hour'] = $cost_per_hour;
    $users[$user_id]['department'] = $depts[$dept_id]['name'];


    if (!empty($job_no) && $job_no != $job_id) {
        continue;
    }


    //format and print detail row
    $row['job_no'] = $job_id;
    $row['start'] = date('Y-m-d H:i', $start_time);
    $row['end'] = date('Y-m-d H:i', $end_time);
    $row['name'] = $users[$user_id]['name'];
    $row['department'] = $depts[$dept_id]['name'];
    $row['cost_per_hour'] = $cost_per_hour;
    $row['hour'] = $hour;
    $row['cost'] = $cost;
    $row['cost_display'] = number_format($cost, 2);
    //$row['link'] = "<a href='detail.php?job_no=$job_no&dept_id=$selected_dept_id&user_id=$user_id&s_date=$s_date&e_date=$e_date' target='_blank'>" . $row['name'] . "</a>";

    $detail_table[$row['name'] . $row['start']] = $row;
}
ksort($detail_table);


$fields = array(
    'Name' => 'name',
    'Department' => 'department',
    'Job' => 'job_no',
    'Start' => 'start',
    'End' => 'end',
    'Hours' => 'hour',
    'Cost/hr' => 'cost_per_hour',
    'Cost' => 'cost_display',
);


if ($mode == 'display') {

    include header_tpl();
    echo <<<HTML
<h2>Data ($s_date ~ $e_date)</h2>
    <table border="1" cellspacing="0" width="800">
    <tr>
        <th>
HTML;
    echo implode('</th><th>', array_keys($fields));
    echo '</th></tr>';


    foreach ($detail_table as $row) {
        echo '<tr>';
        foreach ($fields as $field) {
            echo "<td>{$row[$field]}</td>";
        }
        echo '</tr>';
    }
    echo '</table>';
    include footer_tpl();
} else {

    $xls = new PHPExcel();
    $ws = $xls->createSheet()->setTitle('Data')->freezePane('B2');
    $xls->removeSheetByIndex(0);

    //header
    $rowNo = 1;
    $colNo = 0;
    foreach (array_keys($fields) as $value) {
        $ws->setCellValueExplicitByColumnAndRow($colNo, $rowNo, $value);
        $ws->getColumnDimensionByColumn($colNo)->setAutoSize(TRUE);
        $colNo++;
    }

    foreach ($detail_table as $row) {

        $rowNo++;
        $colNo = 0;

        foreach ($fields as $field) {
            $value = isset($row[$field]) ? $row[$field] : '';
            $cellType = is_numeric($value) ? PHPExcel_Cell_DataType::TYPE_NUMERIC : PHPExcel_Cell_DataType::TYPE_STRING;
            if ('job_no' == $field) {
                $cellType = PHPExcel_Cell_DataType::TYPE_STRING;
            }
            $ws->setCellValueExplicitByColumnAndRow($colNo++, $rowNo, $value, $cellType);
        }
    }

    setWorksheetStyle($ws);

    header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="TimeCostData(' . $s_date . '~' . $e_date . ').xls"');

    $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
    $objWriter->save('php://output');
}

function setWorksheetStyle($ws) {
    $ws->duplicateStyleArray(
            array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        ),
            ), 'A1:' . $ws->getHighestColumn() . $ws->getHighestRow()
    );

    $style = $ws->getStyle('A1:' . $ws->getHighestColumn() . '1');
    $style->getFont()->setBold(true);
    $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(TRUE);
    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFD6FFCF');
}

?>