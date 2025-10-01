<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include_once 'conexao.php';

$response = array('success' => false, 'message' => 'Ocorreu um erro desconhecido.');

if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
    $response['message'] = 'ID da foto ou caminho não fornecido.';
    echo json_encode($response);
    exit;
}

$postId = $_POST['post_id'];

try {
    $sql = "DELETE FROM posts_contratantes WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("i", $postId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Publicação excluída com sucesso!';
    } else {
        $response['message'] = 'Nenhuma publicação encontrada com o ID fornecido ou já foi excluída.';
    }

    $stmt->close();
    
} catch (Exception $e) {
    $response['message'] = 'Erro: ' . $e->getMessage();
}

$conn->close();
echo json_encode($response);
?>