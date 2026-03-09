<?php
session_start();
include "../config/koneksi.php";

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];
$role     = $_POST['role'];

if(empty($username) || empty($password) || empty($role)){
    header("Location: login.php?error=1");
    exit;
}

$query = mysqli_query($conn, "
SELECT * FROM users 
WHERE username='$username' OR email='$username'
");

$user = mysqli_fetch_assoc($query);

if($user){

    if(password_verify($password, $user['password'])){

    if($user['role'] !== $role){
        header("Location: login.php?error=1");
        exit;
    }

    $_SESSION['user'] = $user;

    if($user['role'] == "guru"){
        header("Location: ../guru/dashboard.php");
    }elseif($user['role'] == "kepsek"){
        header("Location: ../kepsek/dashboard.php");
    }elseif($user['role'] == "ortu"){
        header("Location: ../ortu/dashboard.php");
    }elseif($user['role'] == "tu"){
        header("Location: ../tu/dashboard.php");
    }

    exit;
}

}else{
    header("Location: login.php?error=1");
    exit;
}
?>