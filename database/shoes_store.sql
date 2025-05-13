-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
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

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    stock INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Insert sample products
INSERT INTO products (category_id, name, description, price, image_url, stock) VALUES 
(1, 'Speed Runner', 'Ultra-lightweight running shoes for marathon training', 129.99, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff', 25),
(1, 'Trail Blazer', 'Durable trail running shoes with extra grip', 149.99, 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2', 18),
(2, 'Daily Comfort', 'Everyday casual shoes for maximum comfort', 89.99, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77', 32),
(2, 'Urban Walker', 'Stylish casual shoes for city life', 99.99, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86', 22),
(3, 'Executive Leather', 'Premium leather formal shoes for business attire', 199.99, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2', 15),
(4, 'Court Champion', 'Professional tennis shoes with ankle support', 119.99, 'https://images.unsplash.com/photo-1562183241-b937e95585b6', 20),
(5, 'Summer Stride', 'Comfortable sandals for beach and casual wear', 49.99, 'https://images.unsplash.com/photo-1612015670817-0127d21628d4', 45);

-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'shipped', 'delivered'))
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$8sO4anVHlj7MJl5NjmvSAeKQlk3F0h/xShEQ8QSQtG5VVJrQ4wAKW');
