
<?php
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single product
            $product = getProduct($db, $id);
            if ($product) {
                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
            }
        } else {
            // Get all products with optional category filter
            $category_id = $_GET['category'] ?? null;
            $limit = $_GET['limit'] ?? null;
            $products = getProducts($db, $category_id, $limit);
            echo json_encode($products);
        }
        break;

    case 'POST':
        // Create new product
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['name'], $data['price'], $data['category_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            break;
        }

        $query = "INSERT INTO products (name, description, price, category_id, image_url, stock) 
                  VALUES (:name, :description, :price, :category_id, :image_url, :stock)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description'] ?? '');
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':image_url', $data['image_url'] ?? '');
        $stmt->bindParam(':stock', $data['stock'] ?? 0);

        if ($stmt->execute()) {
            $product_id = $db->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $product_id, 'message' => 'Product created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product']);
        }
        break;

    case 'PUT':
        // Update product
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID required']);
            break;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = "UPDATE products SET name = :name, description = :description, price = :price, 
                  category_id = :category_id, image_url = :image_url, stock = :stock 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':image_url', $data['image_url']);
        $stmt->bindParam(':stock', $data['stock']);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update product']);
        }
        break;

    case 'DELETE':
        // Delete product
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID required']);
            break;
        }

        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
