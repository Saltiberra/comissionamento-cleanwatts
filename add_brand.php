<?php
// filepath: c:\xampp\htdocs\comissionamento\add_brand.php
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

    // Receber dados JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['brand_name']) || empty(trim($input['brand_name']))) {
        throw new Exception('Brand name é obrigatório');
    }

    $brandName = trim($input['brand_name']);

    // Verificar se a marca já existe
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM pv_module_brands WHERE brand_name = ?");
    $checkStmt->execute([$brandName]);
    
    if ($checkStmt->fetchColumn() > 0) {
        throw new Exception('Esta marca já existe');
    }

    // Inserir nova marca
    $stmt = $pdo->prepare("INSERT INTO pv_module_brands (brand_name) VALUES (?)");
    $stmt->execute([$brandName]);

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Marca adicionada com sucesso',
        'brand_id' => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    // Resposta de erro
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>