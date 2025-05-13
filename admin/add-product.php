<?php include 'includes/header.php'; ?>

<?php
// Get all categories for the form
$query = "SELECT * FROM categories ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $category_id = (int)$_POST['category_id'];
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $image_url = sanitize($_POST['image_url']);
    $stock = (int)$_POST['stock'];
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Product name is required";
    }
    
    if (empty($description)) {
        $errors[] = "Product description is required";
    }
    
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }
    
    if (empty($image_url)) {
        $errors[] = "Image URL is required";
    }
    
    if ($stock < 0) {
        $errors[] = "Stock cannot be negative";
    }
    
    // If no errors, insert product
    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO products (category_id, name, description, price, image_url, stock) VALUES (:category_id, :name, :description, :price, :image_url, :stock)");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':stock', $stock);
        
        if ($stmt->execute()) {
            $success_message = "Product added successfully";
            // Reset form data after successful submission
            $name = $description = $image_url = '';
            $price = $stock = 0;
            $category_id = 1;
        } else {
            $error_message = "Failed to add product";
        }
    }
}
?>

<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Add New Product</h2>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>
    
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success">
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="post" action="">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo (isset($category_id) && $category_id == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo $category['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo isset($description) ? $description : ''; ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price ($)</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo isset($price) ? $price : '0.00'; ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo isset($stock) ? $stock : '0'; ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo isset($image_url) ? $image_url : ''; ?>" required>
                    <div class="form-text">Enter a valid URL for the product image</div>
                </div>
                
                <div class="mt-3">
                    <img id="image-preview" src="<?php echo isset($image_url) ? $image_url : ''; ?>" alt="Product preview" class="img-fluid" style="max-height: 200px; display: <?php echo isset($image_url) && !empty($image_url) ? 'block' : 'none'; ?>;">
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Add Product
            </button>
            <a href="products.php" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
