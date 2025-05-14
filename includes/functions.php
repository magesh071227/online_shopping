<?php

// Function to sanitize user input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to get all products
function getProducts($db, $category_id = null, $limit = null) {
    try {
        // Build the main query
        $query = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id";
                
        if ($category_id) {
            $query .= " WHERE p.category_id = :category_id";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        // PostgreSQL uses numeric limit
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $db->prepare($query);
        
        if ($category_id) {
            $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If no results found with the join, try a simpler approach
        if (empty($results)) {
            // Get products directly
            $productQuery = "SELECT * FROM products";
            if ($category_id) {
                $productQuery .= " WHERE category_id = " . (int)$category_id;
            }
            $productQuery .= " ORDER BY created_at DESC";
            if ($limit) {
                $productQuery .= " LIMIT " . (int)$limit;
            }
            
            $productStmt = $db->query($productQuery);
            $products = $productStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add category name to each product
            foreach ($products as &$product) {
                $catQuery = "SELECT name FROM categories WHERE id = " . (int)$product['category_id'];
                $catStmt = $db->query($catQuery);
                $catName = $catStmt->fetchColumn();
                $product['category_name'] = $catName ?: 'Unknown Category';
            }
            
            return $products;
        }
        
        return $results;
    } catch (PDOException $e) {
        // Log error but don't expose details to the user
        error_log("Query error in getProducts: " . $e->getMessage());
        return [];
    }
}

// Function to get a single product by ID
function getProduct($db, $id) {
    try {
        // Try the join query first
        $query = "SELECT p.*, c.name as category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If join query failed, try direct query approach
        if (!$result) {
            $directQuery = "SELECT * FROM products WHERE id = " . (int)$id;
            $directStmt = $db->query($directQuery);
            $directResult = $directStmt->fetch(PDO::FETCH_ASSOC);
            
            // If we got a direct result, add category name
            if ($directResult) {
                $catQuery = "SELECT name FROM categories WHERE id = " . (int)$directResult['category_id'];
                $catStmt = $db->query($catQuery);
                $catName = $catStmt->fetchColumn();
                
                $directResult['category_name'] = $catName ?: 'Unknown Category';
                return $directResult;
            }
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Query error in getProduct: " . $e->getMessage());
        return false;
    }
}

// Function to get all categories
function getCategories($db) {
    try {
        $query = "SELECT * FROM categories ORDER BY name";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Query error in getCategories: " . $e->getMessage());
        return [];
    }
}

// Function to add item to cart
function addToCart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // If product already in cart, increase quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Function to update cart item quantity
function updateCartItem($product_id, $quantity) {
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
}

// Function to remove item from cart
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Function to get cart items with product details
function getCartItems($db) {
    $cart_items = [];
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = getProduct($db, $product_id);
            if ($product) {
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
            }
        }
    }
    
    return $cart_items;
}

// Function to calculate cart total
function getCartTotal($cart_items) {
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

// Function to get number of items in cart
function getCartCount() {
    $count = 0;
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $quantity) {
            $count += $quantity;
        }
    }
    
    return $count;
}

// Function to clear the cart
function clearCart() {
    unset($_SESSION['cart']);
}

// Function to check if user is logged in as admin
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Function to redirect if not logged in as admin
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

// Function to display prices in a formatted way
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Function to generate a random order reference
function generateOrderRef() {
    return 'ORD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}
?>
