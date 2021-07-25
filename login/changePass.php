<?php

$string = "";

require '../apis/connect.php';
require '../apis/classList.php';

session_start();

$id = $_SESSION['id'];
$pdo->query("SET NAMES UTF8");

if(isset($_POST['RESET_PWD'])){

    $sql = "SELECT * FROM account WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user == false){
    
        $string = "ㄨㄚˊ系統找不到你的帳號資料ㄟ";
    
    }else{

        $username = $user['username'];
    
        $old = $_POST['oldPwd']; //舊密碼
        $new = $_POST['newPwd']; //新密碼
        $newCheck = $_POST['newPwdCheck']; //確認密碼
    
        // account (id, username, password, name, permission)
        // 檢查舊密碼是否相符
        $sql = "SELECT * FROM account WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $user['username']);
        $stmt->execute();

        $acc = $stmt->fetch(PDO::FETCH_ASSOC);

        if($acc == false){

            $string = "未找到用戶資訊...";

        }else{

            if(password_verify($old, $acc['password']) == false){
                $string = "舊密碼錯誤.";
            }else{

                if(!($new == $newCheck)){

                    $string = "確認密碼和新密碼不相符";
    
                }else{
    
                    $sql = 'UPDATE account SET password = :newPwd WHERE username = :username';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':newPwd', (String)password_hash(trim($new), PASSWORD_BCRYPT, array("cost" => 12)));
                    $stmt->bindValue(':username', $username);
                    $stmt->execute();
    
                    $string = "更改密碼成功! 新密碼為: ";
                    $string .= $new;

                }
            }
        }
    }
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
				</ul>
			</nav>
			<!-- Banner -->
			<section id="banner" class="major">
				<div class="inner">
					<section>
						<header class="major">
							<h1 size="5">501@LCHS-MEET</h1>
						</header>
						<div class="content">
						
							<form method="post" action="#">
								<div class="row gtr-uniform">
                                <div class="col-12">
									<div class="col-6 col-12-xsmall">
										<input type="password" name="oldPwd" id="oldPwd"  placeholder="舊密碼" />
									</div>
                                    </br>
									<div class="col-6 col-12-xsmall">
										<input type="password" name="newPwd" id="newPwd"  placeholder="新密碼" />
									</div>
                                    </br>
                                    <div class="col-6 col-12-xsmall">
										<input type="password" name="newPwdCheck" id="newPwdCheck"  placeholder="重複新密碼" />
									</div>
                                    </br>
										<ul class="actions">
											<li><input type="submit" name="RESET_PWD" id="RESET_PWD" value="設定密碼" class="primary" /></li>
										</ul>
									</div>
								</div>
							</form>
						</div>
                        <div class="col-12"><?php echo $string; ?></div>
					</section>
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
