<?php

require '../apis/connect.php';
require '../apis/classList.php';

session_start();
$ERRMSG = "";
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
$id = $_SESSION['id'];

if ($permission == 2) {
	header('Location: ../student/index.php');
	exit;
}

$classIndex = array();
$index = array();
$dates = array();

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
								<h1 size="4">教師確認簽到表</h1>	
								<a href="./releaseCode.php" class="button primary fit">公布上課代碼&上課/下課</a>
							</header>
						</section>
						<div class="col-12">
							<form method="get" action="viewMonitor.php">
								<select id="date" name="date">
								<?php
								$classIndex = array();
								$index = array();
								$dates = array();
								$all = array();
								$subject = "";
								$sql = "SELECT * FROM account WHERE id = '". $_SESSION['id'] ."'";
								$name = "";
								foreach ($pdo->query($sql) as $row) {
									$name = $row['name'];
								}
								$sql = "SELECT * FROM classTime WHERE teacherName = :teacherName ORDER BY date ASC";
								$stmt = $pdo->prepare($sql);
								$stmt->bindValue(":teacherName", $name);
								$stmt->execute();

								foreach($stmt->fetchAll() as $c=>$cc){
									array_push($classIndex, $cc['classID']); // 把課堂ID存進$classIndex
									$subject = $cc['subject'];
								}
								function gcw($datetime){
									$weekday = date('w', strtotime($datetime));
									return ' （星期' . ['日', '一', '二', '三', '四', '五', '六'][$weekday];
								}
								foreach($classIndex as $classID) {
                        		    $combine = [];

									$sql_class2 = "SELECT * FROM classTime WHERE classID = :classID and teacherName = :teacherName"; // AND date = :date";
                        		   	$stmt = $pdo->prepare($sql_class2);
                        		   	$stmt->bindValue(":classID", $classID);
									$stmt->bindValue(":teacherName", $name);
                        		    $stmt->execute();
                        		    $resultInfo2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        		    foreach ($resultInfo2 as $row) {
                        		    	array_push($combine, $row['date']."//".$row['classID']."//".$row['start']."//".$row['end']);
									}
								
									$combine = array_unique($combine);

									foreach($combine as $rst){
										$arr = explode("//",$rst);
										echo "<option value='".$rst."'>".$arr[0].gcw($arr[0])."） ".strval($arr[2])." - " . strval($arr[3]) ." [".$subject."]</option>";
									}
								}
								?>
								</select>
								</br>
								<input type="submit" name="QUERY" id="QUERY" value="QUERY" class="primary" />
							</form>
						</div>
					</div>
				</section>
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
