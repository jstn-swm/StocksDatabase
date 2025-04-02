<?php
include 'config.php';
include 'includes/header.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];
    
    // Validate input
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = "Username must be 3-20 characters and contain only letters, numbers, and underscores";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // If no validation errors, check if username/email exists and create account
    if (empty($errors)) {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username already exists";
            }
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already in use";
            }
            
            // If no duplicate username/email, create account
            if (empty($errors)) {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert the new user
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);
                
                // Get the new user ID
                $user_id = $conn->lastInsertId();
                
                // Start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                
                // Redirect to home page
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Registration error: " . $e->getMessage();
        }
    }
}
?>

<div class="auth-container">
    <h2>Register</h2>
    
    <?php
    // Display errors if any
    if (!empty($errors)) {
        echo '<div class="error-container">';
        foreach ($errors as $error) {
            echo '<p class="error">' . $error . '</p>';
        }
        echo '</div>';
    }
    ?>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="auth-form" id="register-form">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            <span class="error" id="username-error"></span>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            <span class="error" id="email-error"></span>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span class="error" id="password-error"></span>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="error" id="confirm-password-error"></span>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <p class="auth-link">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
$(document).ready(function() {
    // Client-side form validation
    $("#register-form").on("submit", function(e) {
        let isValid = true;
        
        // Validate username
        const username = $("#username").val().trim();
        if (username === "") {
            $("#username-error").text("Username is required");
            isValid = false;
        } else if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
            $("#username-error").text("Username must be 3-20 characters and contain only letters, numbers, and underscores");
            isValid = false;
        } else {
            $("#username-error").text("");
        }
        
        // Validate email
        const email = $("#email").val().trim();
        if (email === "") {
            $("#email-error").text("Email is required");
            isValid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            $("#email-error").text("Invalid email format");
            isValid = false;
        } else {
            $("#email-error").text("");
        }
        
        // Validate password
        const password = $("#password").val();
        if (password === "") {
            $("#password-error").text("Password is required");
            isValid = false;
        } else if (password.length < 6) {
            $("#password-error").text("Password must be at least 6 characters");
            isValid = false;
        } else {
            $("#password-error").text("");
        }
        
        // Validate confirm password
        const confirmPassword = $("#confirm_password").val();
        if (confirmPassword === "") {
            $("#confirm-password-error").text("Please confirm your password");
            isValid = false;
        } else if (confirmPassword !== password) {
            $("#confirm-password-error").text("Passwords do not match");
            isValid = false;
        } else {
            $("#confirm-password-error").text("");
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?> 