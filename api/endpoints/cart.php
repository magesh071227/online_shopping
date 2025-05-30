
<?php
session_start();

switch ($method) {
    case 'GET':
        // Get cart items
        $cart_items = getCartItems($db);
        $total = getCartTotal($cart_items);
        $count = getCartCount();
        
        echo json_encode([
            'items' => $cart_items,
            'total' => $total,
            'count' => $count
        ]);
        break;

    case 'POST':
        // Add item to cart
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['product_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID is required']);
            break;
        }

        $quantity = $data['quantity'] ?? 1;
        addToCart($data['product_id'], $quantity);
        
        echo json_encode(['message' => 'Item added to cart successfully']);
        break;

    case 'PUT':
        // Update cart item quantity
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['product_id'], $data['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID and quantity are required']);
            break;
        }

        updateCartItem($data['product_id'], $data['quantity']);
        
        echo json_encode(['message' => 'Cart updated successfully']);
        break;

    case 'DELETE':
        if ($id) {
            // Remove specific item from cart
            removeFromCart($id);
            echo json_encode(['message' => 'Item removed from cart']);
        } else {
            // Clear entire cart
            clearCart();
            echo json_encode(['message' => 'Cart cleared successfully']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
