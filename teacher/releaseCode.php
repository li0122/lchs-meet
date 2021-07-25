<?php
if(isset($_GET['msg']) and $_GET['msg']!=""){
    echo "<script type='text/javascript'>alert('".$_GET['msg']."');location.href ='./releaseCode.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>LCHS MEET - Build By li0122/xig1517</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport">
    <link href="../assets/css/main.css" rel="stylesheet"><noscript>
    <link href="../assets/css/noscript.css" rel="stylesheet"></noscript>
</head>
<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Header -->
        <header class="alt" id="header">
            <a class="logo" href="index.php"><strong>LCHS MEET</strong> <span>by li0122/xig1517</span></a>
            <nav>
                <a href="#menu">Menu</a>
            </nav>
        </header><!-- Menu -->
        <nav id="menu">
            <ul class="links">
                <li>
                    <a href="#">Build</a>
                </li>
                <li>
                    <a href="#">By</a>
                </li>
                <li>
                    <a href="#">王瓅</a>
                </li>
                <li>
                    <a href="#">朱晉岑</a>
                </li>
            </ul>
            <ul class="actions stacked">
                <li>
                    <a class="button primary fit" href="#">LCHS MEET</a>
                </li>
                <li>
                    <a class="button fit" href="../login/action.logout.php">LOGOUT</a>
                </li>
                <li>
                    <a class="button primary fit" href="./releaseCode.php">公布 上課代碼&上課/下課</a>
                </li>
                <li>
                    <a class="button fit" href="../login/changePass.php">更改密碼</a>
                </li>
            </ul>
        </nav><!-- Banner -->
        <section class="major" id="banner">
            <div class="inner">
                <section>
                    <header class="major">
                        <h1>上課代碼 - 上課/下課</h1>
                        <h4>目前排定課程：<font color="green">
                        <?php
                        require "../apis/functions.php";

                        $msg = '';
                        $arr = getNextClass($pdo, false);
                        $arr = (array)json_decode($arr);
                        if ($arr['classID'] == 0) {
                            $msg = "無";
                        }
                            
                        if ($msg == '') {
                            $sql = "SELECT * FROM classTime where classID=:classID and date=:date";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(':classID',$arr['classID']);
                            $stmt->bindValue(':date',date('Y-m-d'));
                            $stmt->execute();
                            $meetUrlArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach($meetUrlArray as $u){
                                if(strlen(strval($u['start'])) == 4) 
                                    echo $u['subject'].' '.strval($u['start'])[strlen(strval($u['start']))-4].strval($u['start'])[strlen(strval($u['start']))-3].":".strval($u['start'])[strlen(strval($u['start']))-2].strval($u['start'])[strlen(strval($u['start']))-1];
                                else 
                                    echo $u['subject'].' '."0".strval($u['start'])[strlen(strval($u['start']))-3].":".strval($u['start'])[strlen(strval($u['start']))-2].strval($u['start'])[strlen(strval($u['start']))-1];
    
                                if(strlen(strval($u['end'])) == 4) 
                                    echo ' - '.strval($u['end'])[strlen(strval($u['end']))-4].strval($u['end'])[strlen(strval($u['end']))-3].":".strval($u['end'])[strlen(strval($u['end']))-2].strval($u['end'])[strlen(strval($u['end']))-1];
                                else 
                                    echo ' - '."0".strval($u['end'])[strlen(strval($u['end']))-3].":".strval($u['end'])[strlen(strval($u['end']))-2].strval($u['end'])[strlen(strval($u['end']))-1];
    
                                if($u['mode'] == 0){
                                    echo " [未開始上課]";
                                }
                                echo $msg;
                            }
                        }
                        echo $msg;
                        ?>
                        </font></h4>
                        <form action="action.classControl.php" method="post">
                            <div class="row gtr-uniform">
                                <div class="col-6 col-12-xsmall">
                                    <input id="code" name="code" placeholder="代碼" type="text">
                                </div>
                                <div class="col-12">
                                    <ul class="actions">
                                        <li><input class="primary" id="BEGIN" name="BEGIN" type="submit" value="發布代碼&上課"></li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                        <form action="action.classControl.php" method="post">
                            <div class="row gtr-uniform">
                                <div class="col-12">
                                    <ul class="actions">
                                        <li><input class="primary" id="STOP" name="STOP" type="submit" value="下課"></li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </header>
                </section>
            </div>
        </section>
    </div><!-- Scripts -->
    <script src="../assets/js/jquery.min.js">
    </script> 
    <script src="../assets/js/jquery.scrolly.min.js">
    </script> 
    <script src="../assets/js/jquery.scrollex.min.js">
    </script> 
    <script src="../assets/js/browser.min.js">
    </script> 
    <script src="../assets/js/breakpoints.min.js">
    </script> 
    <script src="../assets/js/util.js">
    </script> 
    <script src="../assets/js/main.js">
    </script>
</body>
</html>