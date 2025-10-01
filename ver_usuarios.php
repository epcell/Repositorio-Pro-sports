<?php
include_once 'conexao.php';

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Usuários cadastrados:</h2><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><strong>Usuário:</strong> " . $row["usuario"] . " | <strong>Email:</strong> " . $row["email"] . " | <strong>Tipo:</strong> " . $row["tipo_usuario"] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum usuário encontrado.";
}
?>
