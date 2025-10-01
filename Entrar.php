<?php
session_start();
include_once 'conexao_login.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_digitado = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha_digitada = $_POST['senha'] ?? '';
    $perfil_selecionado = $_POST['perfil'] ?? '';

    if (empty($email_digitado) || empty($senha_digitada) || empty($perfil_selecionado)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos e selecione um perfil.']);
        $conn->close();
        exit();
    }

    $stmt = $conn->prepare("SELECT id, usuario, email, senha, tipo_usuario FROM usuarios WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email_digitado);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($senha_digitada, $user['senha'])) {
                if ($user['tipo_usuario'] !== $perfil_selecionado) {
                    echo json_encode(['success' => false, 'message' => 'O perfil selecionado não corresponde ao tipo de conta do usuário.']);
                    $stmt->close();
                    $conn->close();
                    exit();
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['usuario'];
                $_SESSION['tipo_usuario'] = $user['tipo_usuario'];

                echo json_encode(['success' => true, 'tipo_usuario' => $user['tipo_usuario'], 'message' => 'Login bem-sucedido!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Credenciais inválidas. Verifique seu e-mail e senha.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciais inválidas. Verifique seu e-mail e senha.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro interno na preparação da query: ' . $conn->error]);
    }
    $conn->close();
    exit();
}
?>