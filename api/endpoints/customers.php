
<?php
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single customer
            $stmt = $db->prepare("SELECT * FROM customers WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($customer) {
                echo json_encode($customer);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Customer not found']);
            }
        } else {
            // Get all customers
            $stmt = $db->prepare("SELECT * FROM customers ORDER BY created_at DESC");
            $stmt->execute();
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($customers);
        }
        break;

    case 'POST':
        // Create new customer
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['name'], $data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and email are required']);
            break;
        }

        $query = "INSERT INTO customers (name, email, phone, address) 
                  VALUES (:name, :email, :phone, :address)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone'] ?? '');
        $stmt->bindParam(':address', $data['address'] ?? '');

        if ($stmt->execute()) {
            $customer_id = $db->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $customer_id, 'message' => 'Customer created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create customer']);
        }
        break;

    case 'PUT':
        // Update customer
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Customer ID required']);
            break;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = "UPDATE customers SET name = :name, email = :email, phone = :phone, address = :address 
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Customer updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update customer']);
        }
        break;

    case 'DELETE':
        // Delete customer
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Customer ID required']);
            break;
        }

        $stmt = $db->prepare("DELETE FROM customers WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Customer deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete customer']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
