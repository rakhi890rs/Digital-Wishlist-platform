<?php 
session_start();
include '../db.php'; // Database connection file

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "❌ Please fill in all fields!";
    } else {
        // Fetch user from database
        $query = "SELECT id, name, password, share_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $error = "❌ User not found!";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "❌ Incorrect password!";
        } else {
            // Get user settings
            $settings_query = "SELECT * FROM user_settings WHERE user_id = ?";
            $settings_stmt = $conn->prepare($settings_query);
            $settings_stmt->bind_param("i", $user['id']);
            $settings_stmt->execute();
            $settings_result = $settings_stmt->get_result();
            $settings = $settings_result->fetch_assoc();

            // Get unread notifications count
            $notifications_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
            $notifications_stmt = $conn->prepare($notifications_query);
            $notifications_stmt->bind_param("i", $user['id']);
            $notifications_stmt->execute();
            $notifications_result = $notifications_stmt->get_result();
            $notifications = $notifications_result->fetch_assoc();

            // Set session variables
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['share_id'] = $user['share_id'];
            $_SESSION['settings'] = $settings;
            $_SESSION['unread_notifications'] = $notifications['count'];

            header('Location: ../wishlist/dashboard.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out;
        }
        .bg-image {
            background-image: url('../assets/img/4.png');
            background-color: #0F2C59;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        .bg-image::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(15, 44, 89, 0.5);
            z-index: -1;
        }
        .form-input {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #E1F0FF;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #0F2C59;
            box-shadow: 0 0 0 3px rgba(15, 44, 89, 0.1);
        }
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }
        .social-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .social-btn:hover {
            transform: translateY(-2px);
        }
        .social-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: translateX(100%);
            transition: transform 0.6s ease;
        }
        .social-btn:hover::after {
            transform: translateX(-100%);
        }
    </style>
    <script>
        function togglePassword() {
            let passwordInput = document.getElementById("password");
            let toggleIcon = document.getElementById("togglePasswordIcon");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
            toggleIcon.classList.toggle("fa-eye");
            toggleIcon.classList.toggle("fa-eye-slash");
        }
    </script>
</head>
<body class="min-h-screen bg-image flex items-center justify-center p-4">
    <!-- Decorative Elements -->
    <div class="decorative-circle w-64 h-64 bg-[#0F2C59] top-0 left-0 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="decorative-circle w-96 h-96 bg-[#E1F0FF] bottom-0 right-0 translate-x-1/2 translate-y-1/2"></div>

    <div class="w-full max-w-md animate-fadeIn relative z-10">
        <div class="bg-white/95 backdrop-blur-sm rounded-xl p-8 shadow-2xl border border-white/20">
            <!-- Logo and Welcome Section -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#0F2C59]">Welcome to <span class="text-[#0F2C59]">Wishlist App</span></h2>
                <p class="text-sm text-gray-500 mt-2">Sign in to access your wishlists</p>
            </div>

            <!-- Social Login Buttons -->
            <div class="space-y-3 mb-6">
                <button class="social-btn w-full flex items-center justify-center gap-3 py-2.5 bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
                    <i class="fab fa-google"></i> Continue with Google
                </button>
                <button class="social-btn w-full flex items-center justify-center gap-3 py-2.5 bg-black text-white rounded-lg shadow hover:bg-gray-900">
                    <i class="fab fa-apple"></i> Continue with Apple
                </button>
                <button class="social-btn w-full flex items-center justify-center gap-3 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                    <i class="fab fa-facebook"></i> Continue with Facebook
                </button>
            </div>

            <div class="relative my-6 text-center text-gray-400">
                <span class="px-4 bg-white/95 z-10 relative">or, log in with email</span>
                <hr class="absolute top-1/2 left-0 w-full border-t z-0">
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm mb-4 font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" class="form-input pl-10" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" id="password" class="form-input pl-10" placeholder="Enter your password" required>
                        <i id="togglePasswordIcon" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 cursor-pointer fa fa-eye"></i>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 mt-2 bg-[#0F2C59] text-white font-semibold rounded-lg hover:bg-[#0a1f3d] transition-all shadow-md flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Log in
                </button>
            </form>

            <p class="text-sm text-gray-600 mt-5 text-center">
                Don't have an account?
                <a href="register.php" class="text-[#0F2C59] font-medium hover:underline">Sign Up</a>
            </p>

            <div class="mt-6 text-center space-y-2">
                <p class="text-xs text-gray-400">
                    By continuing, you agree to Wishlist App's 
                    <a href="#" class="underline hover:text-[#0F2C59]">Terms of Use</a> & 
                    <a href="#" class="underline hover:text-[#0F2C59]">Privacy Policy</a>
                </p>
                <p class="text-xs text-gray-400">
                    Protected by Google reCAPTCHA: 
                    <a href="#" class="underline hover:text-[#0F2C59]">Privacy</a> - 
                    <a href="#" class="underline hover:text-[#0F2C59]">Terms</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html> 