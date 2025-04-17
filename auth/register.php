<?php
session_start();

$error_message = null;
$success_message = null;

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    include '../db.php';

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // --- Validation ---
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Email address already registered.";
            $stmt->close();
        } else {
            $stmt->close();
            // Start transaction
            $conn->begin_transaction();

            try {
                // Generate share_id
                $share_id = uniqid('wish_', true);
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert into users table
                $insert_user_query = "INSERT INTO users (name, email, password, share_id) VALUES (?, ?, ?, ?)";
                $stmt_user = $conn->prepare($insert_user_query);
                $stmt_user->bind_param("ssss", $name, $email, $hashed_password, $share_id);
                $stmt_user->execute();
                $user_id = $stmt_user->insert_id;
                $stmt_user->close();

                // Insert into user_settings table
                $insert_settings_query = "INSERT INTO user_settings (user_id) VALUES (?)";
                $stmt_settings = $conn->prepare($insert_settings_query);
                $stmt_settings->bind_param("i", $user_id);
                $stmt_settings->execute();
                $stmt_settings->close();

                // Insert welcome notification
                $notification_message = "Welcome to Wishlist App, " . htmlspecialchars($name) . "! Start creating your wishlist.";
                $insert_notification_query = "INSERT INTO notifications (user_id, type, message) VALUES (?, 'system', ?)";
                $stmt_notification = $conn->prepare($insert_notification_query);
                $stmt_notification->bind_param("is", $user_id, $notification_message);
                $stmt_notification->execute();
                $stmt_notification->close();

                // Commit transaction
                $conn->commit();
                
                $_SESSION['success'] = "Registration successful! Please log in.";
                header("Location: login.php");
                exit();

            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                error_log("Registration failed: " . $exception->getMessage()); // Log the actual error
                $_SESSION['error'] = "Registration failed. Please try again.";
            }
        }
    }
    $conn->close();
    header("Location: register.php"); // Redirect back to register page to show message
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body::before {
            content: '';
            position: fixed; /* Cover viewport */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 44, 89, 0.5); /* Semi-transparent overlay */
            z-index: 0; /* Behind content */
            opacity: 0; /* Start hidden */
            animation: fadeInBg 1.5s ease-out forwards; /* Use same fade as background */
        }
        .bg-image {
            background-image: url('../assets/img/4.png'); 
            background-color: #E1F0FF; /* Fallback */
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Keep background fixed */
            opacity: 0; /* Start hidden */
            animation: fadeInBg 1.5s ease-out forwards; /* Background fade-in animation */
            position: relative; /* Needed for z-index stacking */
            z-index: 0;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInBg {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out 0.5s forwards; /* Delay form fade-in */
            opacity: 0; /* Start form hidden */
        }
        /* Apply consistent focus styles */
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #0F2C59;
            box-shadow: 0 0 0 3px rgba(15, 44, 89, 0.3);
            outline: none;
        }
        .main-btn {
            background-color: #0F2C59;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .main-btn:hover {
            background-color: #0a1e3f;
            transform: translateY(-2px);
        }
        .link-color {
            color: #0F2C59;
        }
        .link-color:hover {
            color: #0a1e3f;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            backdrop-filter: blur(5px); /* Glass effect */
            position: relative; /* Ensure it sits above the pseudo-element */
            z-index: 1;
        }
    </style>
</head>
<body class="bg-image flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <!-- Back to Home Link (Already has z-10, so it's above overlay) -->
    <div class="absolute top-4 left-4 z-10">
        <a href="../index.php" class="text-white text-lg font-semibold hover:text-[#0F2C59] transition duration-300 flex items-center gap-2 bg-black/30 hover:bg-white/80 px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>

    <div class="form-container p-8 md:p-12 rounded-xl shadow-2xl w-full max-w-md animate-fadeIn">
        <div class="text-center mb-8">
            <div class="inline-block bg-[#0F2C59] p-3 rounded-full mb-4">
                <i class="fas fa-gift text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-[#0F2C59]">Create Your Account</h1>
            <p class="text-gray-600 mt-2">Join our community and start building your wishlists today!</p>
        </div>

        <!-- Social Login Buttons -->
        <div class="space-y-3 mb-6">
            <button class="w-full flex items-center justify-center gap-2 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                <i class="fab fa-google"></i> Continue with Google
            </button>
            <button class="w-full flex items-center justify-center gap-2 bg-black text-white py-3 rounded-lg hover:bg-gray-800 transition font-semibold">
                <i class="fab fa-apple"></i> Continue with Apple
            </button>
            <button class="w-full flex items-center justify-center gap-2 bg-blue-700 text-white py-3 rounded-lg hover:bg-blue-800 transition font-semibold">
                <i class="fab fa-facebook"></i> Continue with Facebook
            </button>
        </div>

        <!-- Divider -->
        <div class="relative flex items-center justify-center mb-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative bg-transparent px-4 text-sm text-gray-600" style="background-color: rgba(255, 255, 255, 0.9);">or register with email</div>
        </div>

        <!-- Error Message Display -->
        <?php if ($error_message): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md animate-fadeIn" role="alert" style="animation-delay: 0.5s;">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error_message); ?></p>
        </div>
        <?php endif; ?>

        <!-- Success Message Display (Optional - Usually redirect) -->
        <?php if ($success_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md animate-fadeIn" role="alert" style="animation-delay: 0.5s;">
            <p class="flex items-center"><i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($success_message); ?></p>
        </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-user text-gray-400"></i>
                    </span>
                    <input type="text" name="name" id="name" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Choose a username" required>
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </span>
                    <input type="email" name="email" id="email" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your email" required>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-lock text-gray-400"></i>
                    </span>
                    <input type="password" name="password" id="password" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Create a password" required>
                    <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="password-eye-icon"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Password should be at least 6 characters long</p>
            </div>

            <div>
                <button type="submit" name="register" class="w-full main-btn text-white py-3 rounded-lg transition font-semibold">Create Account</button>
            </div>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600">
            Already have an account? 
            <a href="login.php" class="font-medium link-color hover:underline">Sign in</a>
        </p>

        <p class="mt-4 text-center text-xs text-gray-500">
            By signing up, you agree to our 
            <a href="../terms.php" class="link-color underline hover:text-gray-700">Terms of Service</a> and 
            <a href="../privacy.php" class="link-color underline hover:text-gray-700">Privacy Policy</a>.
        </p>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 