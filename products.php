<?php include 'includes/header.php'; ?>

<?php
// Get category filter if exists
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Get all products with optional category filter
$products = getProducts($db, $category_id);
?>

<section>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Our Shoes Collection</h1>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        
        <!-- Category Filter Pills -->
        <div class="category-pills">
            <a href="products.php" class="category-pill <?php echo $category_id ? '' : 'active'; ?>" data-category="all">All Shoes</a>
            <?php foreach ($categories as $category): ?>
            <a href="products.php?category=<?php echo $category['id']; ?>" 
               class="category-pill <?php echo $category_id == $category['id'] ? 'active' : ''; ?>"
               data-category="<?php echo $category['id']; ?>">
                <?php echo $category['name']; ?>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="row">
            <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No products found in this category. Please check back later.
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4 mb-4 product-card-wrapper" data-category="<?php echo $product['category_id']; ?>">
                    <div class="product-card">
                        <div class="product-img-container">
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><?php echo $product['name']; ?></h3>
                            <div class="product-category"><?php echo $product['category_name']; ?></div>
                            <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary w-100 mb-2">View Details</a>
                            <form action="cart.php" method="post" class="mb-2">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn btn-primary w-100">Buy Now</button>
                            </form>
                            <form action="cart.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn btn-outline-primary w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
