<?php
require_once("../../connect.php");
require_once("../../root/decode_ajax.php");

$max_date = $decoded['days'];

if ($max_date != 7 && $max_date != 30 && $max_date != 60) {
  $max_date = 30;
}

$today = date('d');
if ($today < $max_date) {
  // Last month
  $qty_day_last_month = $max_date - $today;
  $last_month = date('m', strtotime(" -1 month"));
  $last_month_date = date('Y-m-d', strtotime(" -1 month"));
  $max_day_of_last_month = (new DateTime($last_month_date))->format('t');
  $start_day_of_last_month = $max_day_of_last_month - $qty_day_last_month;

  for ($i = $start_day_of_last_month; $i <= $max_day_of_last_month; ++$i) {
    $key = $i . '-' . $last_month;
    $arr[$key] = [0, 0];
  }

  $start_day_of_cur_month = 1;
} else {
  $start_day_of_cur_month = $today - $max_date;
}

// Current month
$cur_month = date('m');
for ($i = $start_day_of_cur_month; $i <= $today; ++$i) {
  $key = $i . '-' . $cur_month;
  $arr[$key] = [0, 0];
}

// Query
// Đã bán
$sql = "SELECT DATE_FORMAT(create_at, '%e-%m') as 'day',
  sum(so_luong_da_ban) as tong_da_ban
  from hang_hoa 
  WHERE DATE(create_at) >= CURDATE() - INTERVAL $max_date DAY
  group by DATE_FORMAT(create_at, '%e-%m')";
// die($sql);
$resultSold = mysqli_query($conn, $sql);

foreach ($resultSold as $each) {
  $arr[$each['day']][0] = (int)$each['tong_da_ban'];
}

$sql = "SELECT DATE_FORMAT(create_at, '%e-%m') as 'day',
  sum(so_luong_ton) as tong_ton
  from hang_hoa 
  WHERE DATE(create_at) >= CURDATE() - INTERVAL $max_date DAY
  group by DATE_FORMAT(create_at, '%e-%m')";
// die($sql);
$resultRest = mysqli_query($conn, $sql);

foreach ($resultRest as $each) {
  $arr[$each['day']][1] = (int)$each['tong_ton'];
}

echo json_encode($arr);
