<?php
session_start();
include_once 'conexao_login.php';

header('Content-Type: application/json'); 


$data = json_decode(file_get_contents('php://input'), true);


if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos ou vazios recebidos.']);
    exit();
}

$user_id = $data['user_id'] ?? null;
$nome_completo = $data['nomeCompleto'] ?? null; 
$bio = $data['bio'] ?? null;
$foto_perfil_url = $data['fotoPerfilUrl'] ?? null;
$modalidade = $data['modalidade'] ?? null;
$email_contato = $data['emailContato'] ?? null;
$whatsapp = $data['whatsapp'] ?? null;
$instagram = $data['instagram'] ?? null;


if ($_SESSION['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'ID de usuário inválido ou não autenticado.']);
    exit();
}


if (empty($nome_completo) || empty($username) || empty($modalidade)) { 
    echo json_encode(['success' => false, 'message' => 'Nome, usuário ou modalidade são obrigatórios.']);
    exit();
}


$nome_completo = htmlspecialchars($nome_completo);
$username = htmlspecialchars($username);
$bio = htmlspecialchars($bio); 
$modalidade = htmlspecialchars($modalidade);
$email_contato = filter_var($email_contato, FILTER_SANITIZE_EMAIL);
$whatsapp = htmlspecialchars($whatsapp);
$instagram = htmlspecialchars($instagram);


$sql = "UPDATE perfis_atletas SET 
            nome_completo = ?, 
            username = ?, 
            bio = ?, 
            foto_perfil_url = ?, 
            modalidade = ?, 
            email_contato = ?, 
            whatsapp = ?, 
            instagram = ? 
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssssssssi", 
        $nome_completo, 
        $username, 
        $bio,
        $foto_perfil_url, 
        $modalidade, 
        $email_contato, 
        $whatsapp, 
        $instagram, 
        $user_id
    );

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma alteração foi feita no perfil ou perfil não encontrado.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao executar a atualização: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação da query SQL: ' . $conn->error]);
}

$conn->close();
?>