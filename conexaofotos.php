<?php


$servername = "localhost";
$username = "root";
$password = "";

$dbname = "login";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão com o banco de dados falhou: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>