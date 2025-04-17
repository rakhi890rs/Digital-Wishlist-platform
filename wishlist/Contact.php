<?php
session_start();
include '../db.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "❌ Please fill in all fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Please enter a valid email address!";
    } else {
        // Prepare and execute the query
        $query = "INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            
            if ($stmt->execute()) {
                $success = "✅ Thank you for your message! We'll get back to you soon.";
                // Clear form data after successful submission
                $name = $email = $subject = $message = "";
            } else {
                $error = "❌ Sorry, there was an error sending your message. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "❌ Database error. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Wishlist App</title>
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
        .contact-card {
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body class="min-h-screen bg-image">
    <!-- Navigation -->
    <nav class="w-full bg-[#0F2C59] shadow-md fixed top-0 left-0 p-4 flex justify-between items-center z-50">
        <h1 class="text-2xl font-bold text-[#E1F0FF] ml-6">🎁 Wishlist App</h1>
        <div class="flex items-center gap-6 mr-6">
            <a href="../index.php" class="text-[#E1F0FF] text-xl">Home</a>
            <a href="about.php" class="text-[#E1F0FF] text-xl">About Us</a>
            <a href="how-it-works.php" class="text-[#E1F0FF] text-xl">How it Works</a>
            <a href="contact.php" class="text-[#E1F0FF] text-xl font-bold">Contact Us</a>
            <a href="../wishlist/create.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-plus"></i></a>
            <a href="../wishlist/view.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-list"></i></a>
            <a href="../wishlist/share.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-share-nodes"></i></a>
            <a href="../auth/login.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-right-to-bracket"></i></a>
            <a href="../auth/register.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-user-plus"></i></a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 pt-32 pb-16">
        <!-- Hero Section -->
        <div class="text-center mb-16 animate-fadeIn">
            <h1 class="text-5xl font-bold text-white mb-4">Contact Us</h1>
            <p class="text-xl text-white/80 max-w-3xl mx-auto">Have questions or feedback? We'd love to hear from you!</p>
        </div>

        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="contact-card bg-white rounded-xl p-8 shadow-lg animate-fadeIn">
                <h2 class="text-2xl font-bold text-[#0F2C59] mb-6">Send us a Message</h2>
                <form action="contact.php" method="POST" class="space-y-6">
                    <?php if ($success): ?>
                        <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm mb-4 font-medium flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm mb-4 font-medium flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-2">Your Name</label>
                        <input type="text" id="name" name="name" class="form-input" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-input" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" class="form-input" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-[#0F2C59] text-white rounded-lg font-semibold hover:bg-[#0a1f3d] transition">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <!-- Contact Cards -->
                <div class="contact-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Email Us</h3>
                            <p class="text-gray-600">support@wishlistapp.com</p>
                            <p class="text-gray-600">info@wishlistapp.com</p>
                        </div>
                    </div>
                </div>

                <div class="contact-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.2s">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-phone text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Call Us</h3>
                            <p class="text-gray-600">+1 (555) 123-4567</p>
                            <p class="text-gray-600">Mon-Fri: 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>

                <div class="contact-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.4s">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-map-marker-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Visit Us</h3>
                            <p class="text-gray-600">123 Wishlist Street</p>
                            <p class="text-gray-600">San Francisco, CA 94105</p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="contact-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.6s">
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-[#0F2C59] rounded-full flex items-center justify-center text-white hover:bg-[#0a1f3d] transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#0F2C59] rounded-full flex items-center justify-center text-white hover:bg-[#0a1f3d] transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#0F2C59] rounded-full flex items-center justify-center text-white hover:bg-[#0a1f3d] transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-[#0F2C59] rounded-full flex items-center justify-center text-white hover:bg-[#0a1f3d] transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="max-w-4xl mx-auto mt-16">
            <h2 class="text-3xl font-bold text-white mb-8 text-center">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div class="bg-white rounded-xl p-6 shadow-lg animate-fadeIn">
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">How do I create a wishlist?</h3>
                    <p class="text-gray-600">Creating a wishlist is easy! Just sign up for an account, click the "Create" button, and start adding items to your list.</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.2s">
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Can I share my wishlist with specific people?</h3>
                    <p class="text-gray-600">Yes! You can share your wishlist with specific people by sending them a private link or by making it public.</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.4s">
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Is my personal information secure?</h3>
                    <p class="text-gray-600">Absolutely! We take your privacy seriously and use industry-standard security measures to protect your data.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 