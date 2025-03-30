<?php
require_once '../php/cart_functions.php';

// Get cart items and summary
$cart_items = getCartItems();
$cart_summary = getCartSummary();
$total_items = $cart_summary['total_items'];
$subtotal = $cart_summary['total_price'];

// Calculate shipping, tax, and total
$shipping = ($subtotal > 0) ? 150 : 0; // Free shipping over a certain amount could be implemented
$tax = round($subtotal * 0.18); // 18% tax
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - MyCling | Modern Fashion E-Commerce</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" type="image/png" href="../images/favicon.png" />
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <div class="logo">MyCling</div>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="men.php"><i class="fas fa-male"></i> Men</a></li>
                <li><a href="women.php"><i class="fas fa-female"></i> Women</a></li>
                <li><a href="product.php"><i class="fas fa-tshirt"></i> Products</a></li>
                <li><a href="cart.php" class="active"><i class="fas fa-shopping-cart"></i> Cart <?php if($total_items > 0): ?><span class="cart-count"><?php echo $total_items; ?></span><?php endif; ?></a></li>
                <li><a href="login.php"><i class="fas fa-user"></i> Account</a></li>
                <li>
                    <div class="search-container">
                        <input type="text" class="search-bar" placeholder="Search products..." />
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </li>
            </ul>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1>Shopping Cart</h1>
            <div class="breadcrumb">
                <a href="index.php">Home</a> / <span>Cart</span>
            </div>
        </div>
    </section>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <div class="cart-container">
                <?php if(empty($cart_items)): ?>
                <!-- Empty Cart -->
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <a href="product.php" class="continue-shopping">Continue Shopping</a>
                </div>
                <?php else: ?>
                <!-- Cart with Items -->
                <div class="cart-items">
                    <div class="cart-header">
                        <div class="cart-product">Product</div>
                        <div class="cart-quantity">Quantity</div>
                        <div class="cart-price">Price</div>
                        <div class="cart-total">Total</div>
                        <div class="cart-remove">Remove</div>
                    </div>
                    
                    <?php foreach($cart_items as $item): 
                        $unit_price = $item['is_sale'] && $item['sale_price'] ? $item['sale_price'] : $item['price'];
                        $total_price = $unit_price * $item['quantity'];
                    ?>
                    <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                        <div class="cart-product">
                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="cart-product-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <?php if($item['is_sale'] && $item['sale_price']): ?>
                                <p>₹<?php echo number_format($item['sale_price']); ?> <span class="original-price">₹<?php echo number_format($item['price']); ?></span></p>
                                <?php else: ?>
                                <p>₹<?php echo number_format($item['price']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="cart-quantity">
                            <button class="cart-quantity-btn decrease">-</button>
                            <input type="number" class="cart-quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="99">
                            <button class="cart-quantity-btn increase">+</button>
                        </div>
                        <div class="cart-price">
                            ₹<?php echo number_format($unit_price); ?>
                        </div>
                        <div class="cart-total">
                            ₹<?php echo number_format($total_price); ?>
                        </div>
                        <div class="cart-remove">
                            <button class="remove-item"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($subtotal); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>₹<?php echo number_format($shipping); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (18%)</span>
                        <span>₹<?php echo number_format($tax); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>₹<?php echo number_format($total); ?></span>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout <i class="fas fa-arrow-right"></i></a>
                    <a href="product.php" class="continue-shopping">Continue Shopping</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- You May Also Like Section (Shown for both empty and filled carts) -->
    <section class="related-products">
        <div class="container">
            <h2 class="section-title">You May Also Like</h2>
            <div class="product-carousel">
                <?php
                // Get related products
                $sql = "SELECT * FROM products WHERE is_featured = 1 LIMIT 5";
                $result = mysqli_query($conn, $sql);
                while($product = mysqli_fetch_assoc($result)):
                ?>
                <div class="product-card">
                    <?php if($product['is_new']): ?>
                    <div class="product-badge">New</div>
                    <?php elseif($product['is_sale']): ?>
                    <div class="product-badge sale">Sale</div>
                    <?php endif; ?>
                    <a href="product-details.php?id=<?php echo $product['product_id']; ?>">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if($product['is_sale'] && $product['sale_price']): ?>
                        <p class="product-price">₹<?php echo number_format($product['sale_price']); ?> <span class="original-price">₹<?php echo number_format($product['price']); ?></span></p>
                        <?php else: ?>
                        <p class="product-price">₹<?php echo number_format($product['price']); ?></p>
                        <?php endif; ?>
                        <button class="add-to-cart-btn" data-product-id="<?php echo $product['product_id']; ?>">Add to Cart</button>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">MyCling</div>
                    <p>Your one-stop destination for trendy and comfortable clothing.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="men.php">Men</a></li>
                        <li><a href="women.php">Women</a></li>
                        <li><a href="product.php">Products</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Returns & Exchanges</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <div class="footer-info">
                        <div class="contact">+123 456 7890</div>
                        <div class="email">support@mycling.com</div>
                        <div class="insta">@my_cling</div>
                        <div class="address">123 Fashion Street, Design City</div>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                Copyright &copy; MyCling 2025. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navMenu = document.querySelector('nav ul');
        
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                navMenu.classList.toggle('active');
            });
        }
        
        // Header Scroll Effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Cart functionality
        const cartItems = document.querySelectorAll('.cart-item');
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
        
        // Function to update cart totals
        function updateCartTotals(summary) {
            const subtotalElem = document.querySelector('.summary-row:nth-child(1) span:last-child');
            const shippingElem = document.querySelector('.summary-row:nth-child(2) span:last-child');
            const taxElem = document.querySelector('.summary-row:nth-child(3) span:last-child');
            const totalElem = document.querySelector('.summary-row.total span:last-child');
            
            if (subtotalElem && summary) {
                const subtotal = summary.total_price;
                const shipping = subtotal > 0 ? 150 : 0;
                const tax = Math.round(subtotal * 0.18);
                const total = subtotal + shipping + tax;
                
                subtotalElem.textContent = '₹' + subtotal.toLocaleString();
                shippingElem.textContent = '₹' + shipping.toLocaleString();
                taxElem.textContent = '₹' + tax.toLocaleString();
                totalElem.textContent = '₹' + total.toLocaleString();
                
                // Update cart count in header
                updateCartCount(summary.total_items);
            }
        }
        
        // Function to remove a cart item
        function removeCartItem(element) {
            const cartItem = element.closest('.cart-item');
            const productId = cartItem.dataset.productId;
            
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
                    cartItem.remove();
                    
                    // Update cart totals
                    updateCartTotals(data.summary);
                    
                    // Check if cart is empty and reload if necessary
                    if (data.summary.total_items === 0) {
                        location.reload();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Function to update cart item quantity
        function updateItemQuantity(element, newQuantity) {
            const cartItem = element.closest('.cart-item');
            const productId = cartItem.dataset.productId;
            
            fetch('../php/cart_api.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    product_id: productId,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (newQuantity <= 0) {
                        cartItem.remove();
                        
                        // Check if cart is empty and reload if necessary
                        if (data.summary.total_items === 0) {
                            location.reload();
                        }
                    } else {
                        // Update the displayed quantity
                        const quantityInput = cartItem.querySelector('.cart-quantity-input');
                        quantityInput.value = newQuantity;
                        
                        // Update the item total
                        const priceText = cartItem.querySelector('.cart-price').textContent;
                        const price = parseFloat(priceText.replace('₹', '').replace(',', ''));
                        const newTotal = price * newQuantity;
                        cartItem.querySelector('.cart-total').textContent = '₹' + newTotal.toLocaleString();
                    }
                    
                    // Update cart totals
                    updateCartTotals(data.summary);
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Attach event listeners to cart items
        cartItems.forEach(item => {
            // Remove button
            const removeBtn = item.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    removeCartItem(this);
                });
            }
            
            // Quantity decrease button
            const decreaseBtn = item.querySelector('.decrease');
            if (decreaseBtn) {
                decreaseBtn.addEventListener('click', function() {
                    const quantityInput = this.parentElement.querySelector('.cart-quantity-input');
                    const currentQuantity = parseInt(quantityInput.value);
                    if (currentQuantity > 1) {
                        updateItemQuantity(this, currentQuantity - 1);
                    } else {
                        removeCartItem(this);
                    }
                });
            }
            
            // Quantity increase button
            const increaseBtn = item.querySelector('.increase');
            if (increaseBtn) {
                increaseBtn.addEventListener('click', function() {
                    const quantityInput = this.parentElement.querySelector('.cart-quantity-input');
                    const currentQuantity = parseInt(quantityInput.value);
                    if (currentQuantity < 99) {
                        updateItemQuantity(this, currentQuantity + 1);
                    }
                });
            }
            
            // Quantity input change
            const quantityInput = item.querySelector('.cart-quantity-input');
            if (quantityInput) {
                quantityInput.addEventListener('change', function() {
                    let newQuantity = parseInt(this.value);
                    
                    // Validate quantity
                    if (isNaN(newQuantity) || newQuantity < 1) {
                        newQuantity = 1;
                        this.value = 1;
                    } else if (newQuantity > 99) {
                        newQuantity = 99;
                        this.value = 99;
                    }
                    
                    updateItemQuantity(this, newQuantity);
                });
            }
        });
        
        // Add to cart functionality for "You May Also Like" section
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                
                // Add to cart via AJAX
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
                        // Show added confirmation
                        this.innerHTML = '<i class="fas fa-check"></i> Added';
                        this.classList.add('added');
                        
                        // Update cart count in header
                        updateCartCount(data.summary.total_items);
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            this.innerHTML = 'Add to Cart';
                            this.classList.remove('added');
                        }, 2000);
                        
                        // Reload the page if this is the first item added to cart
                        const cartItems = document.querySelector('.cart-items');
                        const emptyCart = document.querySelector('.empty-cart');
                        if (!cartItems && emptyCart) {
                            location.reload();
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
    </script>
</body>
</html> 