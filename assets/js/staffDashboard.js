document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    // sidebar toggle event listener
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });

    // Navigation
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const sections = document.querySelectorAll('.dashboard-section');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').slice(1);
            sections.forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(targetId).classList.add('active');
            navLinks.forEach(navLink => {
                navLink.classList.remove('active');
            });
            link.classList.add('active');
        });
    });

    // Modal functionality
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none'; // Hide modals initially
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
    });

    // Function to show a modal
    function showModal(modal) {
        modal.style.display = 'flex';
    }

    // Function to hide a modal
    function hideModal(modal) {
        modal.style.display = 'none';
    }

    document.querySelector('.modal-content').style.cssText = `
        max-height: 75vh;
        overflow-y: auto;
        width: 75%;
        max-width: 800px;
    `;
    const closeBtns = document.querySelectorAll('.close');

    function openModal(modal) {
        modal.style.display = 'block';
    }

    function closeModal(modal) {
        modal.style.display = 'none';
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal');
            closeModal(modal);
        });
    });

    window.addEventListener('click', (e) => {
        modals.forEach(modal => {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    // User edit functionality
    const userEditModal = document.getElementById('user-edit-modal');
    const userEditForm = document.getElementById('user-edit-form');

    // Use event delegation for edit buttons
    document.getElementById('user-info-grid').addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-edit-user') || e.target.closest('.btn-edit-user')) {
            const btn = e.target.classList.contains('btn-edit-user') ? e.target : e.target.closest('.btn-edit-user');
            const userId = btn.getAttribute('data-id');
            fetchUserDetails(userId);
        }
    });
    
    function fetchUserDetails(userId) {
        fetch(`../php/admin/users_handler.php?action=get_user&id=${userId}`)
            .then(response => response.json())
            .then(user => {
                document.getElementById('user-id').value = user.user_id;
                document.getElementById('user-name').value = user.name;
                document.getElementById('user-email').value = user.email;
                document.getElementById('user-phone').value = user.phone_number;
                document.getElementById('user-role').value = user.role;
                document.getElementById('user-address').value = user.address || '';
                openModal(userEditModal);
            })
            .catch(error => console.error('Error:', error));
    }
    
    function updateUserInList(formData) {
        const userId = formData.get('user_id');
        const userItem = document.querySelector(`.user-info-item button[data-id="${userId}"]`).closest('.user-info-item');
        
        userItem.querySelector('h3').textContent = formData.get('name');
        userItem.querySelector('.user-info-value:nth-of-type(1)').textContent = formData.get('email');
        userItem.querySelector('.user-info-value:nth-of-type(2)').textContent = formData.get('phone_number');
        userItem.querySelector('.user-info-value:nth-of-type(3)').textContent = formData.get('role');
        
        const addressElement = userItem.querySelector('.user-info-value.address');
        if (addressElement) {
            addressElement.textContent = formData.get('address') || 'N/A';
        } else if (formData.get('address')) {
            const newAddressElement = document.createElement('p');
            newAddressElement.innerHTML = `<span class="user-info-label">Address:</span> <span class="user-info-value address">${formData.get('address')}</span>`;
            userItem.querySelector('.user-info-content').appendChild(newAddressElement);
        }
    }
    

    userEditForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(userEditForm);
        formData.append('action', 'update_user');

        fetch('../php/admin/users_handler.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    closeModal(userEditModal);
                    updateUserInList(formData);
                } else {
                    alert('Failed to update user');
                }
            })
            .catch(error => console.error('Error:', error));
    });


// Item functionality
const itemModal = document.getElementById('item-modal');
const itemForm = document.getElementById('item-form');
const addItemBtn = document.getElementById('add-item-btn');
const editItemBtns = document.querySelectorAll('.btn-edit-item');
const removeItemBtns = document.querySelectorAll('.btn-remove-item');

addItemBtn.addEventListener('click', () => {
    itemForm.reset();
    document.getElementById('item-modal-title').textContent = 'Add New Item';
    document.getElementById('item-id').value = '';
    openModal(itemModal);
});

editItemBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const itemId = btn.getAttribute('data-id');
        fetchItemDetails(itemId);
    });
});

removeItemBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const itemId = btn.getAttribute('data-id');
        if (confirm('Are you sure you want to remove this item?')) {
            removeItem(itemId);
        }
    });
});

function fetchItemDetails(itemId) {
    fetch(`../php/admin/items_handler.php?action=get_item&id=${itemId}`)
        .then(response => response.json())
        .then(item => {
            document.getElementById('item-modal-title').textContent = 'Edit Item';
            document.getElementById('item-id').value = item.item_id;
            document.getElementById('item-name').value = item.name;
            document.getElementById('item-price').value = item.price;
            document.getElementById('item-category').value = item.category;
            document.getElementById('item-description').value = item.description;
            document.getElementById('item-available').value = item.available;
            openModal(itemModal);
        })
        .catch(error => console.error('Error:', error));
}

itemForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(itemForm);
    const itemId = formData.get('item_id');
    formData.append('action', itemId ? 'update_item' : 'add_item');

    fetch('../php/admin/items_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                closeModal(itemModal);
                if (itemId) {
                    updateItemInList(formData, result.imagePath);
                } else {
                    addItemToList(result.id, formData, result.imagePath);
                }
            } else {
                alert('Failed to save item');
            }
        })
        .catch(error => console.error('Error:', error));
});

function updateItemInList(formData, imagePath) {
    const itemId = formData.get('item_id');
    const itemElement = document.querySelector(`.menu-item button[data-id="${itemId}"]`).closest('.menu-item');
    itemElement.querySelector('h3').textContent = formData.get('name');
    itemElement.querySelector('p:nth-child(3)').textContent = formData.get('description');
    itemElement.querySelector('.price').textContent = `$${parseFloat(formData.get('price')).toFixed(2)}`;
    
    const categorySpan = itemElement.querySelector('.category');
    categorySpan.textContent = formData.get('category');
    categorySpan.className = `category ${getCategoryClass(formData.get('category'))}`;
    
    itemElement.querySelector('.availability').textContent = formData.get('available') === '1' ? 'Available' : 'Not Available';
    
    // Update image if a new one was uploaded
    if (imagePath) {
        itemElement.querySelector('img').src = imagePath;
    }
}

function addItemToList(itemId, formData, imagePath) {
    const menuGrid = document.getElementById('menu-grid');
    const newItem = document.createElement('div');
    newItem.className = 'menu-item';
    
    const imageUrl = imagePath || '/api/placeholder/200/200';
    const category = formData.get('category');

    newItem.innerHTML = `
        <img src="${imageUrl}" alt="${formData.get('name')}" class="menu-item-image">
        <div class="menu-item-content">
            <h3>${formData.get('name')}</h3>
            <p>${formData.get('description')}</p>
            <p class="price">$${parseFloat(formData.get('price')).toFixed(2)}</p>
            <span class="category ${getCategoryClass(category)}">${category}</span>
            <p class="availability">${formData.get('available') === '1' ? 'Available' : 'Not Available'}</p>
            <div class="menu-item-actions">
                <button class="btn-edit-item" data-id="${itemId}"><i class="fas fa-edit"></i> Edit</button>
                <button class="btn-remove-item" data-id="${itemId}"><i class="fas fa-trash"></i> Remove</button>
            </div>
        </div>
    `;
    menuGrid.appendChild(newItem);

    // Add event listeners to new buttons
    newItem.querySelector('.btn-edit-item').addEventListener('click', () => fetchItemDetails(itemId));
    newItem.querySelector('.btn-remove-item').addEventListener('click', () => {
        if (confirm('Are you sure you want to remove this item?')) {
            removeItem(itemId);
        }
    });
}

function getCategoryClass(category) {
    category = category.toLowerCase();
    return category === 'hot' ? 'hot' : (category === 'cold' ? 'cold' : '');
}

function removeItem(itemId) {
    fetch('../php/admin/items_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove_item&item_id=${itemId}`
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const itemElement = document.querySelector(`.menu-item button[data-id="${itemId}"]`).closest('.menu-item');
                itemElement.remove();
            } else {
                alert('Failed to remove item');
            }
        })
        .catch(error => console.error('Error:', error));
}


    // Order functionality
    const orderModal = document.getElementById('order-modal');
    const viewOrderBtns = document.querySelectorAll('.btn-view-order');
    const updateOrderStatusBtn = document.getElementById('update-order-status');

    viewOrderBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const orderId = btn.getAttribute('data-id');
            fetchOrderDetails(orderId);
        });
    });

    function fetchOrderDetails(orderId) {
        fetch(`../php/admin/orders_handler.php?action=get_order_details&id=${orderId}`)
            .then(response => response.json())
            .then(order => {
                const orderDetails = document.getElementById('order-details');
                orderDetails.innerHTML = `
                    <p><strong>Order ID:</strong> ${order.order_id}</p>
                    <p><strong>Date:</strong> ${order.order_date}</p>
                    <p><strong>Customer:</strong> ${order.user_name}</p>
                    <p><strong>Email:</strong> ${order.user_email}</p>
                    <p><strong>Phone:</strong> ${order.user_phone}</p>
                    <h3>Order Items:</h3>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${order.items.map(item => `
                                <tr>
                                    <td>${item.item_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>$${parseFloat(item.price).toFixed(2)}</td>
                                    <td>$${(item.quantity * item.price).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total Price:</strong></td>
                                <td><strong>$${parseFloat(order.total_amount).toFixed(2)}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                `;
                document.getElementById('order-status').value = order.status;
                updateOrderStatusBtn.setAttribute('data-id', order.order_id);
                openModal(orderModal);
            })
            .catch(error => console.error('Error:', error));
    }

    updateOrderStatusBtn.addEventListener('click', () => {
        const orderId = updateOrderStatusBtn.getAttribute('data-id');
        const newStatus = document.getElementById('order-status').value;
        updateOrderStatus(orderId, newStatus);
    });

    function updateOrderStatus(orderId, newStatus) {
        fetch('../php/admin/orders_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update_order_status&order_id=${orderId}&status=${newStatus}`
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    closeModal(orderModal);
                    updateOrderInList(orderId, newStatus);
                } else {
                    alert('Failed to update order status');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateOrderInList(orderId, newStatus) {
        const orderItem = document.querySelector(`.order-item button[data-id="${orderId}"]`).closest('.order-item');
        orderItem.querySelector('.order-status').textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    }

    // Refresh data periodically
    setInterval(refreshData, 60000); // Refresh every minute

    function refreshData() {
        fetch('../php/admin/stats_handler.php')
            .then(response => response.json())
            .then(stats => {
                document.getElementById('total-users').textContent = stats.users;
                document.getElementById('total-items').textContent = stats.items;
                document.getElementById('completed-orders').textContent = stats.orders;
                document.getElementById('total-profit').textContent = `$${parseFloat(stats.orders_profit).toFixed(2)}`;
            })
            .catch(error => console.error('Error:', error));
    }
    function animateCount(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Stats and Graphs ...

    fetch('../php/admin/stats_handler.php')
        .then(response => response.json())
        .then(stats => {
            animateCount(document.getElementById('total-users'), 0, stats.users, 2000);
            animateCount(document.getElementById('total-items'), 0, stats.items, 2000);
            animateCount(document.getElementById('completed-orders'), 0, stats.orders, 2000);

            const profitElement = document.getElementById('total-profit');
            let startProfit = 0;
            let endProfit = parseFloat(stats.orders_profit);

            // Counting animation for profit
            let profitInterval = setInterval(() => {
                startProfit += 5; // Adjust the speed here
                if (startProfit >= endProfit) {
                    startProfit = endProfit;
                    clearInterval(profitInterval);
                }
                profitElement.textContent = `$${startProfit.toFixed(2)}`;
            }, 30);
        })
        .catch(error => console.error('Error fetching stats:', error));

         // Count Animation Function
    function animateCount(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Stats Animations and Fetching 
    
    // Function to fetch stats and animate count
    function updateStats() {
        fetch('../php/admin/stats_handler.php')
            .then(response => response.json())
            .then(stats => {
                animateCount(document.getElementById('total-users'), 0, stats.users, 2000);
                animateCount(document.getElementById('total-items'), 0, stats.items, 2000);
                animateCount(document.getElementById('completed-orders'), 0, stats.orders, 2000);
                
                const profitElement = document.getElementById('total-profit');
                animateCount(profitElement, 0, parseFloat(stats.orders_profit), 2000);
            })
            .catch(error => console.error('Error fetching stats:', error));
    }

    // Call updateStats on load
    updateStats();
    
    
    // Chart functionality
    function initializeCharts() {
        fetch('../php/admin/chart_data_handler.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received chart data:', data); // For debugging

                createMonthlyOverviewChart(data.monthlyOverview);
                createProfitDistributionChart(data.profitDistribution);
                createTopSellingItemsChart(data.topSellingItems);
                createOrderStatusChart(data.orderStatusDistribution);
            })
            .catch(error => {
                console.error('Error fetching or processing chart data:', error);
                alert('An error occurred while loading the charts. Please try refreshing the page.');
            });
    }

    function createMonthlyOverviewChart(data) {
        const ctx = document.getElementById('monthlyOverviewChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.month),
                datasets: [{
                    label: 'Orders',
                    data: data.map(item => item.orders),
                    borderColor: '#3a1f0d',
                    tension: 0.1
                }, {
                    label: 'Revenue',
                    data: data.map(item => item.revenue),
                    borderColor: '#c7a17a',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function createProfitDistributionChart(data) {
        const ctx = document.getElementById('profitDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(item => item.category),
                datasets: [{
                    data: data.map(item => item.profit),
                    backgroundColor: ['#3a1f0d', '#c7a17a', '#e6b17e', '#f9f3e9']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    function createTopSellingItemsChart(data) {
        const ctx = document.getElementById('topSellingItemsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.name),
                datasets: [{
                    label: 'Units Sold',
                    data: data.map(item => item.total_sold),
                    backgroundColor: '#c7a17a'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function createOrderStatusChart(data) {
        const ctx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.map(item => item.status),
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: ['#3a1f0d', '#c7a17a', '#e6b17e', '#f9f3e9']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Call initializeCharts when the page loads
    initializeCharts();

});