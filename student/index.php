<?php

$ERRMSG = "";
session_start();
require '../apis/connect.php';

$pdo->query("SET NAMES UTF8");
if(!isset($_SESSION['id'])){
	// logincheck
    header('Location: ../login/index.php');
    exit;
}else{
    $sql = "SELECT * FROM account WHERE id = '". $_SESSION['id'] ."'";
    foreach ($pdo->query($sql) as $row) {
        $account = $row['username'];
        $name = $row['name'];
        $permission = $row['permission'];
    }
}
if ($permission == 1) {
	header('Location: ../teacher/index.php');
	exit;
}

$pdo->query("SET NAMES UTF8");
if(isset($_GET['msg'])){
	$msg = $_GET['msg'];
}else{
	$msg= "";
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>LCHS MEET - Build By li0122/xig1517</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="../assets/css/main.css" />
		<noscript><link rel="stylesheet" href="../assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Wrapper -->
		<div id="wrapper">
			<!-- Header -->
			<header id="header" class="alt">
				<a href="index.php" class="logo"><strong>LCHS MEET</strong> <span>by li0122/xig1517</span></a>
					<nav>
						<a href="#menu">Menu</a>
					</nav>
				</header>

				<!-- Menu -->
				<nav id="menu">
					<ul class="links">
						<li><a href="#">Build</a></li>
						<li><a href="#">By</a></li>
						<li><a href="#">王瓅</a></li>
						<li><a href="#">朱晉岑</a></li>
					</ul>
					<ul class="actions stacked">
						<li><a href="#" class="button primary fit">LCHS MEET</a></li>
						<li><a href="../login/action.logout.php" class="button fit">LOGOUT</a></li>
						<li><a href="../login/changePass.php" class="button fit">更改密碼</a></li>
					</ul>
					</nav>
				<!-- Banner -->
				<section id="banner" class="major">
					<div class="inner">
						<section>
							<header class="major">
								<h1 size="4">簽到/簽退表</h1>
								<h4>目前排定課程：<font color="green">
									<?php
									require "../apis/functions.php";
									$msg2 = "";
									$arr = getNextClass($pdo, false);
									$arr = (array)json_decode($arr);
									if ($arr['classID'] == 0) {
										$msg2 = "無";
									}
									if ($msg2 != "") {
										$sql = "SELECT * FROM classTime where classID = :classID and date = :date";
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
										}
									}
									echo $msg2;
									?>
								</font></h4>
								<button onclick="window.location.href='action.sign.php?SIGNIN=1'">簽到</button>	
								<button onclick="window.location.href='action.sign.php?SIGNOUT=1'">簽退</button>
								<button onclick="window.location.href='directToSite.php'"><?php echo "課程連結"?></button>									
<h4 size="2"><?php echo $msg; ?></h4>
							</header>
						</section>
					</div>
				</section>
			</header>
		</div>
		<!-- Scripts -->
		<script src="../assets/js/jquery.min.js"></script>
		<script src="../assets/js/jquery.scrolly.min.js"></script>
		<script src="../assets/js/jquery.scrollex.min.js"></script>
		<script src="../assets/js/browser.min.js"></script>
		<script src="../assets/js/breakpoints.min.js"></script>
		<script src="../assets/js/util.js"></script>
		<script src="../assets/js/main.js"></script>
	</body>
</html>
