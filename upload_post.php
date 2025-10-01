<?php
// upload_post.php
session_start();
header('Content-Type: application/json');

include_once 'conexao_login.php'; // Use seu arquivo de conexão

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não autenticado.';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];
$description = $_POST['description'] ?? null; // A descrição da postagem

if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'Nenhum arquivo de mídia enviado ou erro no upload.';
    echo json_encode($response);
    $conn->close();
    exit();
}

$fileTmpPath = $_FILES['media']['tmp_name'];
$fileName = $_FILES['media']['name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$allowedImageExtensions = ['jpg', 'gif', 'png', 'jpeg'];
$allowedVideoExtensions = ['mp4', 'mov', 'avi', 'webm'];
$allowedfileExtensions = array_merge($allowedImageExtensions, $allowedVideoExtensions);

$media_type = '';
if (in_array($fileExtension, $allowedImageExtensions)) {
    $media_type = 'image';
} elseif (in_array($fileExtension, $allowedVideoExtensions)) {
    $media_type = 'video';
} else {
    $response['message'] = 'Tipo de arquivo de mídia não permitido. Use imagens (JPG, PNG, GIF) ou vídeos (MP4, MOV, AVI, WEBM).';
    echo json_encode($response);
    $conn->close();
    exit();
}

// Gerar um nome de arquivo único para segurança
$newFileName = uniqid() . '-' . time() . '.' . $fileExtension;
$uploadFileDir = './uploads/posts/'; // Crie esta pasta no seu servidor

if (!is_dir($uploadFileDir)) { // Cria o diretório se não existir
    mkdir($uploadFileDir, 0777, true); // Permissões 0777 para teste, ajuste para produção
}

$dest_path = $uploadFileDir . $newFileName;

if (move_uploaded_file($fileTmpPath, $dest_path)) {
    try {
        // Inserir os detalhes da postagem na tabela posts_atletas
        // Adapte os nomes das colunas conforme sua tabela posts_atletas
        $stmt = $conn->prepare("INSERT INTO posts_atletas (user_id, media_url, descricao, tipo_midia, data_publicacao) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("isss", $userId, $dest_path, $description, $media_type);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Publicação criada com sucesso!';
            } else {
                $response['message'] = 'Erro ao inserir publicação no banco de dados.';
                // Se a inserção no DB falhar, tente remover o arquivo enviado para evitar lixo
                if (file_exists($dest_path)) {
                    unlink($dest_path);
                }
            }
            $stmt->close();
        } else {
            $response['message'] = 'Erro na preparação da query de publicação: ' . $conn->error;
            // Se a preparação falhar, tente remover o arquivo enviado para evitar lixo
            if (file_exists($dest_path)) {
                unlink($dest_path);
            }
        }
    } catch (\Exception $e) { // Captura exceções gerais
        $response['message'] = 'Erro ao criar publicação: ' . $e->getMessage();
        error_log('Erro em upload_post.php: ' . $e->getMessage());
        // Se ocorrer uma exceção, remova o arquivo enviado
        if (file_exists($dest_path)) {
            unlink($dest_path);
        }
    }
} else {
    $response['message'] = 'Houve um erro ao mover o arquivo de mídia enviado para o servidor.';
}

echo json_encode($response);
$conn->close();
?>