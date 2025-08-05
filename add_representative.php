<?php
// filepath: c:\xampp\htdocs\comissionamento\add_representative.php
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
        throw new Exception('Nome do representante é obrigatório');
    }

    if (!isset($data['contact']) || empty(trim($data['contact']))) {
        throw new Exception('Contacto do representante é obrigatório');
    }

    if (!isset($data['epc_id']) || empty($data['epc_id'])) {
        throw new Exception('EPC é obrigatório');
    }

    $representativeName = trim($data['name']);
    $representativeContact = trim($data['contact']);
    $epcId = $data['epc_id'];

    // Verificar se o EPC existe
    $checkEpcStmt = $pdo->prepare("SELECT COUNT(*) FROM epcs WHERE id = ?");
    $checkEpcStmt->execute([$epcId]);
    
    if ($checkEpcStmt->fetchColumn() == 0) {
        throw new Exception('EPC não encontrado');
    }

    // Verificar se o representante já existe para este EPC
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM representatives WHERE name = ? AND epc_id = ?");
    $checkStmt->execute([$representativeName, $epcId]);
    
    if ($checkStmt->fetchColumn() > 0) {
        throw new Exception('Este representante já existe para este EPC');
    }

    // Inserir novo representante
    $stmt = $pdo->prepare("INSERT INTO representatives (name, phone, epc_id) VALUES (?, ?, ?)");
    $stmt->execute([$representativeName, $representativeContact, $epcId]);

    // Obter o ID do representante inserido
    $representativeId = $pdo->lastInsertId();

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Representante adicionado com sucesso',
        'representative_id' => $representativeId,
        'representative_name' => $representativeName,
        'representative_phone' => $representativeContact,
        'epc_id' => $epcId
    ]);

} catch (Exception $e) {
    // Resposta de erro
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>