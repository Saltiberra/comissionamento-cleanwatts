<?php
// Configuração da conexão com o banco de dados
$host = '127.0.0.1';
$user = 'root';
$password = ''; // Substitua pela senha do seu banco de dados
$dbname = 'comissionamento';

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die('Erro de conexão: ' . $conn->connect_error);
}

// Consultar dados da tabela epcs
$sql = 'SELECT id, name FROM epcs';
$result = $conn->query($sql);

// Verificar se há resultados
if ($result->num_rows > 0) {
    $epcs = [];
    while ($row = $result->fetch_assoc()) {
        $epcs[] = $row;
    }
    echo json_encode($epcs);
} else {
    echo json_encode([]);
}

// Fechar conexão
$conn->close();
?>
