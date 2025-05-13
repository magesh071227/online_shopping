<?php
include 'includes/header.php';

// Redirect if not a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

// Get cart items
$cart_items = getCartItems($db);
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

$cart_total = getCartTotal($cart_items);

// Process customer information
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$address = sanitize($_POST['address']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip = sanitize($_POST['zip']);
$notes = isset($_POST['notes']) ? sanitize($_POST['notes']) : '';

// Combine address info
$full_address = $address . ', ' . $city . ', ' . $state . ' ' . $zip;

// Store customer info in database
$stmt = $db->prepare("INSERT INTO customers (name, email, phone, address) VALUES (:name, :email, :phone, :address)");
$stmt->bindParam(':name', $name);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':phone', $phone);
$stmt->bindParam(':address', $full_address);
$stmt->execute();
$customer_id = $db->lastInsertId();

// Create order record
$stmt = $db->prepare("INSERT INTO orders (customer_id, total_price) VALUES (:customer_id, :total_price)");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->bindParam(':total_price', $cart_total);
$stmt->execute();
$order_id = $db->lastInsertId();

// Store order items
$stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");

foreach ($cart_items as $item) {
    $product_id = $item['product']['id'];
    $quantity = $item['quantity'];
    $price = $item['product']['price'];
    
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':price', $price);
    $stmt->execute();
    
    // Update product stock
    $update_stmt = $db->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
    $update_stmt->bindParam(':quantity', $quantity);
    $update_stmt->bindParam(':product_id', $product_id);
    $update_stmt->execute();
}

// Generate order reference
$order_ref = generateOrderRef();

// Clear cart after successful order
clearCart();
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">Order Placed Successfully!</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success fa-5x"></i>
                        </div>
                        <h4>Thank you for your order, <?php echo $name; ?>!</h4>
                        <p class="lead">Your order has been received and is being processed.</p>
                        
                        <div class="alert alert-info my-4">
                            <p class="mb-1"><strong>Order Reference:</strong> <?php echo $order_ref; ?></p>
                            <p class="mb-0"><strong>Order Total:</strong> <?php echo formatPrice($cart_total); ?></p>
                        </div>
                        
                        <p>A confirmation email has been sent to <strong><?php echo $email; ?></strong> with the order details.</p>
                        
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">Return to Home</a>
                            <a href="products.php" class="btn btn-outline-primary ms-2">Continue Shopping</a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
