<?php
require '../apis/functions.php';
require '../apis/classList.php';
session_start();
//需要req老師點擊的課程日期 Y-m-d
$post = $_REQUEST['date'];
// date - id 
//改為用'//'分割
$dta = explode("//", $post);
$date = $dta[0];
$classID = $dta[1];
$all = array();
$id = $_SESSION['id'];
echo $id;
//用seesion id對應老師的本名
$sql = "SELECT id, name FROM account WHERE id = ".$id;
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user == false) {
    echo "Unknown Error";
} else {
    /* 後處理 */
    $sql_get = "SELECT * FROM account WHERE permission = 2";
    // permission = 1 為老師
    $stmt = $pdo->prepare($sql_get);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //獲取所有學生帳號
    //account (id, username, password, name, permission)
    //class_check_io (class, date, check_in, check_out, id)
    foreach($students as $stu) {
        //將學生做個別處裡
        //用classID,日期,學生ID對應出該節課的簽到紀錄
        $sql_c = "SELECT * FROM class_check_io WHERE classID = :classID AND date = :date AND userID = :username";
        $stmt = $pdo->prepare($sql_c);
        $stmt->bindValue(':classID', $classID);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':username', $stu['username']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user == false) {//有人沒簽到
            addSbToCheckIO($pdo, $stu['username'], $classID, $date);
        }
    }
    /* ----- */
    //開始抓取資料 以日期和課堂編號作對應
    $sql_allStudent = "SELECT * FROM class_check_io WHERE classID = :classID AND date = :date";
    $stmt = $pdo->prepare($sql_allStudent);
    $stmt->bindValue(':classID', $classID);
    $stmt->bindValue(':date', $date);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stuNumberArr = array();
    $stuNumber = array();
    foreach($students as $cc) {
        array_push($stuNumberArr,['userID'=>$cc['userID'],'check_in'=>$cc['check_in'],'check_out'=>$cc['check_out'],'number'=>studentList[strval($cc['userID'])][0]]);
    }
    $stuNumber = array_column($stuNumberArr,'number');
    array_multisort($stuNumber,SORT_ASC,$stuNumberArr);
    foreach ($stuNumberArr as $cc) {
        echo "<tr>";
        echo "<td>".studentList[$cc['userID']][1]."</td>";
        echo "<td>".studentList[$cc['userID']][0]."</td>";
        echo "<td>";
        if($cc['check_in'] == 1) echo "<font color='green'>是</font>"; else if($cc['check_in'] == 0) echo "<font color='red'>否</font>";
        echo "</td>";
        echo "<td>";
        if($cc['check_out'] == 1) echo "<font color='green'>是</font>"; else if($cc['check_out'] == 0) echo "<font color='red'>否</font>";
        echo "</td>";
        echo "</tr>";
    }
}
?>
