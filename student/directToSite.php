<?php
require '../apis/functions.php';
require_once '../apis/connect.php';
require '../apis/classList.php';
date_default_timezone_set('Asia/Taipei');
$arr = getNextClass($pdo, false);
$arr = (array)json_decode($arr);
$sql = "SELECT * FROM meetUrl where classID=:classID and date=:date";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':classID',$arr['classID']);
$stmt->bindValue(':date',date("Y-m-d"));
$stmt->execute();
$meetUrlArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
if($meetUrlArray == true) foreach($meetUrlArray as $u){ 
	$URL = "https://meet.google.com/".$u['code'];
	if($arr['isBegin']==0) 
		echo "<script>alert('尚未開課'); location.href = './index.php';</script>"; 
	else if($arr['isBegin']==2) 
		echo "<script>alert('課程已結束'); location.href = './index.php';</script>";
    else 
		echo "<script>window.open('".$URL."'".", '_blank');</script>";

}
else{
	echo "<script>alert('尚未開課'); location.href = './index.php';</script>";
}

?>
