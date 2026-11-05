<?php

//recibir los datos del formulario de registro
//require('../database.php');

session_start();

require_once __DIR__ . '/database.php';

if (isset($_SESSION['userID'])) {
    header('refresh: 0; url=index.php');
}

//recibir los datos del formulario de registro
$email = $_POST['email'];
$password = $_POST['pswd'];
$passemc  =md5($password); 

//validar si el usuario existe en la base de datos
$sql = "select * from users where email = '$email' and password = '$passemc'";
$res = pg_query($supa_conn, $sql);
if (pg_num_rows ($res) > 0) {
    $row = pg_fetch_assoc ($res);

    $_SESSION['userID'] = $row['id'];
    $_SESSION['fNAME'] = $row['firstname'];
    $_SESSION['lNAME'] = $row['lastname'];

    header('refresh: 0; url=index.php');
    
} else {
    echo "<script>alert('User email or password is incorrect!!!');</script>";
    header('refresh:0; url=login.html');
}

