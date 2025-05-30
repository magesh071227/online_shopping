
# Shoes Store API

This REST API provides endpoints for managing products, categories, orders, customers, and cart functionality.

## Base URL
```
http://your-repl-url:5000/api/
```

## Endpoints

### Products
- `GET /api/products` - Get all products
- `GET /api/products?category=1` - Get products by category
- `GET /api/products?limit=10` - Limit results
- `GET /api/products/{id}` - Get single product
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Categories
- `GET /api/categories` - Get all categories
- `GET /api/categories/{id}` - Get single category
- `POST /api/categories` - Create new category
- `PUT /api/categories/{id}` - Update category
- `DELETE /api/categories/{id}` - Delete category

### Orders
- `GET /api/orders` - Get all orders
- `GET /api/orders/{id}` - Get single order with items
- `POST /api/orders` - Create new order
- `PUT /api/orders/{id}` - Update order status

### Customers
- `GET /api/customers` - Get all customers
- `GET /api/customers/{id}` - Get single customer
- `POST /api/customers` - Create new customer
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer

### Cart
- `GET /api/cart` - Get cart items
- `POST /api/cart` - Add item to cart
- `PUT /api/cart` - Update cart item quantity
- `DELETE /api/cart/{product_id}` - Remove item from cart
- `DELETE /api/cart` - Clear entire cart

## Example Usage

### Get all products
```bash
curl http://your-repl-url:5000/api/products
```

### Create a new product
```bash
curl -X POST http://your-repl-url:5000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Shoe",
    "description": "Great shoe",
    "price": 99.99,
    "category_id": 1,
    "image_url": "http://example.com/image.jpg",
    "stock": 50
  }'
```

### Create an order
```bash
curl -X POST http://your-repl-url:5000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer": {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "123-456-7890",
      "address": "123 Main St"
    },
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "price": 99.99
      }
    ]
  }'
```

## Response Format

All responses are in JSON format. Success responses include the requested data, while error responses include an error message:

```json
{
  "error": "Error message here"
}
```
