<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../db.php';

// Generate a unique share ID if it doesn't exist
$user_id = $_SESSION['user_id'];
$query = "SELECT share_id FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user['share_id']) {
    // Generate a unique share ID
    $share_id = uniqid('wish_', true);
    $update_query = "UPDATE users SET share_id = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $share_id, $user_id);
    $update_stmt->execute();
} else {
    $share_id = $user['share_id'];
}

// Get the share URL
$share_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/shared.php?id=" . $share_id;

// Get user's wishlist count for stats
$wishlist_query = "SELECT COUNT(*) as total_items FROM wishlist WHERE user_id = ? AND status = 'Active'";
$wishlist_stmt = $conn->prepare($wishlist_query);
$wishlist_stmt->bind_param("i", $user_id);
$wishlist_stmt->execute();
$wishlist_result = $wishlist_stmt->get_result();
$wishlist_stats = $wishlist_result->fetch_assoc();
$wishlist_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Wishlist - Wishlist App</title>
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
                <a href="view.php" class="flex items-center px-4 py-3 text-white/80 hover:bg-white/10 rounded-lg transition">
                    <i class="fas fa-list mr-3"></i> View Wishlist
                </a>
                <a href="share.php" class="flex items-center px-4 py-3 text-white bg-white/10 rounded-lg">
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
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-8 animate-fadeIn">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-[#0F2C59]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-nodes text-[#0F2C59] text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-[#0F2C59] mb-2">Share Your Wishlist</h1>
                    <p class="text-gray-600">Share your wishlist with friends and family</p>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-[#0F2C59]/5 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Items</p>
                                <p class="text-2xl font-bold text-[#0F2C59]"><?php echo $wishlist_stats['total_items']; ?></p>
                            </div>
                            <div class="w-10 h-10 bg-[#0F2C59]/10 rounded-full flex items-center justify-center">
                                <i class="fas fa-gift text-[#0F2C59]"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#0F2C59]/5 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Share ID</p>
                                <p class="text-sm font-mono text-[#0F2C59]"><?php echo $share_id; ?></p>
                            </div>
                            <div class="w-10 h-10 bg-[#0F2C59]/10 rounded-full flex items-center justify-center">
                                <i class="fas fa-id-card text-[#0F2C59]"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#0F2C59]/5 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Status</p>
                                <p class="text-sm font-semibold text-green-600">Active</p>
                            </div>
                            <div class="w-10 h-10 bg-[#0F2C59]/10 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-[#0F2C59]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share URL -->
                <div class="mb-8">
                    <label class="block text-gray-700 font-semibold mb-2">Your Share Link</label>
                    <div class="flex gap-2">
                        <input type="text" value="<?php echo $share_url; ?>" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" readonly>
                        <button onclick="copyToClipboard()" class="bg-[#0F2C59] text-white px-6 py-3 rounded-lg hover:bg-[#0F2C59]/90 transition flex items-center gap-2">
                            <i class="fas fa-copy"></i>
                            Copy
                        </button>
                    </div>
                </div>

                <!-- Share Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" target="_blank" class="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <i class="fab fa-facebook"></i>
                        Share on Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>" target="_blank" class="bg-blue-400 text-white p-4 rounded-lg hover:bg-blue-500 transition flex items-center justify-center gap-2">
                        <i class="fab fa-twitter"></i>
                        Share on Twitter
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode("Check out my wishlist: " . $share_url); ?>" target="_blank" class="bg-green-500 text-white p-4 rounded-lg hover:bg-green-600 transition flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp"></i>
                        Share on WhatsApp
                    </a>
                </div>

                <!-- Preview -->
                <div class="border-t border-gray-200 pt-8">
                    <h2 class="text-xl font-bold text-[#0F2C59] mb-4">Preview</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-600">This is how your shared wishlist will appear to others:</p>
                        <a href="<?php echo $share_url; ?>" target="_blank" class="text-[#0F2C59] hover:underline mt-2 inline-block">
                            <?php echo $share_url; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function copyToClipboard() {
            const copyText = document.querySelector('input');
            copyText.select();
            document.execCommand('copy');
            
            // Show feedback
            const button = document.querySelector('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        }
    </script>
</body>
</html> 