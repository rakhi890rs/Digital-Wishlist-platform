<?php
session_start();
include 'db.php';

// Get total users count
$users_query = "SELECT COUNT(*) as total FROM users";
$users_result = $conn->query($users_query);
$total_users = $users_result->fetch_assoc()['total'];

// Get total wishlists count
$wishlists_query = "SELECT COUNT(*) as total FROM wishlist";
$wishlists_result = $conn->query($wishlists_query);
$total_wishlists = $wishlists_result->fetch_assoc()['total'];

// Get recent wishlist items
$recent_items_query = "SELECT w.*, u.name as user_name 
                      FROM wishlist w 
                      JOIN users u ON w.user_id = u.id 
                      WHERE w.status = 'Active' 
                      ORDER BY w.created_at DESC 
                      LIMIT 4";
$recent_items_result = $conn->query($recent_items_query);
$recent_items = [];
while ($row = $recent_items_result->fetch_assoc()) {
    $recent_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 1s ease-in-out;
        }
        .occasion-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .occasion-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-[#E1F0FF]" style="background-image: url('assets/img/2.png'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;">

    <nav class="w-full bg-[#0F2C59] shadow-md fixed top-0 left-0 p-4 flex justify-between items-center z-50">
        <h1 class="text-2xl font-bold text-[#E1F0FF] ml-6">🎁 Wishlist App</h1>
        <div class="flex items-center gap-6 mr-6">
            <a href="wishlist/AboutUs.php" class="text-[#E1F0FF] text-xl">About Us</a>
            <a href="wishlist/how-it-works.php" class="text-[#E1F0FF] text-xl">How it Works</a>
            <a href="wishlist/Contact.php" class="text-[#E1F0FF] text-xl">Contact Us</a>
            <a href="wishlist/create.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-plus"></i></a>
            <a href="wishlist/view.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-list"></i></a>
            <a href="wishlist/share.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-share-nodes"></i></a>
            <a href="auth/login.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-right-to-bracket"></i></a>
            <a href="auth/register.php" class="text-[#E1F0FF] text-2xl"><i class="fa-solid fa-user-plus"></i></a>
        </div>
    </nav>

    <div class="flex flex-col lg:flex-row items-center justify-center h-screen px-10 mt-20 animate-fadeIn">
        <div class="lg:w-1/2 text-center lg:text-left">
            <h2 class="text-6xl font-bold text-white">Create and Share your WishLists</h2>
            <p class="mt-2 text-lg text-white">Add items from WishList.com. Share wishlists with friends and family.</p>
            <a href="wishlist/create.php" class="mt-5 inline-block px-6 py-3 bg-[#0F2C59] text-white text-lg font-semibold rounded-full hover:bg-[#0a1e3f] transition">
                + Create a Wishlist
            </a>
        </div>
        <div class="lg:w-1/2 mt-10 lg:mt-0 flex justify-center">
            <img src="assets/img/3.png" alt="Wishlist Preview" class="w-80 lg:w-96 rounded-lg shadow-lg">
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 px-10 bg-white text-center border-[10px] border-[#E1F0FF] rounded-2xl shadow-lg mx-10 mt-10 animate-fadeIn">
        <h2 class="text-4xl font-bold text-[#0F2C59] mb-12">Why Choose Our Wishlist App?</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-[#E1F0FF]/20 p-6 rounded-lg">
                <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-share-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[#0F2C59] mb-2">Easy Sharing</h3>
                <p class="text-gray-600">Share your wishlist with friends and family through social media or direct links.</p>
            </div>
            <div class="bg-[#E1F0FF]/20 p-6 rounded-lg">
                <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[#0F2C59] mb-2">Smart Notifications</h3>
                <p class="text-gray-600">Get notified when someone comments on or likes your wishlist items.</p>
            </div>
            <div class="bg-[#E1F0FF]/20 p-6 rounded-lg">
                <div class="w-16 h-16 bg-[#0F2C59] rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-[#0F2C59] mb-2">Secure & Private</h3>
                <p class="text-gray-600">Your data is protected with advanced security measures and privacy controls.</p>
            </div>
        </div>
    </div>

    <div class="py-20 px-10 bg-white text-center border-[10px] border-[#E1F0FF] rounded-2xl shadow-lg mx-10 mt-10 animate-fadeIn" style="background-image: url('assets/img/5.png'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;">
        <h2 class="text-4xl font-bold text-[#E1F0FF]">A Wishlist for Every Occasion</h2>
        <div class="grid md:grid-cols-3 gap-10 mt-10">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden occasion-card" onclick="window.location.href='wishlist/create.php?type=christmas'">
                <img src="http://www.pixelstalk.net/wp-content/uploads/2016/10/Christmas-wide-wallpaper-hd.jpg" alt="Christmas Wishlist" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-[#0F2C59]">🎄 Christmas Wishlist</h3>
                    <p class="mt-2 text-[#0F2C59]">Create a wishlist for any occasion, like a Christmas Wishlist. Get the gifts you want for yourself and your loved ones. Share with friends and family for a jolly holiday!</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden occasion-card" onclick="window.location.href='wishlist/create.php?type=birthday'">
                <img src="https://img.freepik.com/premium-photo/birthday-cake-with-candles_863013-71618.jpg" alt="Birthday Wishlist" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-[#0F2C59]">🎂 Birthday Wishlist</h3>
                    <p class="mt-2 text-[#0F2C59]">Create a birthday wishlist and make your birthday wishes come true!</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden occasion-card" onclick="window.location.href='wishlist/create.php?type=baby'">
                <img src="https://tidymalism.com/wp-content/uploads/2022/11/minimalist-gift-giving-and-receiving.jpg" alt="Baby Wishlist" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-[#0F2C59]">👶 Baby Wishlist</h3>
                    <p class="mt-2 text-[#0F2C59]">Make a baby registry with items from any store. Get all of the must-haves for your new bundle of joy.</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden occasion-card" onclick="window.location.href='wishlist/create.php?type=wedding'">
                <img src="https://images.bonanzastatic.com/afu/images/2502/3577/89/il_fullxfull.844023168_eo29.jpg" alt="Wedding Gift List" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-[#0F2C59]">💍 Wedding Gift List</h3>
                    <p class="mt-2 text-[#0F2C59]">Create your wedding gift list online and collaborate with your partner, then share with family and friends to reserve items.</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden occasion-card" onclick="window.location.href='wishlist/create.php?type=housewarming'">
                <img src="https://www.thesawguy.com/wp-content/uploads/2018/03/Housewarming-Gift-1.jpg" alt="House Warming List" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-[#0F2C59]">🏡 House Warming List</h3>
                    <p class="mt-2 text-[#0F2C59]">Prepare for your new home with a list of home essentials, then share with guests who can tick off what they will bring.</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden occasion-card" onclick="window.location.href='wishlist/create.php?type=other'">
                <img src="https://thumbs.dreamstime.com/b/hand-drawing-wish-list-notebook-recycling-paper-grey-background-handle-46273273.jpg" alt="Anything Else" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold text-[#0F2C59]">📝 Anything Else</h3>
                    <p class="mt-2 text-[#0F2C59]">Manage any list, from to-do lists, bucket lists, planning a BBQ, cocktail parties, or grocery shopping.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="py-20 px-10 bg-white text-center border-[10px] border-[#E1F0FF] rounded-2xl shadow-lg mx-10 mt-10 animate-fadeIn">
        <h2 class="text-4xl font-bold text-[#0F2C59] mb-12">What Our Users Say</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-[#E1F0FF]/20 p-6 rounded-lg">
                <div class="flex items-center justify-center mb-4">
                    <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="User" class="w-16 h-16 rounded-full">
                </div>
                <p class="text-gray-600 mb-4">"This app made my wedding planning so much easier! All our guests could see what we needed and avoid duplicate gifts."</p>
                <p class="font-semibold text-[#0F2C59]">Sarah Johnson</p>
                <p class="text-sm text-gray-500">Wedding Planner</p>
            </div>
            <div class="bg-[#E1F0FF]/20 p-6 rounded-lg">
                <div class="flex items-center justify-center mb-4">
                    <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="User" class="w-16 h-16 rounded-full">
                </div>
                <p class="text-gray-600 mb-4">"I love how easy it is to share my wishlist with family. The notifications keep me updated on who's getting me what!"</p>
                <p class="font-semibold text-[#0F2C59]">Michael Brown</p>
                <p class="text-sm text-gray-500">Happy User</p>
            </div>
            <div class="bg-[#E1F0FF]/20 p-6 rounded-lg">
                <div class="flex items-center justify-center mb-4">
                    <img src="https://randomuser.me/api/portraits/women/2.jpg" alt="User" class="w-16 h-16 rounded-full">
                </div>
                <p class="text-gray-600 mb-4">"The baby registry feature is amazing! It helped us get exactly what we needed for our new arrival."</p>
                <p class="font-semibold text-[#0F2C59]">Emily Davis</p>
                <p class="text-sm text-gray-500">New Parent</p>
            </div>
        </div>
    </div>

    <div class="text-center py-10">
        <a href="wishlist/dashboard.php" class="text-[#0F2C59] text-xl font-semibold hover:underline">➡️ Go to Dashboard</a>
    </div>

    <!-- Footer -->
    <footer class="bg-[#0F2C59] text-white py-12">
        <div class="container mx-auto px-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Wishlist App</h3>
                    <p class="text-gray-300">Create, share, and manage your wishlists with ease. Perfect for any occasion!</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="wishlist/AboutUs.php" class="text-gray-300 hover:text-white">About Us</a></li>
                        <li><a href="wishlist/how-it-works.php" class="text-gray-300 hover:text-white">How it Works</a></li>
                        <li><a href="wishlist/Contact.php" class="text-gray-300 hover:text-white">Contact Us</a></li>
                        <li><a href="auth/login.php" class="text-gray-300 hover:text-white">Login</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Features</h3>
                    <ul class="space-y-2">
                        <li><a href="wishlist/create.php" class="text-gray-300 hover:text-white">Create Wishlist</a></li>
                        <li><a href="wishlist/view.php" class="text-gray-300 hover:text-white">View Wishlist</a></li>
                        <li><a href="wishlist/share.php" class="text-gray-300 hover:text-white">Share Wishlist</a></li>
                        <li><a href="wishlist/dashboard.php" class="text-gray-300 hover:text-white">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white text-2xl"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white text-2xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white text-2xl"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white text-2xl"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; <?php echo date('Y'); ?> Wishlist App. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
