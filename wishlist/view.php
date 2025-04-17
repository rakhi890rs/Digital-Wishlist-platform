<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../db.php';

$user_id = $_SESSION['user_id'];

// Get user's wishlist items
$query = "SELECT w.*, 
          (SELECT COUNT(*) FROM comments WHERE wishlist_id = w.id) as comment_count,
          (SELECT COUNT(*) FROM likes WHERE wishlist_id = w.id) as like_count
          FROM wishlist w 
          WHERE w.user_id = ? 
          ORDER BY w.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_items = $result->fetch_all(MYSQLI_ASSOC);

// Get user's wishlist stats
$stats_query = "SELECT 
                COUNT(*) as total_items,
                SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_items,
                SUM(CASE WHEN status = 'Fulfilled' THEN 1 ELSE 0 END) as fulfilled_items
                FROM wishlist 
                WHERE user_id = ?";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Wishlist - Wishlist App</title>
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
    </style>
</head>
<body class="min-h-screen bg-image">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0F2C59]/95 backdrop-blur-sm shadow-lg flex flex-col justify-between fixed h-screen left-0">
        <div>
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <i class="fas fa-gift text-[#0F2C59] text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-white">Wishlist App</h2>
            </div>
            <nav class="px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-chart-line mr-3"></i> Dashboard
                </a>
                <a href="create.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-plus mr-3"></i> Add Item
                </a>
                <a href="view.php" class="flex items-center px-4 py-3 text-white bg-white/10 rounded-lg">
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
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Total Items</p>
                            <p class="text-2xl font-bold text-[#0F2C59]"><?php echo $stats['total_items']; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-[#0F2C59]/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-gift text-[#0F2C59] text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Active Items</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo $stats['active_items']; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm">Fulfilled Items</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo $stats['fulfilled_items']; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wishlist Items -->
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-8 animate-fadeIn">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-[#0F2C59]">My Wishlist</h1>
                    <a href="create.php" class="bg-[#0F2C59] text-white px-6 py-3 rounded-lg hover:bg-[#0F2C59]/90 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Add New Item
                    </a>
                </div>

                <?php if (empty($wishlist_items)): ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-[#0F2C59]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-gift text-[#0F2C59] text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Items Yet</h3>
                        <p class="text-gray-600 mb-4">Start by adding your first wishlist item</p>
                        <a href="create.php" class="inline-flex items-center gap-2 text-[#0F2C59] hover:underline">
                            <i class="fas fa-plus"></i>
                            Add New Item
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($wishlist_items as $item): ?>
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-48 object-cover">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-[#0F2C59]/10 flex items-center justify-center">
                                        <i class="fas fa-gift text-[#0F2C59] text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold text-[#0F2C59]"><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <span class="px-3 py-1 rounded-full text-sm <?php echo $item['status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo htmlspecialchars($item['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-comment"></i>
                                            <?php echo $item['comment_count']; ?> comments
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-heart"></i>
                                            <?php echo $item['like_count']; ?> likes
                                        </span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="flex-1 bg-[#0F2C59] text-white px-4 py-2 rounded-lg hover:bg-[#0F2C59]/90 transition text-center">
                                            <i class="fas fa-edit mr-2"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?php echo $item['id']; ?>" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-center" onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html> 