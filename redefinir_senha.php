<?php
session_start();


require 'conexao.php'; 


$mensagem = '';
$erro = false;


if (isset($_GET['token'])) {
    $token = $_GET['token'];

   
    $stmt = $pdo->prepare("SELECT usuario_id FROM recuperacao_senhas WHERE token = :token AND expirado = 0 AND data_expiracao > NOW()");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $usuario_id = $resultado['usuario_id'];

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['nova_senha']) && isset($_POST['confirmar_senha'])) {
                $nova_senha = $_POST['nova_senha'];
                $confirmar_senha = $_POST['confirmar_senha'];

        
                if (strlen($nova_senha) < 6) {
                    $mensagem = "A nova senha deve ter pelo menos 6 caracteres.";
                    $erro = true;
                } elseif ($nova_senha !== $confirmar_senha) {
                    $mensagem = "As senhas não coincidem.";
                    $erro = true;
                }

                if (!$erro) {
           
                    $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

                 
                    $stmt_update = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :usuario_id");
                    $stmt_update->bindParam(':senha', $senha_criptografada);
                    $stmt_update->bindParam(':usuario_id', $usuario_id);
                    if ($stmt_update->execute()) {

                        $stmt_expire = $pdo->prepare("UPDATE recuperacao_senhas SET expirado = 1 WHERE token = :token");
                        $stmt_expire->bindParam(':token', $token);
                        $stmt_expire->execute();

                        $mensagem = "Senha redefinida com sucesso! Você já pode fazer login com sua nova senha.";
                        $erro = false;
                    } else {
                        $mensagem = "Ocorreu um erro ao atualizar sua senha. Tente novamente.";
                        $erro = true;
                    }
                }
            } else {
                $mensagem = "Por favor, preencha todos os campos.";
                $erro = true;
            }
        }
    } else {
        $mensagem = "Token de recuperação inválido ou expirado.";
        $erro = true;
    }
} else {
    $mensagem = "Nenhum token de recuperação fornecido.";
    $erro = true;
}
?>

