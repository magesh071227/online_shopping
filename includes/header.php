<?php 
// In production, you would set these to 0 and handle errors gracefully
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'includes/functions.php';

// Connect to database
$database = new Database();
$db = $database->getConnection();

// Ensure database connection
if (!$db) {
    die("Database connection failed. Please try again later.");
}

// Get all categories for navigation
$categories = getCategories($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShoesHub - Your Premier Shoe Destination</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-shoe-prints me-2"></i>ShoesHub
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">All Shoes</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php foreach ($categories as $category): ?>
                                <li><a class="dropdown-item" href="products.php?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <a href="cart.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span class="badge bg-danger"><?php echo getCartCount(); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container py-4">
