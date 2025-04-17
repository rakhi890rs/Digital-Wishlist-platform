<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s forwards;
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
        .step-card {
            transition: all 0.3s ease;
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
            <a href="how-it-works.php" class="text-[#E1F0FF] text-xl font-bold">How it Works</a>
            <a href="Contact.php" class="text-[#E1F0FF] text-xl">Contact Us</a>
            <a href="../wishlist/create.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-plus"></i></a>
            <a href="../wishlist/view.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-list"></i></a>
            <a href="../wishlist/share.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-share-nodes"></i></a>
            <a href="../auth/login.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-right-to-bracket"></i></a>
            <a href="../auth/register.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-user-plus"></i></a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 pt-32 pb-16">
        <div class="text-center mb-16 animate-fadeIn">
            <h1 class="text-5xl font-bold text-white mb-4">How Wishlist App Works</h1>
            <p class="text-xl text-white/80">Follow these simple steps to create and manage your wishlists</p>
        </div>

        <!-- Steps -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Step 1: Register -->
            <div class="step-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">1. Register</h3>
                </div>
                <p class="text-gray-600 mb-4">Create your account by providing your details. You can also sign up using your Google, Apple, or Facebook account.</p>
                <a href="../auth/register.php" class="block w-full py-2 bg-[#0F2C59] text-white rounded-lg text-center hover:bg-[#0a1e3f] transition">
                    Get Started
                </a>
            </div>

            <!-- Step 2: Login -->
            <div class="step-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.2s">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-sign-in-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">2. Login</h3>
                </div>
                <p class="text-gray-600 mb-4">Sign in to your account to access your dashboard and start creating wishlists.</p><br>
                <a href="../auth/login.php" class="block w-full py-2 bg-[#0F2C59] text-white rounded-lg text-center hover:bg-[#0a1e3f] transition">
                    Sign In
                </a>
            </div>

            <!-- Step 3: Create -->
            <div class="step-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.4s">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-plus text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">3. Create</h3>
                </div>
                <p class="text-gray-600 mb-4">Create your wishlist by adding items, descriptions, and links. You can create multiple wishlists for different occasions.</p>
                <a href="../wishlist/create.php" class="block w-full py-2 bg-[#0F2C59] text-white rounded-lg text-center hover:bg-[#0a1e3f] transition">
                    Create Wishlist
                </a>
            </div>

            <!-- Step 4: View & Share -->
            <div class="step-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.6s">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">4. View & Share</h3>
                </div>
                <p class="text-gray-600 mb-4">View your wishlists and share them with friends and family. They can see what you want and mark items as purchased.</p>
                <a href="../wishlist/view.php" class="block w-full py-2 bg-[#0F2C59] text-white rounded-lg text-center hover:bg-[#0a1e3f] transition">
                    View Wishlists
                </a>
            </div>
        </div>

        <!-- Additional Features -->
        <div class="mt-16 bg-white rounded-xl p-8 shadow-lg animate-fadeIn">
            <h2 class="text-3xl font-bold text-[#0F2C59] mb-6 text-center">Additional Features</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <i class="fas fa-mobile-alt text-4xl text-[#0F2C59] mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Mobile Friendly</h3>
                    <p class="text-gray-600">Access your wishlists from any device, anywhere, anytime.</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-bell text-4xl text-[#0F2C59] mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Notifications</h3>
                    <p class="text-gray-600">Get notified when someone purchases an item from your wishlist.</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-lock text-4xl text-[#0F2C59] mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Privacy Control</h3>
                    <p class="text-gray-600">Control who can see your wishlists with privacy settings.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="mt-16 text-center animate-fadeIn">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Start?</h2>
            <p class="text-xl text-white/80 mb-8">Create your account and start building your wishlists today!</p>
            <a href="../auth/register.php" class="inline-block px-8 py-3 bg-white text-[#0F2C59] rounded-lg font-semibold hover:bg-gray-100 transition">
                Get Started Now
            </a>
        </div>
    </div>
</body>
</html> 