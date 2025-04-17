<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Wishlist App</title>
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
        .feature-card {
            transition: all 0.3s ease;
        }
        .feature-card:hover {
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
            <a href="AboutUs.php" class="text-[#E1F0FF] text-xl font-bold">About Us</a>
            <a href="how-it-works.php" class="text-[#E1F0FF] text-xl">How it Works</a>
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
        <!-- Hero Section -->
        <div class="text-center mb-16 animate-fadeIn">
            <h1 class="text-5xl font-bold text-white mb-4">About Wishlist App</h1>
            <p class="text-xl text-white/80 max-w-3xl mx-auto">Making gift-giving easier and more meaningful for everyone</p>
        </div>

        <!-- Mission Section -->
        <div class="bg-white rounded-xl p-8 shadow-lg mb-16 animate-fadeIn">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-[#0F2C59] mb-6">Our Mission</h2>
                <p class="text-gray-600 text-lg mb-6">
                    At Wishlist App, we believe that gift-giving should be a joyful experience for both the giver and receiver. 
                    Our mission is to simplify the process of creating, sharing, and managing wishlists, making it easier for 
                    people to give and receive meaningful gifts.
                </p>
                <div class="flex justify-center">
                    <div class="w-24 h-1 bg-[#0F2C59] rounded-full"></div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="grid md:grid-cols-3 gap-8 mb-16">
            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Meaningful Gifts</h3>
                </div>
                <p class="text-gray-600">We help people give and receive gifts that truly matter, making every occasion special.</p>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.2s">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Community Focus</h3>
                </div>
                <p class="text-gray-600">We build connections between friends and family through the joy of giving and receiving.</p>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.4s">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Innovation</h3>
                </div>
                <p class="text-gray-600">We continuously improve our platform to make gift-giving easier and more enjoyable.</p>
            </div>
        </div>

        <!-- Team Section -->
        <div class="bg-white rounded-xl p-8 shadow-lg mb-16 animate-fadeIn">
            <h2 class="text-3xl font-bold text-[#0F2C59] mb-8 text-center">Our Story</h2>
            <div class="max-w-3xl mx-auto">
                <p class="text-gray-600 mb-6">
                    Wishlist App was born from a simple idea: making gift-giving easier and more meaningful. 
                    Our journey began when our founder struggled to find the perfect gift for a loved one's birthday. 
                    This experience led to the creation of a platform that helps people create, share, and manage their wishlists effortlessly.
                </p>
                <p class="text-gray-600 mb-6">
                    Today, we're proud to serve thousands of users worldwide, helping them make every gift-giving occasion special. 
                    Our platform continues to evolve, incorporating user feedback and the latest technology to provide the best possible experience.
                </p>
                <p class="text-gray-600">
                    Join us in our mission to make gift-giving a more joyful and meaningful experience for everyone.
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Easy to Use</h3>
                        <p class="text-gray-600">Our intuitive interface makes creating and managing wishlists a breeze.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.2s">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Secure & Private</h3>
                        <p class="text-gray-600">Your data is protected with industry-standard security measures.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.4s">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <i class="fas fa-sync text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Always Evolving</h3>
                        <p class="text-gray-600">We continuously improve our platform based on user feedback.</p>
                    </div>
                </div>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-lg animate-fadeIn" style="animation-delay: 0.6s">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-[#0F2C59] rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                        <i class="fas fa-headset text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-[#0F2C59] mb-2">Great Support</h3>
                        <p class="text-gray-600">Our team is always here to help you with any questions or concerns.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center animate-fadeIn">
            <h2 class="text-3xl font-bold text-white mb-4">Join Our Community</h2>
            <p class="text-xl text-white/80 mb-8">Start creating and sharing your wishlists today!</p>
            <a href="../auth/register.php" class="inline-block px-8 py-3 bg-white text-[#0F2C59] rounded-lg font-semibold hover:bg-gray-100 transition">
                Get Started Now
            </a>
        </div>
    </div>
</body>
</html> 