document.addEventListener('DOMContentLoaded', function() {
    // Get all "Add to Cart" buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    // Function to update cart count in the header
    function updateCartCount(count) {
        const cartLink = document.querySelector('nav ul li a[href="cart.php"]');
        let cartCountElem = document.querySelector('.cart-count');
        
        if (count > 0) {
            if (!cartCountElem) {
                cartCountElem = document.createElement('span');
                cartCountElem.className = 'cart-count';
                cartLink.appendChild(cartCountElem);
            }
            cartCountElem.textContent = count;
        } else {
            if (cartCountElem) {
                cartCountElem.remove();
            }
        }
    }
    
    // Check if product is already in cart
    function checkProductInCart(productId, callback) {
        fetch('../php/cart_api.php?summary=true')
            .then(response => response.json())
            .then(data => {
                // Get cart items to check if product is in cart
                fetch('../php/cart_api.php')
                    .then(response => response.json())
                    .then(cartData => {
                        const isInCart = cartData.items.some(item => 
                            parseInt(item.product_id) === parseInt(productId)
                        );
                        callback(isInCart, data.total_items);
                    })
                    .catch(error => console.error('Error:', error));
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Initial cart count update
    fetch('../php/cart_api.php?summary=true')
        .then(response => response.json())
        .then(data => {
            updateCartCount(data.total_items);
            
            // Check each button to see if the product is in the cart
            addToCartButtons.forEach(button => {
                const productId = button.dataset.productId;
                
                // Get cart items to check if product is in cart
                fetch('../php/cart_api.php')
                    .then(response => response.json())
                    .then(cartData => {
                        const isInCart = cartData.items.some(item => 
                            parseInt(item.product_id) === parseInt(productId)
                        );
                        
                        if (isInCart) {
                            button.innerHTML = '<i class="fas fa-check"></i> Added';
                            button.classList.add('in-cart');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        })
        .catch(error => console.error('Error:', error));
    
    // Add to cart functionality
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            // Check if product is already in cart
            checkProductInCart(productId, (isInCart, cartCount) => {
                if (isInCart) {
                    // Remove from cart
                    fetch('../php/cart_api.php', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button
                            this.innerHTML = 'Add to Cart';
                            this.classList.remove('in-cart');
                            this.classList.remove('added');
                            
                            // Update cart count in header
                            updateCartCount(data.summary.total_items);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                } else {
                    // Add to cart
                    fetch('../php/cart_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button
                            this.innerHTML = '<i class="fas fa-check"></i> Added';
                            this.classList.add('added');
                            
                            // Update cart count in header
                            updateCartCount(data.summary.total_items);
                            
                            // After 2 seconds, change to "in cart" state if not clicked again
                            setTimeout(() => {
                                if (this.classList.contains('added')) {
                                    this.classList.remove('added');
                                    this.classList.add('in-cart');
                                }
                            }, 2000);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });
}); 