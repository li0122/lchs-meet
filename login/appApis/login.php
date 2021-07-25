<?php
require '../../apis/connect.php';
$username = !empty($_POST['account']) ? trim($_POST['account']) : null;
$passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;
$loginStatus = "";
$permission = "";
$sql = "SELECT * FROM account WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username);
$stmt->execute(); // execute
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user == false){
    $loginStatus = "False";
    $permission = "Null";
}else{
    $validPassword = password_verify($passwordAttempt, $user['password']);
    if($validPassword){
        $p = $user['permission']; //權限
        $loginStatus = "True";
        if($p == 0){
            $permission = "admin";
        }else if($p == 1){
            $permission = "teacher";
        }else{
            $permission = "student";
        }
    }else{
        $loginStatus = "False";
        $permission = "Null";
    }
}
$rtnMsg = array('loginStatus' => $loginStatus,'permission' => $permission);
echo json_encode($rtnMsg);
?>
