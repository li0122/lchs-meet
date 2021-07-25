<?php

require '../apis/connect.php';
require '../apis/classList.php';
require '../apis/functions.php';

if (date("w")==1){
    $start_monday = date("Y-m-d");
}else{
    $start_monday = date("Y-m-d", strtotime('last monday'));
}

//從sql抓取classTime資料
$sql = 'SELECT * FROM classTime WHERE classID = :classID and date >= :date';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':classID', 'mon-1');
$stmt->bindvalue(':date', $start_monday);
$stmt->execute();
$c = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($c == false) {//還沒把固定課表key上去
    addOrClass($pdo);
}

$sql = 'SELECT * FROM classTime WHERE date >= :date ORDER BY date';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':date', $start_monday);
$stmt->execute();
$c = $stmt->fetchAll(PDO::FETCH_ASSOC);

$class = array();
$all = array();

foreach($c as $cc) {
    foreach($cc as $key=>$value) {       
        array_push($all, $value);
    }
    array_push($class, array(
        "date"=> $all[0],
        "subject"=> $all[1],
        "teacherName"=> $all[2],
        "start"=> $all[3],
        "end"=> $all[4],
        "classID"=> $all[5],
        "mode"=> $all[6]
    ));
    $all = array();
}

echo json_encode($class, JSON_UNESCAPED_UNICODE);
?>
