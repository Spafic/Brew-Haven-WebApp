document.addEventListener('DOMContentLoaded', function () {
    initializeSidebar();
    initializeNavigation();
    initializeCharts();
    initializeDataManagement();
});

function initializeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const mainContent = document.querySelector('.main-content');

    sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('sidebar-closed');
    });
}

function initializeNavigation() {
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const sections = document.querySelectorAll('.dashboard-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);

            // Update URL hash without scrolling
            history.pushState(null, null, `#${targetId}`);

            updateActiveSection(targetId);
        });
    });

    // Handle initial load and back/forward navigation
    window.addEventListener('popstate', handlePopState);
    handlePopState();

    function handlePopState() {
        const hash = window.location.hash.substring(1);
        updateActiveSection(hash || 'overview');
    }

    function updateActiveSection(sectionId) {
        sections.forEach(section => section.classList.remove('active'));
        navLinks.forEach(navLink => navLink.classList.remove('active'));

        const targetSection = document.getElementById(sectionId);
        const targetLink = document.querySelector(`.sidebar-nav a[href="#${sectionId}"]`);

        if (targetSection && targetLink) {
            targetSection.classList.add('active');
            targetLink.classList.add('active');
        }
    }
}

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Monthly Revenue',
                data: chartData,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Order Status Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    'rgb(255, 205, 86)',
                    'rgb(54, 162, 235)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 99, 132)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${percentage}% (${value})`;
                        }
                    }
                }
            }
        }
    });
}

function initializeDataManagement() {
    initializeUserManagement();
    initializeOrderManagement();
    initializeInventoryManagement();
}

function initializeUserManagement() {
    const userList = document.querySelector('.user-list');
    renderUserList();

    function renderUserList() {
        userList.innerHTML = '';
        users.forEach(user => {
            const userElement = createUserElement(user);
            userList.appendChild(userElement);
        });
        addUserManagementListeners();
    }

    function createUserElement(user) {
        const userElement = document.createElement('div');
        userElement.classList.add('user-item');
        userElement.innerHTML = `
            <p><strong>${user.name}</strong> (${user.email})</p>
            <p>Role: ${user.role}</p>
            <button class="btn-edit-user" data-id="${user.user_id}">Edit</button>
            <button class="btn-delete-user" data-id="${user.user_id}">Delete</button>
        `;
        return userElement;
    }

    function addUserManagementListeners() {
        const editButtons = document.querySelectorAll('.btn-edit-user');
        const deleteButtons = document.querySelectorAll('.btn-delete-user');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                openUserEditModal(userId);
            });
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                confirmUserDeletion(userId);
            });
        });
    }

    function openUserEditModal(userId) {
        const user = users.find(u => u.user_id == userId);
        if (!user) return;

        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit User</h2>
                <form id="edit-user-form">
                    <input type="hidden" name="user_id" value="${user.user_id}">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="${user.name}" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="${user.email}" required>
                    <label for="role">Role:</label>
                    <select id="role" name="role">
                        <option value="customer" ${user.role === 'customer' ? 'selected' : ''}>Customer</option>
                        <option value="staff" ${user.role === 'staff' ? 'selected' : ''}>Staff</option>
                    </select>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        `;

        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('.close');
        closeBtn.addEventListener('click', () => modal.remove());

        const form = modal.querySelector('#edit-user-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            updateUser(Object.fromEntries(formData));
            modal.remove();
        });
    }

    function updateUser(userData) {
        const index = users.findIndex(u => u.user_id == userData.user_id);
        if (index !== -1) {
            users[index] = { ...users[index], ...userData };
            renderUserList();
            showMessage('User updated successfully', 'success');
        }
    }

    function confirmUserDeletion(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            deleteUser(userId);
        }
    }

    function deleteUser(userId) {
        users = users.filter(u => u.user_id != userId);
        renderUserList();
        showMessage('User deleted successfully', 'success');
    }
}

function initializeOrderManagement() {
    const orderList = document.querySelector('.order-list');
    renderOrderList();

    function renderOrderList() {
        orderList.innerHTML = '';
        recentOrders.forEach(order => {
            const orderElement = createOrderElement(order);
            orderList.appendChild(orderElement);
        });
        addOrderManagementListeners();
    }

    function createOrderElement(order) {
        const orderElement = document.createElement('div');
        orderElement.classList.add('order-item');
        orderElement.innerHTML = `
            <p><strong>Order #${order.order_id}</strong> by ${order.user_name}</p>
            <p>Date: ${new Date(order.order_date).toLocaleDateString()}</p>
            <p>Status: ${order.status}</p>
            <p>Total: $${parseFloat(order.total_amount).toFixed(2)}</p>
            <button class="btn-update-status" data-id="${order.order_id}">Update Status</button>
        `;
        return orderElement;
    }

    function addOrderManagementListeners() {
        const updateStatusButtons = document.querySelectorAll('.btn-update-status');

        updateStatusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                openOrderStatusModal(orderId);
            });
        });
    }

    function openOrderStatusModal(orderId) {
        const order = recentOrders.find(o => o.order_id == orderId);
        if (!order) return;

        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Update Order Status</h2>
                <form id="update-order-form">
                    <input type="hidden" name="order_id" value="${order.order_id}">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="preparing" ${order.status === 'preparing' ? 'selected' : ''}>Preparing</option>
                        <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completed</option>
                        <option value="canceled" ${order.status === 'canceled' ? 'selected' : ''}>Canceled</option>
                    </select>
                    <button type="submit">Update Status</button>
                </form>
            </div>
        `;

        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('.close');
        closeBtn.addEventListener('click', () => modal.remove());

        const form = modal.querySelector('#update-order-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            updateOrderStatus(Object.fromEntries(formData));
            modal.remove();
        });
    }

    function updateOrderStatus(orderData) {
        const index = recentOrders.findIndex(o => o.order_id == orderData.order_id);
        if (index !== -1) {
            recentOrders[index].status = orderData.status;
            renderOrderList();
            showMessage('Order status updated successfully', 'success');
        }
    }
}

function initializeInventoryManagement() {
    const inventoryList = document.querySelector('.inventory-list');
    renderInventoryList();

    function renderInventoryList() {
        inventoryList.innerHTML = '';
        inventory.forEach(item => {
            const itemElement = createInventoryItemElement(item);
            inventoryList.appendChild(itemElement);
        });
        addInventoryManagementListeners();
    }

    function createInventoryItemElement(item) {
        const itemElement = document.createElement('div');
        itemElement.classList.add('inventory-item');
        itemElement.innerHTML = `
            <p><strong>${item.name}</strong></p>
            <p>Quantity: ${item.quantity}</p>
            <button class="btn-update-inventory" data-id="${item.item_id}">Update</button>
        `;
        return itemElement;
    }

    function addInventoryManagementListeners() {
        const updateInventoryButtons = document.querySelectorAll('.btn-update-inventory');

        updateInventoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                openInventoryUpdateModal(itemId);
            });
        });
    }

    function openInventoryUpdateModal(itemId) {
        const item = inventory.find(i => i.item_id == itemId);
        if (!item) return;

        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Update Inventory</h2>
                <form id="update-inventory-form">
                    <input type="hidden" name="item_id" value="${item.item_id}">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="${item.quantity}" required min="0">
                    <button type="submit">Update Quantity</button>
                </form>
            </div>
        `;

        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('.close');
        closeBtn.addEventListener('click', () => modal.remove());

        const form = modal.querySelector('#update-inventory-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            updateInventoryItem(Object.fromEntries(formData));
            modal.remove();
        });
    }

    function updateInventoryItem(itemData) {
        const index = inventory.findIndex(i => i.item_id == itemData.item_id);
        if (index !== -1) {
            inventory[index].quantity = parseInt(itemData.quantity);
            renderInventoryList();
            showMessage('Inventory updated successfully', 'success');
        }
    }
}

function showMessage(message, type = 'info') {
    const messageContainer = document.getElementById('message-container');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message', type);
    messageElement.textContent = message;
    messageContainer.appendChild(messageElement);

    setTimeout(() => {
        messageElement.classList.add('show');
        setTimeout(() => {
            messageElement.classList.remove('show');
            setTimeout(() => {
                messageElement.remove();
            }, 300);
        }, 3000);
    }, 100);
}