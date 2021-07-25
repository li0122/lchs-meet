<?php

require_once 'connect.php';
//開始抓取資料 以日期和課堂編號作對應
/*
$sql_allStudent = "SELECT * FROM class_check_io WHERE classID = :classID AND date = :date";
$stmt = $pdo->prepare($sql_allStudent);
$stmt->bindValue(':classID', 'sun-2');
$stmt->bindValue(':date', '2021-07-11');
$stmt->execute();

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
$all = array();

foreach ($students as $cc) {
    foreach ($cc as $key=>$value) {
        array_push($all, $value);
    }
    echo $all[6];
}
*/

function addOrClass (PDO $pdo) {

    require_once 'classList.php';
    
    if (date("w")==1) {
        $start_monday = date("Y-m-d");
    }
    else if (date("w")==0 or date("w")==6){
        $start_monday = date('Y-m-d', strtotime('next monday'));
    }
    else{
        $start_monday = date('Y-m-d', strtotime('last monday'));
    }

    $teacherName = "";
    foreach (classList as $id => $sub) {
        $teacherName = teacherList[$sub];
        //唯一例外是探究 兩位老師 之後再說

        $start = classInTime[$id];
        $end = classOutTime[$id];

        $sql = "INSERT INTO classTime VALUES (:date, :subject, :teacherName, :start, :end, :classID, :mode)";

        $st = $pdo->prepare($sql);

        $date = "";
        if (strstr($id, "mon")) {
            $date = $start_monday;
        }
        else if (strstr($id, "tue")) {
            $date = date('Y-m-d', strtotime($start_monday.' this tuesday'));
        }
        else if (strstr($id, "wed")) {
            $date = date('Y-m-d', strtotime($start_monday.' this wednesday'));
        }
        else if (strstr($id, "thu")) {
            $date = date('Y-m-d', strtotime($start_monday.' this thursday'));
        }
        else if (strstr($id, "fri")) {
            $date = date('Y-m-d', strtotime($start_monday.' this friday'));
        }
        
        $st->bindValue(':date', $date);
        $st->bindValue(':subject', $sub);
        $st->bindValue(':teacherName', $teacherName);
        $st->bindValue(':start', intval($start));
        $st->bindValue(':end', intval($end));
        $st->bindValue(':classID', $id);
        $st->bindValue(':mode', intval('0'));
        
        $st->execute();
    }
}

function addSbToCheckIO (PDO $pdo, $sbID, $classID, $date) {

    $sql = 'INSERT INTO 
    class_check_io (classID, date, check_in, check_out, userID, signTime, signOutTime) 
    VALUES 
    (:classID, :date, 0, 0, :userID, -1, -1)';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':classID', $classID);
    $stmt->bindValue(':date', $date);
    $stmt->bindValue(':userID', $sbID);
    $stmt->execute();
}


function timeCal ($time, $min) 
{
    $a = preg_split('//', strval($time), -1, PREG_SPLIT_NO_EMPTY);

    if (sizeof($a) == 4) {
        
        $hr = intval($a[0].$a[1]);
        $cal = $hr*60 + intval($a[2].$a[3]);
        $cal = $cal + $min;

    }
    else if (sizeof($a) == 3) {
        $hr = intval($a[0]);
        $cal = $hr*60 + intval($a[1].$a[2]);
        $cal = $cal + $min;
    }
    else {
        return 0;
    }

    return floor($cal/60)*100 + ($cal%60);
}


function showClass (PDO $pdo) {
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
    
    foreach ($c as $cc) {
        foreach ($cc as $key=>$value) {       
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
    
    return strval(json_encode($class, JSON_UNESCAPED_UNICODE));
}



function getNextClass (PDO $pdo, $isArray) 
{

    date_default_timezone_set('Asia/Taipei');

    $json = array();
    $is = false;

    $ctime = date('H') . date('i');

    $sql = "SELECT * FROM classTime WHERE date = :date ORDER BY start ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindvalue(':date', date('Y-m-d'));
    $stmt->execute();
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $start = 0;
    $end = 0;
    if ($all == false) {
        $json['classID'] = 0;
        $json['isInClassTime'] = 0;
        $json['isBegin'] = 0;
    }
    else{

        foreach ($all as $index) {

            if ($index['mode'] == -1) {
                $json['classID'] = 0;
                $json['isInClassTime'] = 0;
                $json['isBegin'] = 0;
                continue;
            }
                

            $start = $index['start'];
            $end = $index['end'];
    
            if ($ctime >= $start) {
                if ($ctime <= $end) {//上課中
    
                    $json['classID'] = $index['classID'];
                    $json['isInClassTime'] = 1;
                    $is = true;
    
                }
            }
            if (!$is) {//確定沒有在上課
    
                $json['isInClassTime'] = 0;
                if ($start >= $ctime) {
                    $json['classID'] = $index['classID'];
                    $is = true;
                }
            }
            if (!$is) {//徹底沒課
                $json['classID'] = "0";
                $json['isInClassTime'] = 0;
            }

            if ($index['mode'] == 1) {//老師已經點開始了
                $json['isBegin'] = 1;
            }
            else{//老師還沒點開始
                $json['isBegin'] = 0;
            }
        }
    }

    if ($isArray) {
        return $json;
    }else{
        return json_encode($json);
    }
}

?>

