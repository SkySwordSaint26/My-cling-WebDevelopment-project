<?php
require_once '../php/config.php';

// Initialize variables
$email = $password = '';
$error_message = '';
$success_message = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error_message = 'Please enter both email and password.';
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Please enter a valid email address.';
        } else {
            // Check if user exists
            $sql = "SELECT user_id, email, password, first_name FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($user = mysqli_fetch_assoc($result)) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Login successful - set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'];
                    
                    // If there's a cart in session, associate it with the user
                    $sql = "UPDATE cart SET user_id = ? WHERE session_id = ? AND user_id IS NULL";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "is", $_SESSION['user_id'], $_SESSION['session_id']);
                    mysqli_stmt_execute($stmt);
                    
                    $success_message = 'Login successful. Redirecting...';
                    header("Refresh: 2; URL=index.php");
                } else {
                    $error_message = 'Invalid password. Please try again.';
                }
            } else {
                $error_message = 'No account found with that email. Please sign up.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyCling | Modern Fashion E-Commerce</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="icon" type="image/png" href="../images/favicon.png" />
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">MyCling</div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Login to your account to continue shopping</p>
            
            <?php if (!empty($error_message)): ?>
            <div class="error-message" style="color: #FF6B6B; text-align: center; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
            <div class="success-message" style="color: #28a745; text-align: center; margin-bottom: 15px;">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <form class="auth-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="auth-btn">Login</button>
                
                <div class="auth-links">
                    <a href="#">Forgot Password?</a>
                </div>
                
                <div class="social-login">
                    <div class="social-login-title">Or login with</div>
                    <div class="social-login-buttons">
                        <button type="button" class="social-btn google">
                            <i class="fab fa-google"></i> Google
                        </button>
                        <button type="button" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                    </div>
                </div>
                
                <div class="auth-links">
                    Don't have an account? <a href="sign_up.php">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Form Validation
        const form = document.querySelector('.auth-form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        <?php if (empty($success_message)): // Only add client-side validation if not already successful ?>
        form.addEventListener('submit', (e) => {
            let isValid = true;
            
            // Simple email validation
            if (!emailInput.value.includes('@')) {
                emailInput.style.borderColor = '#FF6B6B';
                isValid = false;
            } else {
                emailInput.style.borderColor = '';
            }
            
            // Simple password validation
            if (passwordInput.value.length < 6) {
                passwordInput.style.borderColor = '#FF6B6B';
                isValid = false;
            } else {
                passwordInput.style.borderColor = '';
            }
            
            if (!isValid) {
                e.preventDefault();
            } else {
                const authBtn = document.querySelector('.auth-btn');
                authBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            }
        });
        <?php endif; ?>
    </script>
</body>
</html> 