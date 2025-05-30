
#!/bin/bash

echo "Testing API Endpoints..."
echo "========================"

# Test locally (since server is running on port 5000)
BASE_URL="http://localhost:5000/api"

echo -e "\n1. Testing GET /products"
curl -X GET "$BASE_URL/products"

echo -e "\n\n2. Testing GET /categories"
curl -X GET "$BASE_URL/categories"

echo -e "\n\n3. Testing GET /customers"
curl -X GET "$BASE_URL/customers"

echo -e "\n\n4. Testing GET /orders"
curl -X GET "$BASE_URL/orders"

echo -e "\n\n5. Testing GET /cart"
curl -X GET "$BASE_URL/cart"

echo -e "\n\n6. Testing single product (ID 1)"
curl -X GET "$BASE_URL/products/1"

echo -e "\n\nAPI Testing Complete!"
