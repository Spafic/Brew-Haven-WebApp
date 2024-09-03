<?php
require_once '../php/helpers/sessionConfig.php'; // Assuming you have a config file with session configuration
require_once '../database/db_connection.php'; // Assuming you have a config file with database connection details

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch menu items
$stmt = $pdo->prepare("SELECT * FROM items WHERE available = 1");
$stmt->execute();
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch order history
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 10");
$stmt->execute([$user_id]);
$order_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brew Haven Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>
<div id="message-container"></div>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="parent-container" style="display: flex; justify-content: center;">
                    <button class="sidebar-toggle" style="margin-bottom:50px; margin-top:20px;" aria-label="Toggle Sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="profile-image-container">
                    <img src="<?php echo "../" . htmlspecialchars($user['profile_image'] ?? '../assets/imgs/default-profile.png'); ?>"
                        alt="Profile Picture" class="profile-image">
                    </div>
                    <button class="change-profile-image" title="Change Profile Picture">
                        <i class="fas fa-camera"></i>
                    </button>
                <h2 class="user-name"><?php echo htmlspecialchars($user['name']); ?></h2>
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="#user-info" class="active" data-section="user-info"><i class="fas fa-user"></i>
                                <span>User Info</span></a></li>
                        <li><a href="#menu" data-section="menu"><i class="fas fa-utensils"></i> <span>Menu</span></a>
                        </li>
                        <li><a href="#cart" data-section="cart"><i class="fas fa-shopping-cart"></i>
                                <span>Cart</span></a>
                        </li>
                        <li><a href="#order-history" data-section="order-history"><i class="fas fa-history"></i>
                                <span>Order
                                    History</span></a></li>
                    </ul>
                </nav>
        </aside>

        <main class="main-content">
            <section id="user-info" class="dashboard-section active">
                <h2>Your Information</h2>
                <div class="user-info-grid">
                    <div class="user-info-item">
                        <span class="user-info-label">Name:</span>
                        <span class="user-info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                        <button class="btn-change" data-field="name">Change</button>
                    </div>
                    <div class="user-info-item">
                        <span class="user-info-label">Email:</span>
                        <span class="user-info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="user-info-item">
                        <span class="user-info-label">Phone:</span>
                        <span class="user-info-value"><?php echo htmlspecialchars($user['phone_number']); ?></span>
                        <button class="btn-change" data-field="phone">Change</button>
                    </div>
                    <div class="user-info-item">
                        <span class="user-info-label">Address:</span>
                        <span class="user-info-value"><?php echo htmlspecialchars($user['address']); ?></span>
                        <button class="btn-change" data-field="address">Change</button>
                    </div>
                </div>
                <button id="change-password" class="btn-change-password">Change Password</button>
            </section>

            <section id="menu" class="dashboard-section">
                <h2>Menu</h2>
                <div class="menu-grid">
                    <?php foreach ($menu_items as $item): ?>
                        <div class="menu-item" data-id="<?php echo $item['item_id']; ?>">
                            <img src="<?php echo htmlspecialchars($item['product_image']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>" class="menu-item-image">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?></p>
                            <div class="menu-item-actions">
                                <input type="number" min="1" value="1" class="item-quantity">
                                <button class="btn-add-to-cart">Add to Cart</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="cart" class="dashboard-section">
                <h2>Your Cart</h2>
                <div class="cart-items">
                    <!-- Cart items will be dynamically added here -->
                </div>
                <div class="cart-total">
                    <span>Total:</span>
                    <span id="cart-total-amount">$0.00</span>
                </div>
                <button id="place-order" class="btn-place-order">Place Order</button>
            </section>

            <section id="order-history" class="dashboard-section">
                <h2>Order History</h2>
                <div class="order-history-container">
                    <?php foreach ($order_history as $order): ?>
                        <div class="order-item">
                            <p>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></p>
                            <p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
                            <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
                            <button class="btn-order-details" data-order-id="<?php echo $order['order_id']; ?>">View
                                Details</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>

<!-- Password Change Modal -->
<div id="password-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Change Password</h2>
            <form id="password-change-form">
                <div class="form-group">
                    <label for="current-password">Current Password:</label>
                    <input type="password" id="current-password" name="current-password" required>
                </div>
                <div class="form-group">
                    <label for="new-password">New Password:</label>
                    <input type="password" id="new-password" name="new-password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm New Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
                <button type="submit" class="btn-change-password">Change Password</button>
            </form>
        </div>
    </div>


<!-- User Info Change Modal -->
<div id="user-info-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update Information</h2>
        <form id="user-info-form">
            <div class="form-group">
                <label for="update-field">Field:</label>
                <span id="update-field"></span>
            </div>
            <div class="form-group">
                <label for="update-value">New Value:</label>
                <input type="text" id="update-value" name="update-value" required>
            </div>
            <button type="submit" class="btn-update">Update</button>
        </form>
    </div>
</div>

    <script src="../assets/js/dashboard.js"></script>
</body>

</html>