<?php
require_once '../php/helpers/sessionConfig.php';
require_once '../database/db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'customer')) {
    header('Location: ../pages');
    exit();
}
if ($_SESSION['role'] !== 'staff' && $_SESSION['role'] == 'customer') {
    header('Location: ../pages/dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'staff'");
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent orders
$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.order_date DESC 
    LIMIT 10
");
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user count
$stmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM users WHERE role = 'customer'");
$stmt->execute();
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// Fetch order count
$stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders");
$stmt->execute();
$order_count = $stmt->fetch(PDO::FETCH_ASSOC)['order_count'];

// Fetch total revenue
$stmt = $pdo->prepare("SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'completed'");
$stmt->execute();
$total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];

// Fetch monthly revenue for the past 6 months
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(order_date, '%Y-%m') as month, SUM(total_amount) as revenue
    FROM orders
    WHERE status = 'completed' AND order_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month ASC
");
$stmt->execute();
$monthly_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch order status counts
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count
    FROM orders
    GROUP BY status
");
$stmt->execute();
$order_status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users for user management
$stmt = $pdo->prepare("SELECT user_id, name, email, role FROM users ORDER BY name ASC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch inventory items
$stmt = $pdo->prepare("
    SELECT i.item_id, i.name, inv.quantity
    FROM items i
    LEFT JOIN inventory inv ON i.item_id = inv.item_id
    ORDER BY i.name ASC
");
$stmt->execute();
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$chart_labels = [];
$chart_data = [];
foreach ($monthly_revenue as $row) {
    $chart_labels[] = date('M Y', strtotime($row['month']));
    $chart_data[] = $row['revenue'];
}

$status_labels = [];
$status_data = [];
foreach ($order_status_counts as $row) {
    $status_labels[] = ucfirst($row['status']);
    $status_data[] = $row['count'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brew Haven Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="message-container"></div>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Dashboard</h2>
                <button class="sidebar-toggle" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#overview" class="active" data-section="overview"><i class="fas fa-chart-line"></i> <span>Overview</span></a></li>
                    <li><a href="#user-management" data-section="user-management"><i class="fas fa-users"></i> <span>User Management</span></a></li>
                    <li><a href="#order-management" data-section="order-management"><i class="fas fa-shopping-cart"></i> <span>Order Management</span></a></li>
                    <li><a href="#inventory" data-section="inventory"><i class="fas fa-boxes"></i> <span>Inventory</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <section id="overview" class="dashboard-section active">
                <h2>Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Users</h3>
                        <p class="count-animation" data-value="<?php echo $user_count; ?>">0</p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Orders</h3>
                        <p class="count-animation" data-value="<?php echo $order_count; ?>">0</p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Revenue</h3>
                        <p class="count-animation" data-value="<?php echo $total_revenue; ?>">$0</p>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </section>

            <section id="user-management" class="dashboard-section">
                <h2>User Management</h2>
                <div class="user-list">
                    <!-- User list will be populated dynamically -->
                </div>
            </section>

            <section id="order-management" class="dashboard-section">
                <h2>Order Management</h2>
                <div class="order-list">
                    <!-- Order list will be populated dynamically -->
                </div>
            </section>

            <section id="inventory" class="dashboard-section">
                <h2>Inventory Management</h2>
                <div class="inventory-list">
                    <!-- Inventory list will be populated dynamically -->
                </div>
            </section>
        </main>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const chartLabels = <?php echo json_encode($chart_labels); ?>;
        const chartData = <?php echo json_encode($chart_data); ?>;
        const statusLabels = <?php echo json_encode($status_labels); ?>;
        const statusData = <?php echo json_encode($status_data); ?>;
        const users = <?php echo json_encode($users); ?>;
        const recentOrders = <?php echo json_encode($recent_orders); ?>;
        const inventory = <?php echo json_encode($inventory); ?>;
    </script>
    <script src="../assets/js/admin-dashboard.js"></script>
</body>
</html>