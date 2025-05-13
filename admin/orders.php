<?php include 'includes/header.php'; ?>

<?php
// Update order status if requested
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $order_id);
    
    if ($stmt->execute()) {
        $success_message = "Order status updated successfully";
    } else {
        $error_message = "Failed to update order status";
    }
}

// Get all orders with customer details
$query = "SELECT o.*, c.name as customer_name, c.email 
          FROM orders o 
          JOIN customers c ON o.customer_id = c.id 
          ORDER BY o.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get order items for detailed view if an order is selected
$selected_order = null;
$order_items = [];

if (isset($_GET['view']) && $_GET['view'] > 0) {
    $order_id = (int)$_GET['view'];
    
    // Get order details
    $stmt = $db->prepare("SELECT o.*, c.name as customer_name, c.email, c.phone, c.address 
                         FROM orders o 
                         JOIN customers c ON o.customer_id = c.id 
                         WHERE o.id = :id");
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();
    $selected_order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($selected_order) {
        // Get order items
        $stmt = $db->prepare("SELECT oi.*, p.name as product_name, p.image_url 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="admin-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Orders Management</h2>
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
    
    <?php if ($selected_order): ?>
    <div class="mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #<?php echo $selected_order['id']; ?> Details</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-secondary">Back to All Orders</a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p><strong>Name:</strong> <?php echo $selected_order['customer_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $selected_order['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $selected_order['phone']; ?></p>
                        <p><strong>Address:</strong> <?php echo $selected_order['address']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($selected_order['created_at'])); ?></p>
                        <p><strong>Order Total:</strong> <?php echo formatPrice($selected_order['total_price']); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge <?php 
                                switch($selected_order['status']) {
                                    case 'pending': echo 'bg-warning'; break;
                                    case 'processing': echo 'bg-info'; break;
                                    case 'shipped': echo 'bg-primary'; break;
                                    case 'delivered': echo 'bg-success'; break;
                                    default: echo 'bg-secondary';
                                }
                            ?>">
                                <?php echo ucfirst($selected_order['status']); ?>
                            </span>
                        </p>
                        
                        <form action="" method="post" class="mt-3">
                            <input type="hidden" name="order_id" value="<?php echo $selected_order['id']; ?>">
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    <option value="pending" <?php echo $selected_order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $selected_order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $selected_order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $selected_order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['product_name']; ?>" width="50" height="50" style="object-fit: cover; margin-right: 10px;">
                                        <?php echo $item['product_name']; ?>
                                    </div>
                                </td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th><?php echo formatPrice($selected_order['total_price']); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" class="text-center">No orders found</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td>
                            <?php echo $order['customer_name']; ?><br>
                            <small class="text-muted"><?php echo $order['email']; ?></small>
                        </td>
                        <td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td>
                        <td><?php echo formatPrice($order['total_price']); ?></td>
                        <td>
                            <span class="badge <?php 
                                switch($order['status']) {
                                    case 'pending': echo 'bg-warning'; break;
                                    case 'processing': echo 'bg-info'; break;
                                    case 'shipped': echo 'bg-primary'; break;
                                    case 'delivered': echo 'bg-success'; break;
                                    default: echo 'bg-secondary';
                                }
                            ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="orders.php?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
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
