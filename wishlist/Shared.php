<?php
include '../db.php';

$share_id = isset($_GET['id']) ? $_GET['id'] : null;
$user = null;
$wishlist_items = [];
$error = null;

if (!$share_id) {
    $error = "No share ID provided.";
} else {
    // Find the user by share_id
    $user_query = "SELECT id, name FROM users WHERE share_id = ? LIMIT 1";
    $stmt_user = $conn->prepare($user_query);
    
    if ($stmt_user) {
        $stmt_user->bind_param("s", $share_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows === 1) {
            $user = $result_user->fetch_assoc();
            $user_id = $user['id'];

            // Fetch active wishlist items for this user
            $wishlist_query = "SELECT w.*, 
                                (SELECT COUNT(*) FROM comments WHERE wishlist_id = w.id) as comment_count,
                                (SELECT COUNT(*) FROM likes WHERE wishlist_id = w.id) as like_count
                              FROM wishlist w 
                              WHERE w.user_id = ? AND w.status = 'Active'
                              ORDER BY w.priority DESC, w.created_at DESC";
            $stmt_wishlist = $conn->prepare($wishlist_query);
            
            if ($stmt_wishlist) {
                $stmt_wishlist->bind_param("i", $user_id);
                $stmt_wishlist->execute();
                $result_wishlist = $stmt_wishlist->get_result();
                $wishlist_items = $result_wishlist->fetch_all(MYSQLI_ASSOC);
                $stmt_wishlist->close();
            } else {
                error_log("Wishlist Prepare Error: " . $conn->error);
                $error = "Could not fetch wishlist items.";
            }
        } else {
            $error = "Invalid share link or wishlist not found.";
        }
        $stmt_user->close();
    } else {
         error_log("User Prepare Error: " . $conn->error);
         $error = "An error occurred trying to find the user.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user ? htmlspecialchars($user['name']) . "'s Wishlist" : "Shared Wishlist"; ?> - Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <style>
        body::before {
            content: '';
            position: fixed; /* Cover viewport */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 44, 89, 0.5); /* Semi-transparent overlay */
            z-index: -1; /* Sit behind content but above background image */
            opacity: 0; /* Start hidden */
            animation: fadeInBg 1.5s ease-out forwards; /* Use same fade as background */
        }
        body {
            background-image: url('../assets/img/2.png'); 
            background-color: #E1F0FF; /* Fallback */
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Keep background fixed */
            position: relative; /* Needed for stacking context */
            z-index: 0;
            opacity: 0; /* Start hidden */
            animation: fadeInBg 1.5s ease-out forwards; /* Background fade-in animation */
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInBg {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out 0.5s forwards; /* Delay content fade-in */
            opacity: 0;
        }
        .wishlist-card {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95); /* Semi-transparent white card */
            backdrop-filter: blur(5px);
        }
        .wishlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(15, 44, 89, 0.2);
        }
         .priority-high { background-color: #FFE4E4; color: #DC2626; border-color: #FECACA; }
         .priority-medium { background-color: #FEF3C7; color: #D97706; border-color: #FDE68A; }
         .priority-low { background-color: #DCFCE7; color: #16A34A; border-color: #BBF7D0; }
    </style>
</head>
<body class="font-sans">

    <!-- Header -->
    <header class="bg-[#0F2C59] text-white shadow-lg py-4 fixed top-0 left-0 right-0 z-10">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                🎁 <?php echo $user ? htmlspecialchars($user['name']) . "'s Wishlist" : "Shared Wishlist"; ?>
            </h1>
            <a href="../index.php" class="text-sm hover:text-blue-200 transition">Powered by Wishlist App</a>
        </div>
    </header>

    <!-- Main Content (Add padding top to account for fixed header) -->
    <main class="container mx-auto px-6 py-12 pt-24"> <!-- Added pt-24 -->
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg relative text-center animate-fadeIn shadow-md max-w-xl mx-auto" role="alert">
                <strong class="font-bold block text-lg mb-2">Oops! Something went wrong.</strong>
                <span class="block sm:inline text-md"><?php echo htmlspecialchars($error); ?></span>
                <p class="mt-4"><a href="../index.php" class="text-[#0F2C59] font-semibold hover:underline">Go back home</a></p>
            </div>
        <?php elseif ($user): ?>
            <?php if (empty($wishlist_items)): ?>
                <div class="text-center py-16 bg-white/95 backdrop-blur-sm rounded-xl shadow-lg animate-fadeIn max-w-lg mx-auto">
                    <div class="w-20 h-20 bg-[#0F2C59]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-[#0F2C59] text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-[#0F2C59] mb-3">This wishlist is empty</h3>
                    <p class="text-gray-600 text-lg"><?php echo htmlspecialchars($user['name']); ?> hasn't added any items yet.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fadeIn">
                    <?php foreach ($wishlist_items as $item): ?>
                        <div class="wishlist-card rounded-xl shadow-lg overflow-hidden relative">
                             <!-- Priority Badge -->
                             <div class="absolute top-3 right-3 px-2 py-1 rounded-md text-xs font-semibold border <?php echo 'priority-' . strtolower($item['priority']); ?> z-10">
                                 <?php echo htmlspecialchars($item['priority']); ?>
                             </div>
                            <?php if ($item['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-56 object-cover transition duration-300 ease-in-out hover:scale-105">
                            <?php else: ?>
                                <div class="w-full h-56 bg-gray-200/50 flex items-center justify-center">
                                    <i class="fas fa-gift text-[#0F2C59]/50 text-6xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-[#0F2C59] mb-2 truncate" title="<?php echo htmlspecialchars($item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?></h3>
                                
                                <p class="text-gray-700 mb-4 text-sm h-16 line-clamp-3" title="<?php echo htmlspecialchars($item['description'] ?? ''); ?>">
                                    <?php echo nl2br(htmlspecialchars($item['description'] ?? 'No description provided.')); ?>
                                </p>
                                
                                <div class="flex items-center justify-between text-xs text-gray-500 border-t border-gray-200 pt-3 mt-4">
                                     <span class="flex items-center gap-1.5" title="Category">
                                         <i class="fas fa-tag"></i>
                                         <?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?>
                                     </span>
                                     <?php if (!empty($item['price_range'])): ?>
                                     <span class="flex items-center gap-1.5" title="Price Range">
                                         <i class="fas fa-dollar-sign"></i>
                                         <?php echo htmlspecialchars($item['price_range']); ?>
                                     </span>
                                     <?php endif; ?>
                                 </div>

                                <div class="flex items-center justify-between text-sm text-gray-500 mt-3">
                                    <span class="flex items-center gap-1.5" title="Comments">
                                        <i class="fas fa-comment text-gray-400"></i>
                                        <?php echo $item['comment_count']; ?>
                                    </span>
                                    <span class="flex items-center gap-1.5" title="Likes">
                                        <i class="fas fa-heart text-gray-400"></i>
                                        <?php echo $item['like_count']; ?>
                                    </span>
                                    <span class="text-xs text-gray-400" title="Added on <?php echo date('M d, Y', strtotime($item['created_at'])); ?>">
                                        <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-transparent text-center py-8 mt-12">
        <p class="text-white/80 text-sm">Wishlist shared using <a href="../index.php" class="text-white font-semibold hover:underline">Wishlist App</a></p>
    </footer>

</body>
</html> 