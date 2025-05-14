<?php
ob_start();
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Process cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if ($product_id > 0) {
        switch ($action) {
            case 'add':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                addToCart($product_id, $quantity);
                header('Location: checkout.php');
                exit();
                break;

            case 'update':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
                updateCartItem($product_id, $quantity);
                header('Location: cart.php');
                exit();
                break;

            case 'remove':
                removeFromCart($product_id);
                header('Location: cart.php');
                exit();
                break;
        }
    }
}

// Get cart items
$cart_items = getCartItems($db);
$cart_total = getCartTotal($cart_items);

include 'includes/header.php';
?>

<!-- Your HTML content here -->

<?php
include 'includes/footer.php';
ob_end_flush();
?>
