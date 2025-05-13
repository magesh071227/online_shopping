<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1>Step into Style</h1>
        <p class="mx-auto">Discover the perfect shoes for every occasion at ShoesHub. From casual comfort to professional elegance, we've got you covered.</p>
        <a href="products.php" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
</section>

<!-- Featured Products Section -->
<section>
    <div class="container">
        <h2 class="text-center mb-4">Featured Shoes</h2>
        <div class="row">
            <?php
            // Get featured products (limited to 4)
            $featured_products = getProducts($db, null, 4);
            
            foreach ($featured_products as $product):
            ?>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="product-card">
                    <div class="product-img-container">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <div class="product-category"><?php echo $product['category_name']; ?></div>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary w-100 mb-2">View Details</a>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="action" value="add">
                            <button type="submit" class="btn btn-primary w-100">Buy Now</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-dark">View All Shoes</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 my-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="products.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-shoe-prints fa-3x mb-3"></i>
                            <h5 class="card-title"><?php echo $category['name']; ?></h5>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose ShoesHub?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-shipping-fast fa-3x mb-3 text-primary"></i>
                    <h4>Fast Shipping</h4>
                    <p>Get your shoes delivered quickly to your doorstep with our express delivery options.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-undo fa-3x mb-3 text-primary"></i>
                    <h4>Easy Returns</h4>
                    <p>Not the right fit? No problem! Our hassle-free return policy has you covered.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-shield-alt fa-3x mb-3 text-primary"></i>
                    <h4>Secure Shopping</h4>
                    <p>Shop with confidence knowing your information is protected with secure encryption.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
