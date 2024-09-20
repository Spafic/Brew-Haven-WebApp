<?php
require_once '../php/helpers/sessionConfig.php';
require_once '../database/db_connection.php';

// Check if user is logged in and is a staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../pages');
    exit();
}

require_once '../php/admin/users_handler.php';
require_once '../php/admin/items_handler.php';
require_once '../php/admin/orders_handler.php';
require_once '../php/admin/stats_handler.php';

// Fetch data
$stats = fetchStats($pdo);
$users = fetchUsers($pdo);
$items = fetchItems($pdo);
$orders = fetchOrders($pdo);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link rel="stylesheet" href="../assets/css/staffDashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <span class="user-name">Staff</span>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#stats" class="active"><i class="fas fa-chart-line"></i> <span>Statistics</span></a></li>
                    <li><a href="#users"><i class="fas fa-users"></i> <span>Users</span></a></li>
                    <li><a href="#items"><i class="fas fa-boxes"></i> <span>Items</span></a></li>
                    <li><a href="#orders"><i class="fas fa-file-invoice"></i> <span>Orders</span></a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Stats Section -->
            <section id="stats" class="dashboard-section active">
                <h2>Statistics Overview</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <h3>Total Users</h3>
                        <i class="fas fa-user"></i>
                        <p id="total-users" data-value="<?= $stats['users'] ?>">0</p>
                    </div>
                    <div class="stat-item">
                        <h3>Total Items</h3>
                        <i class="fas fa-box"></i>
                        <p id="total-items" data-value="<?= $stats['items'] ?>">0</p>
                    </div>
                    <div class="stat-item">
                        <h3>Completed Orders</h3>
                        <i class="fas fa-check-circle"></i>
                        <p id="completed-orders" data-value="<?= $stats['orders'] ?>">0</p>
                    </div>
                    <div class="stat-item">
                        <h3>Total Profit</h3>
                        <i class="fas fa-dollar-sign"></i>
                        <p id="total-profit" data-value="<?= $stats['orders_profit'] ?>">$0.00</p>
                    </div>
                </div>
                <div class="charts-container">
                    <div class="chart-item">
                        <h3>Monthly Overview</h3>
                        <canvas id="monthlyOverviewChart"></canvas>
                    </div>
                    <div class="chart-item">
                        <h3>Profit Distribution</h3>
                        <canvas id="profitDistributionChart"></canvas>
                    </div>
                    <div class="chart-item">
                        <h3>Top Selling Items</h3>
                        <canvas id="topSellingItemsChart"></canvas>
                    </div>
                    <div class="chart-item">
                        <h3>Order Status Distribution</h3>
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </section>

            <!-- Users Section -->
            <section id="users" class="dashboard-section">
                <h2>Members</h2>
                <div id="user-info-grid" class="user-info-grid">
                    <?php foreach ($users as $user) : ?>
                        <div class="user-info-item">
                            <div class="user-info-header">
                                <h3><?= htmlspecialchars($user['name']) ?></h3>
                                <button class="btn-edit-user" data-id="<?= $user['user_id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                            <div class="user-info-content">
                                <p><span class="user-info-label">Email:</span> <span class="user-info-value"><?= htmlspecialchars($user['email']) ?></span></p>
                                <p><span class="user-info-label">Phone:</span> <span class="user-info-value"><?= htmlspecialchars($user['phone_number']) ?></span></p>
                                <p><span class="user-info-label">Role:</span> <span class="user-info-value"><?= ucfirst(htmlspecialchars($user['role'])) ?></span></p>
                                <?php if (isset($user['address']) && $user['address'] !== ''): ?>
                                    <p><span class="user-info-label">Address:</span> <span class="user-info-value"><?= htmlspecialchars($user['address']) ?></span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Items Section -->
            <section id="items" class="dashboard-section">
                <h2>Items</h2>
                <div class="add-button-container">
                    <button id="add-item-btn" class="btn-add"><i class="fas fa-plus"></i> Add New Item</button>
                </div>
                <div id="menu-grid" class="menu-grid">
                    <?php foreach ($items as $item) :
                        $categoryClass = strtolower($item['category']) == 'hot' ? 'hot' : (strtolower($item['category']) == 'cold' ? 'cold' : '');
                        $availabilityClass = $item['available'] ? 'available' : 'not-available';
                    ?>
                        <div class="menu-item">
                            <img src="../<?= $item['product_image'] ?>" alt="<?= $item['name'] ?>" class="menu-item-image">
                            <div class="menu-item-content">
                                <h3><?= $item['name'] ?></h3>
                                <p><?= $item['description'] ?></p>
                                <p class="price">$<?= number_format($item['price'], 2) ?></p>
                                <span class="category <?= $categoryClass ?>"><?= $item['category'] ?></span>
                                <p class="availability <?= $availabilityClass ?>"><?= $item['available'] ? 'Available' : 'Not Available' ?></p>
                                <div class="menu-item-actions">
                                    <button class="btn-edit-item" data-id="<?= $item['item_id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                                    <button class="btn-remove-item" data-id="<?= $item['item_id'] ?>"><i class="fas fa-trash"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Orders Section -->
            <section id="orders" class="dashboard-section">
                <h2>Orders</h2>
                <div id="orders-list" class="orders-list">
                    <?php foreach ($orders as $order) : ?>
                        <div class="order-item">
                            <span class="order-id">Order #<?= $order['order_id'] ?></span>
                            <span class="order-date"><?= $order['order_date'] ?></span>
                            <span class="order-status"><?= ucfirst($order['status']) ?></span>
                            <span class="order-total">$<?= number_format($order['total_amount'], 2) ?></span>
                            <span class="order-user"><?= $order['user_name'] ?></span>
                            <button class="btn-view-order" data-id="<?= $order['order_id'] ?>"><i class="fas fa-eye"></i> View</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- User edit modal -->
    <div id="user-edit-modal" class="modal">
        <div class="modal-content edit-modal">
            <div class="modal-header">
                <h2>Edit Staff Member</h2>
                <span class="close">&times;</span>
            </div>
            <form id="user-edit-form" class="compact-form">
                <input type="hidden" id="user-id" name="user_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="user-name">Name:</label>
                        <input type="text" id="user-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Email:</label>
                        <input type="email" id="user-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="user-phone">Phone:</label>
                        <input type="text" id="user-phone" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="user-role">Role:</label>
                        <select id="user-role" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="user-address">Address:</label>
                    <input type="text" id="user-address" name="address">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-update">Update</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Item Add/Edit Modal -->
    <div id="item-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="item-modal-title">Add/Edit Item</h2>
                <span class="close">&times;</span>
            </div>
            <form id="item-form" enctype="multipart/form-data">
                <input type="hidden" id="item-id" name="item_id">
                <div class="form-group">
                    <label for="item-name">Name:</label>
                    <input type="text" id="item-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="item-price">Price:</label>
                    <input type="number" id="item-price" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="item-category">Category:</label>
                    <input type="text" id="item-category" name="category" required>
                </div>
                <div class="form-group">
                    <label for="item-description">Description:</label>
                    <textarea id="item-description" name="description" required rows="2" style="width: 100%; resize: none;"></textarea>
                </div>
                <div class="form-group">
                    <label for="item-image">Product Image:</label>
                    <input type="file" id="item-image" name="product_image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="item-available">Available:</label>
                    <select id="item-available" name="available" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <button type="submit" class="btn-update">Save</button>
            </form>
        </div>
    </div>

    <!-- Order View Modal -->
    <div id="order-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Details</h2>
                <span class="close">&times;</span>
            </div>
            <div id="order-details"></div>
            <div class="form-group">
                <label for="order-status">Status:</label>
                <select id="order-status" name="status">
                    <option value="pending">Pending</option>
                    <option value="preparing">Preparing</option>
                    <option value="completed">Completed</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>
            <button id="update-order-status" class="btn-update">Update Status</button>
        </div>
    </div>


    <script src="../assets/js/staffDashboard.js"></script>
</body>

</html>