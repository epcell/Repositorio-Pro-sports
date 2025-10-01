<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


include_once 'conexao_login.php'; 


$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname = "login"; 

$conn = new mysqli($servidor, $usuario, $senha, $dbname);

if ($conn->connect_error) {
   
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro interno de conexão.']);
    exit();
}
 ?>