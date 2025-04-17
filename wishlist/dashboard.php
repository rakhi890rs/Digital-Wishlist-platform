<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../db.php';

// Get user's wishlist items
$query = "SELECT * FROM wishlist WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_items = $result->fetch_all(MYSQLI_ASSOC);

// Get statistics
$total_items = count($wishlist_items);
$high_priority = array_filter($wishlist_items, function($item) {
    return $item['priority'] === 'High';
});
$medium_priority = array_filter($wishlist_items, function($item) {
    return $item['priority'] === 'Medium';
});
$low_priority = array_filter($wishlist_items, function($item) {
    return $item['priority'] === 'Low';
});

// Group items by category
$categories = [];
foreach ($wishlist_items as $item) {
    $category = $item['category'];
    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }
    $categories[$category][] = $item;
}

$stmt->close();
$conn->close();

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User";

$hour = date('H');
if ($hour < 12) {
    $greeting = "Good Morning, ";
} elseif ($hour < 18) {
    $greeting = "Good Afternoon, ";
} else {
    $greeting = "Good Evening, ";
}

// Sample Data
$wishlist_total = 5;
$wishlist_done = 2;
$wishlist_pending = 3;

$recent_items = [
    ["icon" => "🎧", "name" => "Wireless Headphones"],
    ["icon" => "🎉", "name" => "Birthday Card"],
    ["icon" => "🎓", "name" => "PHP Certification Course"],
    ["icon" => "🎁", "name" => "Gift for Friend"],
    ["icon" => "📷", "name" => "Polaroid Camera"]
];

$quote = "Dream big, plan smart, and wish with intention ✨";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
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
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .priority-high {
            border-left: 4px solid #EF4444;
        }
        .priority-medium {
            border-left: 4px solid #F59E0B;
        }
        .priority-low {
            border-left: 4px solid #10B981;
        }
    </style>
</head>
<body class="min-h-screen bg-image flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0F2C59]/95 backdrop-blur-sm shadow-lg flex flex-col justify-between fixed h-screen">
        <div>
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <i class="fas fa-gift text-[#0F2C59] text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-white">Wishlist App</h2>
            </div>
            <nav class="px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center px-4 py-3 text-white bg-white/10 rounded-lg">
                    <i class="fas fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="create.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-plus mr-3"></i> Add Item
                </a>
                <a href="view.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-list mr-3"></i> View Wishlist
                </a>
                <a href="share.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-share-nodes mr-3"></i> Share
                </a>
                <a href="../auth/logout.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Profile -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <p class="text-white/60 text-sm">Logged in as</p>
                    <p class="text-white font-semibold">
                        <?php echo htmlspecialchars($user_name); ?>
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 animate-fadeIn">
                <h1 class="text-4xl font-bold text-white mb-2">
                    <?php echo $greeting . htmlspecialchars($user_name); ?> 👋
                </h1>
                <p class="text-white/80"><?php echo $quote; ?></p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">Total Items</p>
                            <h3 class="text-2xl font-bold text-[#0F2C59]"><?php echo $total_items; ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-[#0F2C59]/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-gift text-[#0F2C59]"></i>
                        </div>
                    </div>
                </div>

                <div class="card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">High Priority</p>
                            <h3 class="text-2xl font-bold text-red-500"><?php echo count($high_priority); ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-fire text-red-500"></i>
                        </div>
                    </div>
                </div>

                <div class="card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">Medium Priority</p>
                            <h3 class="text-2xl font-bold text-yellow-500"><?php echo count($medium_priority); ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-yellow-500/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-bolt text-yellow-500"></i>
                        </div>
                    </div>
                </div>

                <div class="card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500">Low Priority</p>
                            <h3 class="text-2xl font-bold text-green-500"><?php echo count($low_priority); ?></h3>
                        </div>
                        <div class="w-12 h-12 bg-green-500/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-green-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-4">Categories</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($categories as $category => $items): ?>
                        <div class="card rounded-xl p-6 shadow-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-[#0F2C59]"><?php echo htmlspecialchars($category); ?></h3>
                                <span class="text-gray-500"><?php echo count($items); ?> items</span>
                            </div>
                            <div class="space-y-4">
                                <?php foreach (array_slice($items, 0, 3) as $item): ?>
                                    <div class="flex items-center gap-3">
                                        <?php if ($item['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-10 h-10 rounded-lg object-cover">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-gift text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($items) > 3): ?>
                                    <a href="view.php?category=<?php echo urlencode($category); ?>" class="text-[#0F2C59] hover:text-[#0F2C59]/80 transition flex items-center gap-2 text-sm">
                                        <i class="fas fa-arrow-right"></i>
                                        View all <?php echo count($items); ?> items
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Items -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-white">Recent Items</h2>
                    <a href="view.php" class="text-white/80 hover:text-white transition flex items-center gap-2">
                        View All
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach (array_slice($wishlist_items, 0, 6) as $item): ?>
                        <div class="card rounded-xl p-6 shadow-lg <?php echo 'priority-' . strtolower($item['priority']); ?>">
                            <div class="flex items-start gap-4">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-16 h-16 rounded-lg object-cover">
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-gift text-gray-400 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <span class="text-sm px-2 py-1 rounded-full <?php 
                                            echo ($item['priority'] ?? 'Medium') === 'High' ? 'bg-red-100 text-red-600' : 
                                                (($item['priority'] ?? 'Medium') === 'Medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600'); 
                                        ?>">
                                            <?php echo htmlspecialchars($item['priority'] ?? 'Medium'); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                    <?php if ($item['price_range']): ?>
                                        <p class="text-sm font-medium text-[#0F2C59] mt-2"><?php echo htmlspecialchars($item['price_range']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 