
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/', '', $path);
$pathSegments = explode('/', trim($path, '/'));

// Connect to database
$database = new Database();
$db = $database->getConnection();

// Route the request
$endpoint = $pathSegments[0] ?? '';
$id = $pathSegments[1] ?? null;

try {
    switch ($endpoint) {
        case 'products':
            require_once 'endpoints/products.php';
            break;
        case 'categories':
            require_once 'endpoints/categories.php';
            break;
        case 'orders':
            require_once 'endpoints/orders.php';
            break;
        case 'customers':
            require_once 'endpoints/customers.php';
            break;
        case 'cart':
            require_once 'endpoints/cart.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
}
?>
