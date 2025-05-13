// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Quantity increment/decrement functionality
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    if (quantityInputs) {
        quantityInputs.forEach(input => {
            const decrementBtn = input.parentElement.querySelector('.decrement');
            const incrementBtn = input.parentElement.querySelector('.increment');
            
            if (decrementBtn) {
                decrementBtn.addEventListener('click', function() {
                    if (input.value > 1) {
                        input.value = parseInt(input.value) - 1;
                        
                        // If in cart page, trigger form submission to update
                        if (input.closest('form.update-cart-form')) {
                            input.closest('form').submit();
                        }
                    }
                });
            }
            
            if (incrementBtn) {
                incrementBtn.addEventListener('click', function() {
                    input.value = parseInt(input.value) + 1;
                    
                    // If in cart page, trigger form submission to update
                    if (input.closest('form.update-cart-form')) {
                        input.closest('form').submit();
                    }
                });
            }
        });
    }
    
    // Product filter functionality
    const categoryFilters = document.querySelectorAll('.category-pill');
    
    if (categoryFilters) {
        categoryFilters.forEach(filter => {
            filter.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all filters
                categoryFilters.forEach(f => f.classList.remove('active'));
                
                // Add active class to clicked filter
                this.classList.add('active');
                
                const categoryId = this.getAttribute('data-category');
                const productCards = document.querySelectorAll('.product-card-wrapper');
                
                productCards.forEach(card => {
                    if (categoryId === 'all' || card.getAttribute('data-category') === categoryId) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Form validation for checkout
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = checkoutForm.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Email validation
            const emailField = checkoutForm.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailField.value)) {
                    isValid = false;
                    emailField.classList.add('is-invalid');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill all required fields correctly.');
            }
        });
    }
    
    // Initialize any tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Image preview for admin product forms
    const imageUrlInput = document.getElementById('image_url');
    const imagePreview = document.getElementById('image-preview');
    
    if (imageUrlInput && imagePreview) {
        imageUrlInput.addEventListener('input', function() {
            imagePreview.src = this.value;
            imagePreview.style.display = this.value ? 'block' : 'none';
        });
    }
});
