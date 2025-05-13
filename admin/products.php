<?php include 'includes/header.php'; ?>

<?php
// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    
    $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $product_id);
    
    if ($stmt->execute()) {
        $success_message = "Product deleted successfully";
    } else {
        $error_message = "Failed to delete product";
    }
}

// Get all products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Products Management</h2>
        <a href="add-product.php" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Product
        </a>
    </div>
    
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
        <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="text-center">No products found</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" width="50" height="50" style="object-fit: cover;">
                        </td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category_name']; ?></td>
                        <td><?php echo formatPrice($product['price']); ?></td>
                        <td>
                            <?php if ($product['stock'] > 10): ?>
                            <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                            <?php elseif ($product['stock'] > 0): ?>
                            <span class="badge bg-warning"><?php echo $product['stock']; ?></span>
                            <?php else: ?>
                            <span class="badge bg-danger">Out of stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
