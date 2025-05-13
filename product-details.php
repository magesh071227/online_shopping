<?php include 'includes/header.php'; ?>

<?php
// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if no product ID
if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

// Get product details
$product = getProduct($db, $product_id);

// Redirect if product not found
if (!$product) {
    header('Location: products.php');
    exit();
}

// Get related products (same category, excluding current product)
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE p.category_id = :category_id AND p.id != :product_id 
          LIMIT 4";
$stmt = $db->prepare($query);
$stmt->bindParam(":category_id", $product['category_id'], PDO::PARAM_INT);
$stmt->bindParam(":product_id", $product_id, PDO::PARAM_INT);
$stmt->execute();
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="py-4">
    <div class="container">
        <div class="mb-3">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <li class="breadcrumb-item"><a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name']; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="product-detail-img">
            </div>
            <div class="col-lg-6 product-detail-info">
                <h1><?php echo $product['name']; ?></h1>
                <div class="product-detail-category">Category: <?php echo $product['category_name']; ?></div>
                <div class="product-detail-price"><?php echo formatPrice($product['price']); ?></div>
                
                <?php
                // Display stock status
                if ($product['stock'] > 10) {
                    echo '<div class="stock-badge in-stock"><i class="fas fa-check-circle"></i> In Stock</div>';
                } elseif ($product['stock'] > 0) {
                    echo '<div class="stock-badge low-stock"><i class="fas fa-exclamation-circle"></i> Low Stock - Only ' . $product['stock'] . ' left</div>';
                } else {
                    echo '<div class="stock-badge out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</div>';
                }
                ?>
                
                <div class="product-detail-description">
                    <?php echo $product['description']; ?>
                </div>
                
                <?php if ($product['stock'] > 0): ?>
                <form action="cart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <div class="input-group" style="width: 130px;">
                            <button type="button" class="btn btn-outline-secondary decrement">-</button>
                            <input type="number" name="quantity" id="quantity" class="form-control text-center quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button type="button" class="btn btn-outline-secondary increment">+</button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg mb-3">
                        <i class="fas fa-shopping-cart me-2"></i> Buy Now
                    </button>
                </form>
                <?php else: ?>
                <button disabled class="btn btn-secondary btn-lg mb-3">
                    <i class="fas fa-ban me-2"></i> Out of Stock
                </button>
                <?php endif; ?>
                
                <div class="mt-4">
                    <h5>Features:</h5>
                    <ul>
                        <li>High-quality materials</li>
                        <li>Comfortable fit</li>
                        <li>Durable construction</li>
                        <li>Stylish design</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <?php if (!empty($related_products)): ?>
        <div class="related-products mt-5">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row">
                <?php foreach ($related_products as $related): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="product-card">
                        <div class="product-img-container">
                            <img src="<?php echo $related['image_url']; ?>" alt="<?php echo $related['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo $related['name']; ?></h3>
                            <div class="product-category"><?php echo $related['category_name']; ?></div>
                            <div class="product-price"><?php echo formatPrice($related['price']); ?></div>
                            <a href="product-details.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
