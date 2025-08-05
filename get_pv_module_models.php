<?php
// filepath: c:\xampp\htdocs\comissionamento\get_pv_module_models.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
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

    // Verificar se brand_id foi fornecido
    if (!isset($_GET['brand_id']) || empty($_GET['brand_id'])) {
        throw new Exception('Brand ID é obrigatório');
    }

    $brandId = $_GET['brand_id'];

    // Buscar modelos da marca específica
    $stmt = $pdo->prepare("SELECT id, model_name, characteristics FROM pv_module_models WHERE brand_id = ? ORDER BY model_name");
    $stmt->execute([$brandId]);

    // Obter resultados
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Resposta de sucesso
    echo json_encode($models);

} catch (Exception $e) {
    // Resposta de erro
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>