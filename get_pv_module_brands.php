<?php
// filepath: c:\xampp\htdocs\comissionamento\get_pv_module_brands.php
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

    // Buscar todas as marcas de painéis PV
    $stmt = $pdo->prepare("SELECT id, brand_name FROM pv_module_brands ORDER BY brand_name");
    $stmt->execute();

    // Obter resultados
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Resposta de sucesso
    echo json_encode($brands);

} catch (Exception $e) {
    // Resposta de erro
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>