<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$api_script_path = __DIR__ . '/api/get_featured_athletes.php';

if (file_exists($api_script_path)) {
    echo "O arquivo 'get_featured_athletes.php' foi ENCONTRADO em: " . htmlspecialchars($api_script_path) . "<br><br>";
    echo "Conteúdo do arquivo (primeiras 200 chars):<br>";
    echo "<pre>" . htmlspecialchars(substr(file_get_contents($api_script_path), 0, 200)) . "</pre>";
    echo "<br>Isso significa que o PHP consegue vê-lo.<br>";
    echo "Agora, tente acessar a URL diretamente no seu navegador:<br>";
    echo "<strong>http://localhost/tcc/tcc/api/get_featured_athletes.php</strong> (substitua 'tcc/tcc/' pelo caminho real do seu projeto)<br><br>";
    echo "Se o navegador der 404, o problema é do Apache, não do PHP/caminho.<br>";
} else {
    echo "ERRO: O arquivo 'get_featured_athletes.php' NÃO foi encontrado em: " . htmlspecialchars($api_script_path) . "<br>";
    echo "Verifique a estrutura das suas pastas.<br>";
    echo "A pasta atual é: " . htmlspecialchars(__DIR__) . "<br>";
    echo "Você esperava que 'api/get_featured_athletes.php' estivesse em: " . htmlspecialchars(__DIR__ . '/api/') . "<br>";
}

if (file_exists($api_script_path)) {
    echo "<hr>Tentando EXECUTAR o script get_featured_athletes.php...<br>";
    include $api_script_path; 
    echo "<br>Execução tentada. Verifique o output acima para erros PHP.";
}
?>
