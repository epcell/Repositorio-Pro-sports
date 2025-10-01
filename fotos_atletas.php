<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/apache/logs/php_error.log');

session_start();

error_log("fotos_atletas.php: Script iniciado.");

require_once 'conexaofotos.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    $error_message = "fotos_atletas.php: Erro na conexão com o BD: " . ($conn->connect_error ?? "Variável \$conn não definida ou inválida.");
    error_log($error_message);
    echo json_encode(["success" => false, "message" => "Erro de conexão com o banco de dados."]);
    exit();
}

try {
    $sql = "SELECT id, nome, modalidade, foto_url, id_usuario FROM fotos_atletas ORDER BY id DESC";
    $result = $conn->query($sql);

    $fotos = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['url'] = 'http://localhost/tcc/' . $row['foto_url'];
            $fotos[] = $row;
        }
        $result->free();
    } else {
        error_log("fotos_atletas.php: Erro na query SQL: " . $conn->error);
    }

    echo json_encode($fotos);

} catch (Exception $e) {
    error_log("fotos_atletas.php: Exceção: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Ocorreu um erro interno ao carregar as fotos."]);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
        error_log("fotos_atletas.php: Conexão com o BD fechada.");
    }
    error_log("fotos_atletas.php: Script finalizado.");
}
?>