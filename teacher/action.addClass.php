<?php

require '../apis/connect.php';
require '../apis/classList.php';

session_start();
$ERRMSG = "";

/*
    SUBJECT 選擇科目(list)

    DATE 課程日期

    CLASS_TIME_H 開始上課時間hour (list)
    CLASS_TIME_M min (list)

    AFTER_CLASS_TIME_H 下課時間hour (list)
    AFTER_CLASS_TIME_M min (list)
*/

/*
    sql classTime
    date, subject, teacherName, start, end, id
*/


//用seesion id對應老師的本名
$sql = "SELECT id, name FROM account WHERE id = " . $_SESSION['id'];
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (date("w")==1){
    $start_monday = date("Y-m-d");
}else{
    $start_monday = date("Y-m-d", strtotime('last monday'));
}

$class = array();

if (isset($_POST['ADD'])) {

    $sql = 'SELECT * FROM classTime WHERE classID = :classID and date >= :date';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':classID', 'mon-1');
    $stmt->bindValue(':date', $start_monday);
    $stmt->execute();
    $c = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($c == false) {//還沒把固定課表key上去
        addOrClass($pdo);
    }

    if (empty($_POST['SUBJECT'])) {

        $ERRMSG = '請選擇科目';
    } else {

        if (empty($_POST['DATE'])) {

            $ERRMSG = '請選擇日期';
        } else {

            if (empty($_POST['CLASS_TIME_H']) or empty($_POST['CLASS_TIME_M'])) {

                $ERRMSG = '你沒有設置開始上課的時間喔><';
            } else {

                if (empty($_POST['AFTER_CLASS_TIME_H']) or empty($_POST['AFTER_CLASS_TIME_M'])) {

                    $ERRMSG = '你沒有設置結束上課的時間喔><';
                } else {

                    $sub = $_POST['SUBJECT'];
                    $date = $_POST['DATE']; // Y-m-d
                    $start = intval((string)$_POST['CLASS_TIME_H'] . (string)$_POST['CLASS_TIME_M']);
                    $end = intval((string)$_POST['AFTER_CLASS_TIME_H'] . (string)$_POST['AFTER_CLASS_TIME_M']);

                    //用輸入的科目對應該老師的名稱
                    $teacherName = teacherList[$sub];

                    $classID = date('d') . date('H') . date('i') . date('s');// 房間id

                    if ($start > $end) {

                        $sql = "SELECT start, end FROM classTime WHERE date = $date";
                        //只抓取當天的課程時間
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $allCT = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $breaktrue = false;

                        foreach ($allCT as $ct) {

                            foreach ($ct as $t) {

                                $s = $t['start'];
                                $e = $t['end'];
    
                                if ($start >= $s and $start <= $e) { //開始課程時間在某堂課之間(重疊)
                                    $ERRMSG = '課程時間重複了喔';
                                    $breakTrue = true;
                                } else if ($end >= $s and $end <= $e) { //結束課程時間在某堂課之間(重疊)
                                    $ERRMSG = '課程時間重複了喔';
                                    $breakTrue = true;
                                } else if ($start <= $s and $end >= $e) { //徹徹底底地包起來了
                                    $ERRMSG = '課程時間重複了喔';
                                    $breakTrue = true;
                                } else if ($start >= $s and $end <= $e) { //徹徹底底地被包起來了
                                    $ERRMSG = '課程時間重複了喔';
                                    $breakTrue = true;
                                }

                            }
                            if ($breakTrue == false) {
                                $sql_push = "INSERT INTO classTime (date, subject, teacherName, start, end, classID, mode) VALUES (:date, :subject, :teacherName, :start, :end, :classID, 0)";
    
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindValue(':date', $date);
                                $stmt->bindValue(':subject', $sub);
                                $stmt->bindValue(':teacherName', $teacherName);
                                $stmt->bindValue(':start', intval($start));
                                $stmt->bindValue(':end', intval($end));
                                $stmt->bindValue(':classID', $classID);
                                $stmt->execute();
                                
                            }else{
                                break;
                            }

                        }//break
                    }
                }
            }
        }
    }
}
