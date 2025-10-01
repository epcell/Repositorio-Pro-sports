<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['id']) && isset($data['path'])) {
        $photoId = $data['id'];
        $photoPath = $data['path'];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "prosportes";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            $response['message'] = "Falha na conexão com o banco de dados: " . $conn->connect_error;
            echo json_encode($response);
            exit();
        }

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("DELETE FROM fotos_atletas WHERE id = ?");
            if ($stmt === false) {
                throw new Exception("Erro ao preparar a declaração SQL: " . $conn->error);
            }
            $stmt->bind_param("i", $photoId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $base_dir = __DIR__ . '/';
                $relative_path = str_replace('http://localhost/tcc/', '', $photoPath);
                $file_to_delete = $base_dir . $relative_path;

                if (file_exists($file_to_delete)) {
                    if (unlink($file_to_delete)) {
                        $conn->commit();
                        $response['success'] = true;
                        $response['message'] = "Foto apagada com sucesso!";
                    } else {
                        throw new Exception("Erro ao apagar o arquivo da foto do servidor.");
                    }
                } else {
                    $conn->commit();
                    $response['success'] = true;
                    $response['message'] = "Foto apagada do banco de dados, mas o arquivo não foi encontrado no servidor.";
                }
            } else {
                throw new Exception("Nenhuma foto encontrada com o ID fornecido ou ela já foi apagada.");
            }

            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = "Erro ao apagar a foto: " . $e->getMessage();
        } finally {
            if (isset($conn) && $conn instanceof mysqli) {
                $conn->close();
            }
        }

    } else {
        $response['message'] = "ID da foto ou caminho não fornecido.";
    }
} else {
    $response['message'] = "Método de requisição inválido.";
}

echo json_encode($response);
?>