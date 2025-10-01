<?php
session_start();
header('Content-Type: application/json');

include_once 'conexao_login.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Usuário não autenticado.';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];

$nome_completo = $_POST['athleteName'] ?? null;
$nome_usuario = $_POST['username'] ?? null;
$biografia = $_POST['bio'] ?? null;
$modalidade_esportiva = $_POST['sport'] ?? null;

$foto_perfil_url = null;

if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
    $fileName = $_FILES['profilePicture']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = uniqid() . '-' . time() . '.' . $fileExtension;
        $uploadFileDir = './uploads/profile_pictures/';

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $foto_perfil_url = $dest_path;

            $stmt_old_pic = $conn->prepare("SELECT foto_perfil_url FROM perfis_atletas WHERE user_id = ?");
            if ($stmt_old_pic) {
                $stmt_old_pic->bind_param("i", $userId);
                $stmt_old_pic->execute();
                $result_old_pic = $stmt_old_pic->get_result();
                $old_pic_path = $result_old_pic->fetch_column();
                $stmt_old_pic->close();

                if ($old_pic_path && file_exists($old_pic_path) && $old_pic_path !== $foto_perfil_url) {
                    unlink($old_pic_path);
                }
            }
        } else {
            $response['message'] = 'Erro ao mover o arquivo de foto de perfil enviado.';
            echo json_encode($response);
            $conn->close();
            exit();
        }
    } else {
        $response['message'] = 'Tipo de arquivo para foto de perfil não permitido. Use JPG, PNG ou GIF.';
        echo json_encode($response);
        $conn->close();
        exit();
    }
}

try {
    $updateFields = [];
    $bindTypes = '';
    $bindValues = [];

    if ($nome_completo !== null) { $updateFields[] = 'nome_completo = ?'; $bindTypes .= 's'; $bindValues[] = &$nome_completo; }
    if ($nome_usuario !== null) { $updateFields[] = 'username = ?'; $bindTypes .= 's'; $bindValues[] = &$nome_usuario; }
    if ($biografia !== null) { $updateFields[] = 'bio = ?'; $bindTypes .= 's'; $bindValues[] = &$biografia; }
    if ($modalidade_esportiva !== null) { $updateFields[] = 'modalidade = ?'; $bindTypes .= 's'; $bindValues[] = &$modalidade_esportiva; }
    if ($foto_perfil_url !== null) { $updateFields[] = 'foto_perfil_url = ?'; $bindTypes .= 's'; $bindValues[] = &$foto_perfil_url; }

    if (empty($updateFields)) {
        $response['message'] = 'Nenhum dado para atualizar.';
        echo json_encode($response);
        $conn->close();
        exit();
    }

    $updateFields[] = 'data_atualizacao = NOW()';
    
    $sql = "UPDATE perfis_atletas SET " . implode(', ', $updateFields) . " WHERE user_id = ?";
    $bindTypes .= 'i';
    $bindValues[] = &$userId;

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindValues));
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Perfil atualizado com sucesso!';
        } else {
            $response['success'] = true;
            $response['message'] = 'Nenhum dado do perfil foi alterado ou perfil não encontrado.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Erro na preparação da query de atualização: ' . $conn->error;
    }
} catch (\Exception $e) {
    $response['message'] = 'Erro ao atualizar perfil: ' . $e->getMessage();
    error_log('Erro em update_profile_data.php: ' . $e->getMessage());
}

echo json_encode($response);
$conn->close();
?>
