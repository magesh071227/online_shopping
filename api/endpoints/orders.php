
<?php
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single order with items
            $query = "SELECT o.*, c.name as customer_name, c.email, c.phone, c.address 
                      FROM orders o 
                      JOIN customers c ON o.customer_id = c.id 
                      WHERE o.id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                // Get order items
                $itemQuery = "SELECT oi.*, p.name as product_name, p.image_url 
                              FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = :order_id";
                $itemStmt = $db->prepare($itemQuery);
                $itemStmt->bindParam(':order_id', $id);
                $itemStmt->execute();
                $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($order);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
            }
        } else {
            // Get all orders
            $query = "SELECT o.*, c.name as customer_name 
                      FROM orders o 
                      JOIN customers c ON o.customer_id = c.id 
                      ORDER BY o.created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($orders);
        }
        break;

    case 'POST':
        // Create new order
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['customer'], $data['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Customer and items are required']);
            break;
        }

        try {
            $db->beginTransaction();

            // Create customer
            $customerQuery = "INSERT INTO customers (name, email, phone, address) 
                              VALUES (:name, :email, :phone, :address)";
            $customerStmt = $db->prepare($customerQuery);
            $customerStmt->bindParam(':name', $data['customer']['name']);
            $customerStmt->bindParam(':email', $data['customer']['email']);
            $customerStmt->bindParam(':phone', $data['customer']['phone']);
            $customerStmt->bindParam(':address', $data['customer']['address']);
            $customerStmt->execute();
            
            $customer_id = $db->lastInsertId();

            // Calculate total
            $total = 0;
            foreach ($data['items'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Create order
            $orderQuery = "INSERT INTO orders (customer_id, total_price, status) 
                           VALUES (:customer_id, :total_price, 'pending')";
            $orderStmt = $db->prepare($orderQuery);
            $orderStmt->bindParam(':customer_id', $customer_id);
            $orderStmt->bindParam(':total_price', $total);
            $orderStmt->execute();
            
            $order_id = $db->lastInsertId();

            // Create order items
            $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES (:order_id, :product_id, :quantity, :price)";
            $itemStmt = $db->prepare($itemQuery);
            
            foreach ($data['items'] as $item) {
                $itemStmt->bindParam(':order_id', $order_id);
                $itemStmt->bindParam(':product_id', $item['product_id']);
                $itemStmt->bindParam(':quantity', $item['quantity']);
                $itemStmt->bindParam(':price', $item['price']);
                $itemStmt->execute();
            }

            $db->commit();
            http_response_code(201);
            echo json_encode(['id' => $order_id, 'message' => 'Order created successfully']);
        } catch (Exception $e) {
            $db->rollback();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create order', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Update order status
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID required']);
            break;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $data['status']);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Order updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
