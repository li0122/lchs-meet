<?php

$ERRMSG = "";

date_default_timezone_set('Asia/Taipei');

require '../apis/connect.php';
require '../apis/classList.php';
require '../apis/functions.php';

session_start();

$date = date('Y').'-'.date('m').'-'.date('d');
$w = date('w');
$hr = date('G');
$min = date('i');

/* 串接session_id 獲取學號(username) */
$searchStuNameSQL = "SELECT * FROM account WHERE id = ".$_SESSION['id'];
$stmt = $pdo->prepare($searchStuNameSQL);
$stmt->execute();

$accDetail = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $accDetail['username']; //學生帳號(學號)+

if(isset($_GET['SIGNIN'])){ //簽到

    $sql = "SELECT * FROM classTime WHERE date = :date";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":date", $date);
    $stmt->execute();
    $c = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($c) {

        $all = array();
        foreach ($c as $index) {

            $mode = $index['mode'];//mode=1開始上課 mode=0還沒上課 mode=-1已經下課
            $time = intval($hr.$min);
            $start = $index['start'];
            $end = $index['end'];
            $classID = $index['classID'];

            if ($time >= timeCal($start, -10) and $time <= $end) {//上課前5分鐘~下課(簽到時間)

                $sql_get = "SELECT * FROM class_check_io WHERE date = :date and classID = :classID and userID = :userID";
                $stmt = $pdo->prepare($sql_get);
                $stmt->bindValue(':date', $date);
                $stmt->bindValue(':classID', $index['classID']);
                $stmt->bindValue(':userID', $username);
                $stmt->execute();
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($user) {
		            foreach($user as $usr){
                        if ($usr['check_in'] == '0') {

                            $sql_push = 'UPDATE 
                                class_check_io 
                                SET 
                                check_in = "1", signTime = :t 
                                WHERE 
                                date = :date AND classID = :classID AND userID = :userID';
                            $stmt = $pdo->prepare($sql_push);
                            $stmt->bindValue(':classID', $classID);
                            $stmt->bindValue(':date', $date);
                            $stmt->bindValue(':userID', $username);
                            $stmt->bindValue(':t', $time);
                            $stmt->execute();
                            $ERRMSG = "簽到成功! 你獲得了經驗值 0 點.";

                        }
                        else{
                            $ERRMSG = '你已經簽到過了><';
		                }
		            }
                }
                else{

                    $sql_push = "INSERT INTO 
                    class_check_io 
                    (classID, date, check_in, check_out, userID, signTime, signOutTime) 
                    VALUES 
                    (:classID, :date, 1, 0, :userID, :t, -1)";
                    $stmt = $pdo->prepare($sql_push);
                    $stmt->bindValue(':classID', $classID);
                    $stmt->bindValue(':date', $date);
                    $stmt->bindValue(':userID', $username);
                    $stmt->bindValue(':t', $time);
                    $stmt->execute();
                    $ERRMSG = "簽到成功! 你獲得了經驗值 0 點.";
                }
		        break;

            }
            else{

                $ERRMSG = "現在沒有可以簽到的課喔!";

            }

        }

    }
    else{
        $ERRMSG = "今天沒有課程喔><";
    }

}

if(isset($_GET['SIGNOUT'])){ //簽退

    $sql = "SELECT * FROM classTime WHERE date = :date";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":date", $date);
    $stmt->execute();
    $c = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($c) {

        foreach ($c as $index) {

            $mode = $index['mode'];//mode=1開始上課 mode=0還沒上課 mode=-1已經下課
            $time = intval($hr.$min);
            $end = $index['end'];
            $start = $index['start'];
            $classID = $index['classID'];

            $sql_get = "SELECT * FROM class_check_io WHERE date = :date and classID = :classID and userID = :userID";
            $stmt = $pdo->prepare($sql_get);
            $stmt->bindValue(':date', $date);
            $stmt->bindValue(':classID', $index['classID']);
            $stmt->bindValue(':userID', $username);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($user) {

                if ($time > $end and $time <= timeCal($end, 10)) {//下課後10分鐘內(情況一)

                    foreach ($user as $u) {

                        if ($u['check_out'] == 0) {

                            if ($u['check_in'] == 0) {

                                $ERRMSG = "找不到你簽到的紀錄喔><";
                                continue;

                            }
                            else{

                                $sql_push = 'UPDATE 
                                class_check_io 
                                SET 
                                check_out = "1", signOutTime = :t 
                                WHERE 
                                date = :date AND classID = :classID AND userID = :userID';

                            }

                        }
                        else{

                            $ERRMSG = '你已經簽退過了><';
                            continue;

                        }
                        break;
                    }

                    $stmt = $pdo->prepare($sql_push);
                    $stmt->bindValue(':classID', $classID);
                    $stmt->bindValue(':date', $date);
                    $stmt->bindValue(':userID', $username);
                    $stmt->bindValue(':t', $time);
                    $stmt->execute();
                    $ERRMSG = "簽退成功! 你獲得了經驗值 0 點.";
                    break;

                }
                else if ($time > timeCal($end, 10)) {

                    if ($mode == 1) {
                        $sql_push = 'UPDATE 
                        classTime 
                        SET 
                        mode = -1 
                        WHERE 
                        date = :date AND classID = :classID';

                        $stmt = $pdo->prepare($sql_push);
                        $stmt->bindValue(':classID', $classID);
                        $stmt->bindValue(':date', $date);
                        $stmt->execute();
                    }
                    $ERRMSG = "已經超過可以簽退的時間了喔";
                    continue;
                    
                }
                else if ($time > $start and $time <= $end) {

                    if ($mode == -1) {//老師提前下課

                        foreach ($user as $u) {

                            if ($u['check_out'] == 0) {

                                if ($u['check_in'] == 0) {

                                    $ERRMSG = "找不到你簽到的紀錄喔><";
                                    continue;

                                }
                                else{

                                    $sql_push = 'UPDATE 
                                    class_check_io 
                                    SET 
                                    check_out = "1", signOutTime = :t 
                                    WHERE 
                                    date = :date AND classID = :classID AND userID = :userID';

                                }
                            }
                            else{

                                $ERRMSG = '你已經簽退過了><';
                                continue;

                            }
                            break;
                        }
                        if ($sql_push != "") {
                            $stmt = $pdo->prepare($sql_push);
                            $stmt->bindValue(':classID', $classID);
                            $stmt->bindValue(':date', $date);
                            $stmt->bindValue(':userID', $username);
                            $stmt->bindValue(':t', $time);
                            $stmt->execute();
                            $ERRMSG = "簽退成功! 你獲得了經驗值 0 點.";
                            break;
                        }
                    }
                    else{

                        $ERRMSG = '上課時間不能簽退喔= =';
                        break;

                    }

                }

            }
            else{

                $ERRMSG = "找不到你簽到的紀錄喔><";
                addSbToCheckIO($pdo, $username, $index['classID'], $date);
                continue;

            }
        }
    }
    else{
        $ERRMSG = "今天沒有課程喔><";
    }
}
header("Location: ./index.php?msg=".$ERRMSG);

?>

