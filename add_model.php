<?php
// filepath: c:\xampp\htdocs\comissionamento\add_model.php
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
    
    if (!isset($input['model_name']) || empty(trim($input['model_name']))) {
        throw new Exception('Model name é obrigatório');
    }
    
    if (!isset($input['brand_id']) || empty($input['brand_id'])) {
        throw new Exception('Brand é obrigatório');
    }
    
    if (!isset($input['characteristics']) || empty(trim($input['characteristics']))) {
        throw new Exception('Características são obrigatórias');
    }

    $modelName = trim($input['model_name']);
    $brandId = $input['brand_id'];
    $characteristics = trim($input['characteristics']);

    // Verificar se a marca existe
    $brandCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM pv_module_brands WHERE id = ?");
    $brandCheckStmt->execute([$brandId]);
    
    if ($brandCheckStmt->fetchColumn() == 0) {
        throw new Exception('Marca selecionada não existe');
    }

    // Verificar se o modelo já existe para esta marca
    $modelCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM pv_module_models WHERE model_name = ? AND brand_id = ?");
    $modelCheckStmt->execute([$modelName, $brandId]);
    
    if ($modelCheckStmt->fetchColumn() > 0) {
        throw new Exception('Este modelo já existe para esta marca');
    }

    // Inserir novo modelo
    $stmt = $pdo->prepare("INSERT INTO pv_module_models (model_name, brand_id, characteristics) VALUES (?, ?, ?)");
    $stmt->execute([$modelName, $brandId, $characteristics]);

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Modelo adicionado com sucesso',
        'model_id' => $pdo->lastInsertId()
    ]);

} catch (Exception $e) {
    // Resposta de erro
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>