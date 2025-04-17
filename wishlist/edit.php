<?php
session_start();

include '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$error = null;
$success = null;
$item = null;

if ($item_id <= 0) {
    header("Location: view.php?error=Invalid item ID");
    exit();
}

// Re-open connection if needed (e.g., if closed after POST)
if ($conn->connect_error) {
    include '../db.php'; // Re-include if connection was closed
}

// Fetch item details - Corrected to fetch 'name'
$query = "SELECT * FROM wishlist WHERE id = ? AND user_id = ?";
$stmt_fetch = $conn->prepare($query);
if ($stmt_fetch === false) {
    error_log("SQL Prepare Error (FETCH): " . $conn->error);
    // Redirect or display a generic error; avoid showing SQL errors directly
    header("Location: view.php?error=Database error"); 
    exit();
}
$stmt_fetch->bind_param("ii", $item_id, $user_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
if ($result->num_rows === 1) {
    $item = $result->fetch_assoc();
} else {
    header("Location: view.php?error=Item not found or access denied");
    exit();
}
$stmt_fetch->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']); // Use item_name from form
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $priority = $_POST['priority'];
    $price_range = trim($_POST['price_range']);
    $image_url = trim($_POST['image_url']);
    $status = $_POST['status'];

    // Validate required fields
    if (empty($item_name)) {
        $error = "Item name is required!";
    } else {
        // Update item details - Corrected to update 'name' column
        $update_query = "UPDATE wishlist SET name = ?, category = ?, description = ?, priority = ?, price_range = ?, image_url = ?, status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        
        if ($update_stmt === false) {
            error_log("SQL Prepare Error (UPDATE): " . $conn->error);
            $error = "An error occurred while preparing the update.";
        } else {
            // Corrected bind_param types and order
            $update_stmt->bind_param("sssssssii", $item_name, $category, $description, $priority, $price_range, $image_url, $status, $item_id, $user_id);
            
            if ($update_stmt->execute()) {
                $success = "Wishlist item updated successfully!";
                // Re-fetch item details to show updated values immediately
                $stmt_refetch = $conn->prepare("SELECT * FROM wishlist WHERE id = ? AND user_id = ?");
                $stmt_refetch->bind_param("ii", $item_id, $user_id);
                $stmt_refetch->execute();
                $item = $stmt_refetch->get_result()->fetch_assoc(); // Update the $item array
                $stmt_refetch->close();
                // Use JavaScript to show success message and then redirect
                // echo "<script>setTimeout(() => { window.location.href = 'view.php?id=".$item_id."&success=1'; }, 1500);</script>"; // Redirect after delay
            } else {
                error_log("SQL Execute Error (UPDATE): " . $update_stmt->error);
                $error = "Failed to update item. Please try again.";
            }
            $update_stmt->close();
        }
    }
}

$conn->close(); // Close connection after processing
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Wish - Wishlist App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <style>
        /* Add overlay and background styles */
        body::before {
            content: '';
            position: fixed; 
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 44, 89, 0.5); /* Semi-transparent overlay */
            z-index: -1; 
            opacity: 0; 
            animation: fadeInBg 1.5s ease-out forwards;
        }
        body {
            background-image: url('../assets/img/1.jpg'); 
            background-color: #E1F0FF; /* Fallback */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            z-index: 0;
            opacity: 0;
            animation: fadeInBg 1.5s ease-out forwards;
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
        /* Consistent focus styles */
        .input-focus:focus {
            border-color: #0F2C59;
            box-shadow: 0 0 0 3px rgba(15, 44, 89, 0.3);
            outline: none;
        }
        /* Form container style */
        .form-card {
            backdrop-filter: blur(5px);
            background: rgba(255, 255, 255, 0.95);
            position: relative; /* Ensure it sits above the pseudo-element */
            z-index: 1;
        }
        .main-btn {
            background-color: #0F2C59;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .main-btn:hover {
            background-color: #0a1e3f;
            transform: translateY(-2px);
        }
        .link-color {
            color: #0F2C59;
        }
        .link-color:hover {
            color: #0a1e3f;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 0.5rem;
            display: <?php echo ($item['image_url'] ?? null) ? 'block' : 'none'; ?>; /* Use null coalescing */
        }
    </style>
</head>
<body class="min-h-screen flex p-4">
    <!-- Sidebar remains the same -->
    <aside class="w-64 bg-[#0F2C59]/95 backdrop-blur-sm shadow-lg flex flex-col justify-between fixed h-screen left-0 z-20">
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
                 <a href="view.php" class="flex items-center px-4 py-3 text-white bg-white/10 rounded-lg"> <!-- Assuming view.php is the active page when editing -->
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
         <!-- Profile remains the same -->
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
                    <i class="fas fa-pencil-alt text-[#0F2C59] text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-[#0F2C59]">Edit Your Wish</h2>
                <p class="text-gray-600 mt-2">Update your wishlist item details</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg flex items-center gap-3 animate-fadeIn" style="animation-delay: 0.6s;">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg flex items-center gap-3 animate-fadeIn" style="animation-delay: 0.6s;">
                    <i class="fas fa-check-circle"></i>
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit.php?id=<?php echo $item_id; ?>" class="space-y-6" onsubmit="return showLoading()"> 
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Item Name</label>
                        <input type="text" name="item_name" 
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus" 
                            value="<?php echo htmlspecialchars($item['name'] ?? ''); ?>"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Category</label>
                        <select name="category" 
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus bg-white">
                            <option value="Electronics" <?php echo ($item['category'] ?? '') === 'Electronics' ? 'selected' : ''; ?>>📱 Electronics</option>
                            <option value="Fashion" <?php echo ($item['category'] ?? '') === 'Fashion' ? 'selected' : ''; ?>>👕 Fashion</option>
                            <option value="Home & Living" <?php echo ($item['category'] ?? '') === 'Home & Living' ? 'selected' : ''; ?>>🏠 Home & Living</option>
                            <option value="Books & Media" <?php echo ($item['category'] ?? '') === 'Books & Media' ? 'selected' : ''; ?>>📚 Books & Media</option>
                            <option value="Sports & Outdoors" <?php echo ($item['category'] ?? '') === 'Sports & Outdoors' ? 'selected' : ''; ?>>⚽ Sports & Outdoors</option>
                            <option value="Beauty & Health" <?php echo ($item['category'] ?? '') === 'Beauty & Health' ? 'selected' : ''; ?>>💄 Beauty & Health</option>
                            <option value="Toys & Games" <?php echo ($item['category'] ?? '') === 'Toys & Games' ? 'selected' : ''; ?>>🎮 Toys & Games</option>
                            <option value="Food & Beverages" <?php echo ($item['category'] ?? '') === 'Food & Beverages' ? 'selected' : ''; ?>>🍕 Food & Beverages</option>
                            <option value="Travel" <?php echo ($item['category'] ?? '') === 'Travel' ? 'selected' : ''; ?>>✈️ Travel</option>
                            <option value="Other" <?php echo ($item['category'] ?? '') === 'Other' ? 'selected' : ''; ?>>📦 Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="description" id="description" maxlength="200"
                        class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus" 
                        oninput="updateCharCount()" rows="4"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                    <p id="charCount" class="text-right text-sm text-gray-500 mt-1"><?php echo strlen($item['description'] ?? ''); ?>/200</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Priority</label>
                        <select name="priority" 
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus bg-white">
                            <option value="High" <?php echo ($item['priority'] ?? '') === 'High' ? 'selected' : ''; ?>>🔥 High Priority</option>
                            <option value="Medium" <?php echo ($item['priority'] ?? '') === 'Medium' ? 'selected' : ''; ?>>⚡ Medium Priority</option>
                            <option value="Low" <?php echo ($item['priority'] ?? '') === 'Low' ? 'selected' : ''; ?>>⏳ Low Priority</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" 
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus bg-white">
                            <option value="Active" <?php echo ($item['status'] ?? '') === 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Fulfilled" <?php echo ($item['status'] ?? '') === 'Fulfilled' ? 'selected' : ''; ?>>Fulfilled</option>
                           </select>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Price Range</label>
                    <input type="text" name="price_range" 
                        class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus" 
                        value="<?php echo htmlspecialchars($item['price_range'] ?? ''); ?>"
                        placeholder="e.g., $50-$100">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Image URL</label>
                    <div class="space-y-3">
                        <input type="url" name="image_url" id="image_url" 
                            class="w-full px-4 py-3 mt-1 border border-gray-300 rounded-lg input-focus" 
                            value="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>"
                            placeholder="Enter image URL (optional)">
                        <img id="image_preview" class="preview-image mx-auto border border-gray-200 shadow-sm" 
                            src="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>" 
                            alt="Image Preview">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="view.php" class="link-color hover:underline transition flex items-center gap-2">
                         <i class="fas fa-arrow-left"></i>
                         Back to Wishlist
                     </a>
                    <button type="submit" id="submitButton" 
                        class="main-btn w-auto py-3 px-6 text-white rounded-lg transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Update Wish
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Script remains the same -->
    <script>
        function updateCharCount() {
            const textarea = document.getElementById('description');
            const charCount = document.getElementById('charCount');
            charCount.textContent = `${textarea.value.length}/200`;
        }

        function showLoading() {
            const button = document.getElementById('submitButton');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            button.disabled = true;
            button.classList.add("opacity-70", "cursor-not-allowed");
            return true;
        }

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

        // Initial character count update on page load
        updateCharCount(); 
    </script>
</body>
</html> 