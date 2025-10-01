<?php
session_start();

include_once 'conexao_login.php'; 


header('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
   
    $tipo_usuario = filter_input(INPUT_POST, 'perfil', FILTER_SANITIZE_STRING); 

    if (empty($usuario) || empty($email) || empty($senha) || empty($tipo_usuario)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Formato de e-mail inválido.']);
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt_check_usuario = $conn->prepare("SELECT usuario FROM usuarios WHERE usuario = ?");
    if ($stmt_check_usuario) {
        $stmt_check_usuario->bind_param("s", $usuario);
        $stmt_check_usuario->execute();
        $stmt_check_usuario->store_result();
        if ($stmt_check_usuario->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Este nome de usuário já está em uso.']);
            $stmt_check_usuario->close();
            exit();
        }
        $stmt_check_usuario->close();
    }

    $stmt_check_email = $conn->prepare("SELECT email FROM usuarios WHERE email = ?");
    if ($stmt_check_email) {
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $stmt_check_email->store_result();
        if ($stmt_check_email->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Este e-mail já está cadastrado.']);
            $stmt_check_email->close();
            exit();
        }
        $stmt_check_email->close();
    }


    $stmt_insert = $conn->prepare("INSERT INTO usuarios (usuario, email, senha, tipo_usuario) VALUES (?, ?, ?, ?)");
    if ($stmt_insert) {
        $stmt_insert->bind_param("ssss", $usuario, $email, $senhaHash, $tipo_usuario);
        if ($stmt_insert->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $usuario; 
            $_SESSION['tipo_usuario'] = $tipo_usuario;

       
            if ($tipo_usuario === 'atleta') {
                $stmt_perfil = $conn->prepare("INSERT INTO perfis_atletas (user_id, nome_completo, username, modalidade) VALUES (?, ?, ?, ?)");
                $nome_completo_padrao = $usuario;
                $modalidade_padrao = 'Não Definida'; 
                $stmt_perfil->bind_param("isss", $_SESSION['user_id'], $nome_completo_padrao, $usuario, $modalidade_padrao);
                if (!$stmt_perfil->execute()) {
                    error_log("Erro ao criar perfil de atleta: " . $stmt_perfil->error);
                }
                $stmt_perfil->close();
            }

            echo json_encode(['success' => true, 'perfil' => $tipo_usuario, 'message' => 'Cadastro realizado com sucesso!']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar: ' . $stmt_insert->error]);
            exit();
        }
        $stmt_insert->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro na preparação da query de inserção: ' . $conn->error]);
        exit();
    }

} else {
 
    http_response_code(405); 
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit();
}

$conn->close();
?>