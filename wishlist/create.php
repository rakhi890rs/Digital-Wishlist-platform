<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

include '../db.php'; // Ensure database connection is included

$error = null;
$success = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $wishlist_name_input = trim($_POST['wishlist_name']); // Keep this if needed elsewhere, but don't insert it
    $item_name = trim($_POST['item_name']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    $price_range = trim($_POST['price_range']);
    $image_url = trim($_POST['image_url']);
    $status = $_POST['status'];

    // Use item_name for validation now
    if (empty($item_name)) {
        $error = "Item name is required!";
    } else {
        if (!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }
        
        // Corrected Query: Use 'name' column, remove 'wishlist_name'
        $query = "INSERT INTO wishlist (user_id, name, category, description, priority, price_range, image_url, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            // Log error or display a user-friendly message
             error_log("SQL Prepare Error: " . $conn->error); 
             $error = "An error occurred while preparing the statement.";
             // Optionally: die("SQL Error: " . $conn->error); // For debugging only
        } else {
            // Corrected bind_param: Match the new query columns and types (8 params now)
            $stmt->bind_param("isssssss", $_SESSION['user_id'], $item_name, $category, $description, $priority, $price_range, $image_url, $status);
            if ($stmt->execute()) {
                $success = "Item added successfully!";
                // Clear form fields or redirect
                echo "<script>setTimeout(() => { window.location.href = 'view.php'; }, 1500);</script>";
            } else {
                 error_log("SQL Execute Error: " . $stmt->error);
                $error = "Failed to add item. Please try again.";
                 // Optionally: $error = "Failed to add item: " . $stmt->error; // For debugging only
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Wishlist Item - Wishlist App</title>
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
        .form-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(15, 44, 89, 0.1);
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 0.5rem;
            display: none;
        }
    </style>
</head>
<body class="min-h-screen bg-image flex items-center justify-center p-4">
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
                <a href="create.php" class="flex items-center px-4 py-3 text-white bg-white/10 rounded-lg">
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
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 flex items-center justify-center p-8">
        <div class="form-card w-full max-w-2xl rounded-xl shadow-lg p-8 animate-fadeIn">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-[#0F2C59]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-[#0F2C59] text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#0F2C59]">Add to Your Wishlist</h2>
                <p class="text-gray-600 mt-2">Save your favorite items and make your wishes come true!</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <form action="create.php" method="POST" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Wishlist Name</label>
                        <input type="text" name="wishlist_name" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" placeholder="Enter wishlist name" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Category</label>
                        <select name="category" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" required>
                            <option value="Electronics">📱 Electronics</option>
                            <option value="Fashion">👕 Fashion</option>
                            <option value="Home & Living">🏠 Home & Living</option>
                            <option value="Books & Media">📚 Books & Media</option>
                            <option value="Sports & Outdoors">⚽ Sports & Outdoors</option>
                            <option value="Beauty & Health">💄 Beauty & Health</option>
                            <option value="Toys & Games">🎮 Toys & Games</option>
                            <option value="Food & Beverages">🍕 Food & Beverages</option>
                            <option value="Travel">✈️ Travel</option>
                            <option value="Other">📦 Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Item Name</label>
                        <input type="text" name="item_name" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" placeholder="Enter item name" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Price Range</label>
                        <input type="text" name="price_range" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" placeholder="e.g., $50-$100">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="description" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" placeholder="Enter item description" rows="4"></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Priority</label>
                        <select name="priority" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" required>
                            <option value="High">🔥 High Priority</option>
                            <option value="Medium" selected>⚡ Medium Priority</option>
                            <option value="Low">⏳ Low Priority</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" required>
                            <option value="Active" selected>Active</option>
                            <option value="Fulfilled">Fulfilled</option>
                            <option value="Removed">Removed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Image URL</label>
                    <div class="space-y-4">
                        <input type="url" name="image_url" id="image_url" class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0F2C59] focus:border-[#0F2C59] outline-none" placeholder="Enter image URL">
                        <img id="image_preview" class="preview-image mx-auto" src="" alt="Preview">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="view.php" class="text-[#0F2C59] hover:text-[#0F2C59]/80 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Back to Wishlist
                    </a>
                    <button type="submit" class="bg-[#0F2C59] text-white px-6 py-3 rounded-lg hover:bg-[#0F2C59]/90 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Add to Wishlist
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Image preview functionality
        document.getElementById('image_url').addEventListener('input', function() {
            const preview = document.getElementById('image_preview');
            const url = this.value;
            
            if (url) {
                preview.src = url;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html> 