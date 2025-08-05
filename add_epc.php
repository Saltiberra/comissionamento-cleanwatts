<?php
// filepath: c:\xampp\htdocs\comissionamento\add_epc.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuração da base de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comissionamento";

try {
    // Criar conexão
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    // Ler dados JSON do corpo da requisição
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validar dados
    if (!isset($data['name']) || empty(trim($data['name']))) {
        throw new Exception('Nome do EPC é obrigatório');
    }

    $epcName = trim($data['name']);

    // Verificar se o EPC já existe
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM epcs WHERE name = ?");
    $checkStmt->execute([$epcName]);
    
    if ($checkStmt->fetchColumn() > 0) {
        throw new Exception('Este EPC já existe na base de dados');
    }

    // Inserir novo EPC (apenas com name)
    $stmt = $pdo->prepare("INSERT INTO epcs (name) VALUES (?)");
    $stmt->execute([$epcName]);

    // Obter o ID do EPC inserido
    $epcId = $pdo->lastInsertId();

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'EPC adicionado com sucesso',
        'epc_id' => $epcId,
        'epc_name' => $epcName
    ]);

} catch (Exception $e) {
    // Resposta de erro
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>