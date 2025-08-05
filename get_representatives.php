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

// Obter o ID do EPC da requisição
$epc_id = isset($_GET['epc_id']) ? intval($_GET['epc_id']) : 0;

// Log de depuração
error_log("EPC ID recebido: $epc_id");

// Consultar dados da tabela representatives com base no EPC selecionado
$sql = "SELECT id, name, phone FROM representatives WHERE epc_id = $epc_id";
error_log("Consulta SQL: $sql");
$result = $conn->query($sql);

// Verificar se há resultados
if ($result->num_rows > 0) {
    $representatives = [];
    while ($row = $result->fetch_assoc()) {
        $representatives[] = $row;
    }
    error_log("Representantes encontrados: " . json_encode($representatives));
    echo json_encode($representatives);
} else {
    error_log("Nenhum representante encontrado para EPC ID: $epc_id");
    echo json_encode([]);
}

// Fechar conexão
$conn->close();
?>
