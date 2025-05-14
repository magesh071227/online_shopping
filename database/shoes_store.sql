-- Create categories table first
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Running'), 
('Casual'), 
('Formal'), 
('Sports'), 
('Sandals');

-- Create products table with matching data type for foreign key
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Insert sample products for casual category
INSERT INTO products (category_id, name, description, price, image_url, stock) VALUES 
(2, 'Urban Classic', 'Versatile casual shoes for everyday wear', 79.99, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77', 30),
(2, 'Street Comfort', 'Modern streetwear sneakers', 89.99, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86', 25),
(5, 'Summer Breeze', 'Comfortable beach sandals', 49.99, 'https://images.unsplash.com/photo-1628626126093-97c2d922a83d', 40),
(5, 'Flip Flop Pro', 'Premium flip flops for everyday wear', 39.99, 'https://images.unsplash.com/photo-1615032585566-dbb52fbc6c94', 35),
(5, 'Comfort Walk', 'Ergonomic walking sandals', 59.99, 'https://images.unsplash.com/photo-1595341888016-a392ef81b7de', 25),
(2, 'Daily Runner', 'Lightweight casual running shoes', 69.99, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff', 20),
(2, 'Metro Walk', 'Stylish walking shoes for city life', 84.99, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2', 15),
(2, 'Casual Elite', 'Premium casual shoes for any occasion', 99.99, 'https://images.unsplash.com/photo-1562183241-b937e95585b6', 18),
(2, 'Urban Breeze', 'Breathable casual sneakers', 74.99, 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2', 22),
(2, 'City Stride', 'Comfortable city walking shoes', 79.99, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77', 28),
(2, 'Leisure Step', 'Relaxed casual footwear', 69.99, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86', 35),
(2, 'Weekend Comfort', 'Perfect weekend casual shoes', 89.99, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff', 20),
(2, 'Urban Flow', 'Trendy casual sneakers', 94.99, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2', 15);

-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped', 'delivered'))
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$8sO4anVHlj7MJl5NjmvSAeKQlk3F0h/xQSQtG5VVJrQ4wAKW');