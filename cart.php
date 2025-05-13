<?php 
include 'includes/header.php'; 

// Process cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($product_id > 0) {
        switch ($action) {
            case 'add':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                addToCart($product_id, $quantity);
                
                // Redirect directly to checkout
                header('Location: checkout.php');
                exit();
                break;
                
            case 'update':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
                updateCartItem($product_id, $quantity);
                break;
                
            case 'remove':
                removeFromCart($product_id);
                break;
        }
    }
    
    // Redirect to prevent form resubmission (for update/remove actions)
    header('Location: cart.php');
    exit();
}

// Get cart items
$cart_items = getCartItems($db);
$cart_total = getCartTotal($cart_items);
?>

<section>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Shopping Cart</h1>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
        
        <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="products.php">Continue shopping</a>.
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo $item['product']['image_url']; ?>" alt="<?php echo $item['product']['name']; ?>" class="cart-item-img">
                        </div>
                        <div class="col-md-4">
                            <h5><a href="product-details.php?id=<?php echo $item['product']['id']; ?>"><?php echo $item['product']['name']; ?></a></h5>
                            <div class="text-muted"><?php echo $item['product']['category_name']; ?></div>
                            <div><?php echo formatPrice($item['product']['price']); ?></div>
                        </div>
                        <div class="col-md-3">
                            <form action="cart.php" method="post" class="update-cart-form">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <input type="hidden" name="action" value="update">
                                <div class="input-group mb-2">
                                    <button type="button" class="btn btn-outline-secondary decrement">-</button>
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" class="form-control text-center quantity-input">
                                    <button type="button" class="btn btn-outline-secondary increment">+</button>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="fw-bold mb-2"><?php echo formatPrice($item['subtotal']); ?></div>
                            <form action="cart.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <input type="hidden" name="action" value="remove">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="order-summary">
                    <h4 class="mb-3">Order Summary</h4>
                    <div class="order-summary-item">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($cart_total); ?></span>
                    </div>
                    <div class="order-summary-item">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="order-total">
                        <span>Total</span>
                        <span><?php echo formatPrice($cart_total); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary w-100 mt-3">Proceed to Checkout</a>
                    <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
