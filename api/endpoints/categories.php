
<?php
switch ($method) {
    case 'GET':
        if ($id) {
            // Get single category
            $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category) {
                echo json_encode($category);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Category not found']);
            }
        } else {
            // Get all categories
            $categories = getCategories($db);
            echo json_encode($categories);
        }
        break;

    case 'POST':
        // Create new category
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Category name is required']);
            break;
        }

        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $data['name']);

        if ($stmt->execute()) {
            $category_id = $db->lastInsertId();
            http_response_code(201);
            echo json_encode(['id' => $category_id, 'message' => 'Category created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create category']);
        }
        break;

    case 'PUT':
        // Update category
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Category ID required']);
            break;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Category updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update category']);
        }
        break;

    case 'DELETE':
        // Delete category
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Category ID required']);
            break;
        }

        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Category deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete category']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
