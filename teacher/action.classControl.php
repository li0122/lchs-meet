<?php

require '../apis/connect.php';
require '../apis/classList.php';
require '../apis/functions.php';
date_default_timezone_set('Asia/Taipei');
session_start();
$ERRMSG = "";

$date = date('Y-m-d');
$w = date('w');
$h = date('G');
$min = date('i');

$time = $h . $min;

//用seesion id對應老師的本名
$sql = "SELECT id, name FROM account WHERE id = " . $_SESSION['id'];
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user == false) {

    $ERRMSG = '未知錯誤 無法確認身分';

}
else{

    if (isset($_POST['BEGIN'])) {
        $code = $_POST['code'];
        $arr = getNextClass($pdo, false);
        $arr = (array)json_decode($arr);
	    //die(var_dump($arr));
	    if ($arr["classID"] == "0") {//今天沒課了
            $ERRMSG = '找不到您想開始的課喔._.';
        }
        else{

            if ($arr['isBegin'] == 1) {//老師已經點開始了
                $ERRMSG = '現在有課程在進行喔><';
	        }
            else{

                $classID = $arr['classID'];

                $sql = 'SELECT * FROM classTime WHERE classID = :classID and date = :date';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':classID', $classID);
                $stmt->bindValue(':date', $date);
                $stmt->execute();
                $classIndex = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($classIndex as $index) {
                    if ($time < timeCal($index['start'], -10)) {
                        $ERRMSG = "請在上課前10分鐘再開始課程喔!";
                        break;
                    }
                    else{

                        $sql_is = 'SELECT * FROM meetUrl WHERE date = :date and classID = :classID';
                        $s = $pdo->prepare($sql_is);
                        $s->bindValue(':classID', $classID);
                        $s->bindvalue(':date', $date);
                        $s->execute();
                        if ($s->fetchAll(PDO::FETCH_ASSOC) == false) {
                            
                            $sql_push = 'INSERT INTO meetUrl(classID, date, code) VALUES (:classID, :date, :code)';
                            $st = $pdo->prepare($sql_push);
                            $st->bindValue(':classID', $classID);
                            $st->bindvalue(':date', $date);
                            $st->bindValue(':code', $code);
                            $st->execute();
                        }
                        else{
                            $sql_push = 'UPDATE meetUrl SET code = :code WHERE classID = :classID and date = :date';
                            $st = $pdo->prepare($sql_push);
                            $st->bindValue(':classID', $classID);
                            $st->bindvalue(':date', $date);
                            $st->bindValue(':code', $code);
                            $st->execute();
                        }
                        if ($index['mode'] == 0) {//閒置
                            $sql = 'UPDATE 
                            classTime
                            SET 
                            mode = 1 
                            WHERE 
                            date = :date AND classID = :classID';
        
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':classID', $classID);
                            $stmt->bindValue(':date', $date);
                            $stmt->execute();
                        }
                    }
                }
            }
        }
        if ($code == "") {
            $ERRMSG = "未知錯誤 無法找到code";
        }
        if ($ERRMSG == "") {
            header("Location: https://meet.google.com/".$code);
        }
    }

    if (isset($_POST['STOP'])) {
        $arr = getNextClass($pdo, true);

        if ($arr['classID'] == 0) {//今天沒課了+現在沒在上課
            $ERRMSG = '找不到您想關閉的課喔._.';
        }
        else{

            if ($arr['isBegin'] == 0) {//還沒開課
                $ERRMSG = '你要先開啟課程才能夠關閉課程喔><;';
            }
            else{//徹底開啟

                $classID = $arr['classID'];

                $sql = 'UPDATE 
                classTime
                SET 
                mode = -1 
                WHERE 
                date = :date AND classID = :classID';

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':classID', $classID);
                $stmt->bindValue(':date', $date);
                $stmt->execute();

                $ERRMSG = '你已關閉課程!!';

            }
        }
    }
}
if ($ERRMSG != "") {
	header("Location: ./releaseCode.php?msg=".$ERRMSG);
}




?>
