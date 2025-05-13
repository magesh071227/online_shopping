<?php 
include 'includes/header.php'; 

// Redirect if cart is empty
$cart_items = getCartItems($db);
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$cart_total = getCartTotal($cart_items);
?>

<section>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Checkout</h1>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Cart
            </a>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Shipping & Billing Information</h4>
                        
                        <form id="checkout-form" action="process-order.php" method="post">
                            <div class="row checkout-form">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <div class="invalid-feedback">Please enter your full name.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                    <div class="invalid-feedback">Please enter your phone number.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Street Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                    <div class="invalid-feedback">Please enter your address.</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                    <div class="invalid-feedback">Please enter your city.</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state" name="state" required>
                                    <div class="invalid-feedback">Please enter your state/province.</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="zip" class="form-label">Zip/Postal Code</label>
                                    <input type="text" class="form-control" id="zip" name="zip" required>
                                    <div class="invalid-feedback">Please enter your zip/postal code.</div>
                                </div>
                                
                                <div class="col-12 mb-4">
                                    <label for="notes" class="form-label">Order Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                                
                                <h4 class="mb-3">Payment Information</h4>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="card_name" class="form-label">Name on Card</label>
                                    <input type="text" class="form-control" id="card_name" name="card_name" required>
                                    <div class="invalid-feedback">Please enter name on card.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" required>
                                    <div class="invalid-feedback">Please enter a valid card number.</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="expiry_month" class="form-label">Expiry Month</label>
                                    <select class="form-select" id="expiry_month" name="expiry_month" required>
                                        <option value="">Select Month</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select expiry month.</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="expiry_year" class="form-label">Expiry Year</label>
                                    <select class="form-select" id="expiry_year" name="expiry_year" required>
                                        <option value="">Select Year</option>
                                        <?php 
                                        $current_year = date('Y');
                                        for ($i = $current_year; $i <= $current_year + 10; $i++): 
                                        ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select expiry year.</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" required>
                                    <div class="invalid-feedback">Please enter CVV.</div>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Place Order</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="order-summary mb-4">
                    <h4 class="mb-3">Order Summary</h4>
                    <?php foreach ($cart_items as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold"><?php echo $item['quantity']; ?> x</span> <?php echo $item['product']['name']; ?>
                        </div>
                        <div><?php echo formatPrice($item['subtotal']); ?></div>
                    </div>
                    <?php endforeach; ?>
                    <hr>
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
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Secure Checkout</h5>
                        <p class="card-text">
                            <i class="fas fa-lock me-2"></i> All transactions are secure and encrypted.
                        </p>
                        <p class="card-text">
                            <i class="fas fa-shipping-fast me-2"></i> Free shipping on all orders.
                        </p>
                        <p class="card-text mb-0">
                            <i class="fas fa-undo me-2"></i> Easy 30-day returns.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
