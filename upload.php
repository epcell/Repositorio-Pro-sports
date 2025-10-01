<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/apache/logs/php_error.log');

session_start();

error_log("upload.php: Script iniciado.");

require_once 'conexaofotos.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_error) {
    $error_message = "upload.php: Erro na conexão com o BD: " . ($conn->connect_error ?? "Variável \$conn não definida ou inválida.");
    error_log($error_message);
    echo json_encode(["success" => false, "message" => "Erro de conexão com o banco de dados. Verifique o servidor MySQL e as credenciais."]);
    exit();
} else {
    error_log("upload.php: Conexão com o BD bem-sucedida.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $modalidade = $_POST['modalidade'] ?? '';

    $id_usuario = $_SESSION['usuario_id'] ?? null;

    if ($id_usuario === null) {
        error_log("upload.php: Usuário não logado. ID de usuário ausente na sessão.");
        echo json_encode(["success" => false, "message" => "Você precisa estar logado para enviar fotos."]);
        $conn->close();
        exit();
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
        error_log("upload.php: Nenhum arquivo foi enviado.");
        echo json_encode(["success" => false, "message" => "Por favor, selecione uma foto para enviar."]);
        $conn->close();
        exit();
    }

    $foto_temp = $_FILES['foto']['tmp_name'];
    $foto_nome_original = $_FILES['foto']['name'];

    $extensao = pathinfo($foto_nome_original, PATHINFO_EXTENSION);
    $novo_nome_arquivo = uniqid('post_', true) . '.' . $extensao;

    // Caminho para o servidor: 'C:\xampp\htdocs' + '/tcc/TCC/uploads/'
    $upload_dir_servidor = $_SERVER['DOCUMENT_ROOT'] . '/tcc/TCC/uploads/';

    // Caminho para o banco de dados e navegador
    $foto_path_db = 'uploads/' . $novo_nome_arquivo;

    // Cria a pasta de uploads se não existir
    if (!is_dir($upload_dir_servidor)) {
        error_log("upload.php: Diretório de uploads não existe. Tentando criar: " . $upload_dir_servidor);
        if (!mkdir($upload_dir_servidor, 0777, true)) {
            error_log("upload.php: Erro ao criar diretório de uploads: " . $upload_dir_servidor . ". Verifique permissões.");
            echo json_encode(["success" => false, "message" => "Erro: Não foi possível criar o diretório de uploads. Verifique permissões."]);
            $conn->close();
            exit();
        } else {
            error_log("upload.php: Diretório de uploads criado com sucesso: " . $upload_dir_servidor);
        }
    } else {
        error_log("upload.php: Diretório de uploads já existe: " . $upload_dir_servidor);
    }

    error_log("upload.php: Tentando mover arquivo de " . $foto_temp . " para " . $upload_dir_servidor . $novo_nome_arquivo);
    if (move_uploaded_file($foto_temp, $upload_dir_servidor . $novo_nome_arquivo)) {
        error_log("upload.php: Arquivo movido com sucesso para: " . $upload_dir_servidor . $novo_nome_arquivo);

        $stmt = $conn->prepare("INSERT INTO fotos_atletas (nome, modalidade, foto_url, id_usuario) VALUES (?, ?, ?, ?)");

        if ($stmt === false) {
            $error_message = "upload.php: Erro na preparação da query SQL: " . $conn->error;
            error_log($error_message);
            echo json_encode(["success" => false, "message" => "Erro interno: Falha na preparação do banco de dados. " . $conn->error]);
            $conn->close();
            exit();
        }

        $stmt->bind_param("sssi", $nome, $modalidade, $foto_path_db, $id_usuario);

        error_log("upload.php: Tentando executar query SQL para (nome: $nome, modalidade: $modalidade, path: $foto_path_db, id_usuario: $id_usuario).");
        if ($stmt->execute()) {
            error_log("upload.php: Dados salvos no BD com sucesso.");
            echo json_encode(["success" => true, "message" => "Foto enviada e salva com sucesso!"]);
        } else {
            $error_message = "upload.php: Erro ao executar query SQL: " . $stmt->error;
            error_log($error_message);
            echo json_encode(["success" => false, "message" => "Erro ao salvar no banco de dados: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        $upload_error = $_FILES['foto']['error'];
        $error_message_upload = "upload.php: Erro ao mover arquivo. Código: {$upload_error}. ";
        switch ($upload_error) {
            case UPLOAD_ERR_INI_SIZE: $error_message_upload .= "O arquivo excede o tamanho máximo permitido pelo servidor (php.ini)."; break;
            case UPLOAD_ERR_FORM_SIZE: $error_message_upload .= "O arquivo excede o tamanho máximo permitido pelo formulário HTML."; break;
            case UPLOAD_ERR_PARTIAL: $error_message_upload .= "O upload foi feito parcialmente."; break;
            case UPLOAD_ERR_NO_FILE: $error_message_upload .= "Nenhum arquivo foi enviado."; break;
            case UPLOAD_ERR_NO_TMP_DIR: $error_message_upload .= "Faltando uma pasta temporária."; break;
            case UPLOAD_ERR_CANT_WRITE: $error_message_upload .= "Falha ao escrever o arquivo no disco (verifique permissões da pasta 'uploads')."; break;
            case UPLOAD_ERR_EXTENSION: $error_message_upload .= "Uma extensão do PHP impediu o upload do arquivo."; break;
            default: $error_message_upload .= "Erro desconhecido."; break;
        }
        error_log($error_message_upload);
        echo json_encode(["success" => false, "message" => "Erro ao fazer upload da foto: " . $error_message_upload]);
    }
} else {
    error_log("upload.php: Método de requisição inválido (não é POST).");
    echo json_encode(["success" => false, "message" => "Método de requisição inválido."]);
}

if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
    error_log("upload.php: Conexão com o BD fechada.");
}
error_log("upload.php: Script finalizado.");
?>