document.addEventListener('DOMContentLoaded', function () {
    // Sidebar functionality
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const mainContent = document.querySelector('.main-content');

    sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('sidebar-closed');
    });

    // Navigation functionality
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const sections = document.querySelectorAll('.dashboard-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);

            sections.forEach(section => section.classList.remove('active'));
            document.getElementById(targetId).classList.add('active');

            navLinks.forEach(navLink => navLink.classList.remove('active'));
            this.classList.add('active');

            if (window.innerWidth <= 768) {
                sidebar.classList.add('closed');
                mainContent.classList.add('sidebar-closed');
            }
        });
    });

    // Responsive design adjustments
    function adjustForMobile() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('closed');
            mainContent.classList.add('sidebar-closed');
        } else {
            sidebar.classList.remove('closed');
            mainContent.classList.remove('sidebar-closed');
        }
    }

    window.addEventListener('resize', adjustForMobile);
    adjustForMobile(); // Call on page load

    // User info functionality
    const changeButtons = document.querySelectorAll('.btn-change');
    const userInfoModal = document.getElementById('user-info-modal');
    const userInfoForm = document.getElementById('user-info-form');
    const updateField = document.getElementById('update-field');
    const updateValue = document.getElementById('update-value');

    const nameRegex = /^[a-zA-Z\s]{2,50}$/;
    const phoneRegex = /^[0-9]{11}$/;
    const locationRegex = /^[a-zA-Z0-9\s,.-]{2,100}$/;
    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?!.*[<>'"/;`%])(?!.*\s).{8,}$/;

    changeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const field = this.dataset.field;
            const currentValue = this.previousElementSibling.textContent;
            updateField.textContent = field;
            updateValue.value = currentValue;
            userInfoModal.style.display = 'block';
        });
    });

    userInfoForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const field = updateField.textContent;
        const value = updateValue.value;

        if (!validateField(field, value)) {
            return;
        }

        const formData = new FormData();
        formData.append('field', field);
        formData.append('value', value);

        fetch('../php/updateUserInfo.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showMessage(data.error, 'error');
                } else {
                    document.querySelector(`[data-field="${field}"]`).previousElementSibling.textContent = data[field];
                    userInfoModal.style.display = 'none';
                    showMessage(`${field.charAt(0).toUpperCase() + field.slice(1)} updated successfully`, 'success');
                    if (field === 'name') {
                        updateUserName(data[field]);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating user information:', error);
                showMessage('An error occurred while updating the user information. Please try again later.', 'error');
            });
    });

    function updateUserName(newName) {
        const userNameElement = document.querySelector('.user-name');
        if (userNameElement) {
            userNameElement.textContent = newName;
        }
    }

    function validateField(field, value) {
        switch (field) {
            case 'name':
                if (!nameRegex.test(value)) {
                    showMessage('Name must be between 2 and 50 characters and contain only letters and spaces.', 'error');
                    return false;
                }
                break;
            case 'phone':
                if (!phoneRegex.test(value)) {
                    showMessage('Phone number must be 11 digits.', 'error');
                    return false;
                }
                break;
            case 'address':
                if (!locationRegex.test(value)) {
                    showMessage('Invalid address format. Please use only letters, numbers, spaces, commas, periods, and hyphens.', 'error');
                    return false;
                }
                break;
        }
        return true;
    }

    function sanitizeInput(input) {
        return input.replace(/[<>&'"]/g, function (c) {
            return {
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;',
                '&': '&amp;'
            }[c];
        });
    }

    // Password change functionality
    const changePasswordBtn = document.getElementById('change-password');
    const passwordModal = document.getElementById('password-modal');
    const passwordForm = document.getElementById('password-change-form');

    changePasswordBtn.addEventListener('click', function () {
        passwordModal.style.display = 'block';
    });

    passwordForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (!passwordRegex.test(newPassword)) {
            showMessage('Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*). It must not contain any spaces, angle brackets, or other special characters.', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            showMessage('New passwords do not match.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('field', 'password');
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        formData.append('confirm_password', confirmPassword);

        fetch('../php/updateUserInfo.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showMessage(data.error, 'error');
                } else {
                    showMessage(data.message, 'success');
                    passwordModal.style.display = 'none';
                    passwordForm.reset();
                }
            })
            .catch(error => {
                console.error('Error updating password:', error);
                showMessage('An error occurred while updating the password. Please try again later.', 'error');
            });
    });

    // Close modal functionality
    const closeButtons = document.querySelectorAll('.close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Cart functionality
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    const cartItems = document.querySelector('.cart-items');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    let cart = [];

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const menuItem = this.closest('.menu-item');
            const itemId = menuItem.dataset.id;
            const itemName = menuItem.querySelector('h3').textContent;
            const itemPrice = parseFloat(menuItem.querySelector('.price').textContent.replace('$', ''));
            const quantity = parseInt(menuItem.querySelector('.item-quantity').value);

            addToCart(itemId, itemName, itemPrice, quantity);
            updateCartUI();
            showMessage('Item added to cart', 'success');
        });
    });

    function addToCart(id, name, price, quantity) {
        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({ id, name, price, quantity });
        }
    }

    function updateCartUI() {
        cartItems.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const cartItemElement = document.createElement('div');
            cartItemElement.classList.add('cart-item');
            cartItemElement.innerHTML = `
                <span>${item.name}</span>
                <div class="cart-item-controls">
                    <button class="btn-decrease">-</button>
                    <span class="cart-item-quantity">${item.quantity}</span>
                    <button class="btn-increase">+</button>
                    <span class="cart-item-price">$${itemTotal.toFixed(2)}</span>
                    <button class="btn-remove">Remove</button>
                </div>
            `;

            cartItemElement.querySelector('.btn-decrease').addEventListener('click', () => updateCartItemQuantity(item.id, -1));
            cartItemElement.querySelector('.btn-increase').addEventListener('click', () => updateCartItemQuantity(item.id, 1));
            cartItemElement.querySelector('.btn-remove').addEventListener('click', () => removeCartItem(item.id));

            cartItems.appendChild(cartItemElement);
        });

        cartTotalAmount.textContent = `$${total.toFixed(2)}`;
    }

    function updateCartItemQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeCartItem(id);
            } else {
                updateCartUI();
            }
        }
    }

    function removeCartItem(id) {
        cart = cart.filter(item => item.id !== id);
        updateCartUI();
        showMessage('Item removed from cart', 'error');
    }

    function showMessage(message, type) {
        const messageContainer = document.getElementById('message-container');
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type);
        messageElement.textContent = message;
        messageContainer.appendChild(messageElement);

        // Force a reflow to trigger the transition
        messageElement.offsetHeight;

        // Add the 'show' class to trigger the transition
        messageElement.classList.add('show');

        setTimeout(() => {
            messageElement.classList.remove('show');
            setTimeout(() => {
                messageElement.remove();
            }, 300); // Wait for the transition to complete before removing
        }, 1000);
    }

    // Profile image change functionality
    const changeProfileImageBtn = document.querySelector('.change-profile-image');
    const profileImage = document.querySelector('.profile-image');
    const profileImageInput = document.createElement('input');
    profileImageInput.type = 'file';
    profileImageInput.accept = 'image/*';
    profileImageInput.style.display = 'none';
    document.body.appendChild(profileImageInput);

    changeProfileImageBtn.addEventListener('click', function () {
        profileImageInput.click();
    });

    profileImageInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            // Immediately show a preview of the selected image
            const reader = new FileReader();
            reader.onload = function (e) {
                profileImage.src = e.target.result;
                profileImage.style.opacity = '0.5'; // Indicate that it's not yet uploaded
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('field', 'profile_image');
            formData.append('profile_image', file);

            showMessage('Uploading image...', 'info');

            fetch('../php/updateUserInfo.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showMessage(data.error, 'error');
                        // Revert to the original image if there's an error
                        profileImage.src = profileImage.dataset.originalSrc;
                    } else {
                        // Update the image src with the new server path
                        const newImagePath = '../' + data.profile_image;
                        profileImage.src = newImagePath + '?t=' + new Date().getTime(); // Add timestamp to force reload
                        profileImage.dataset.originalSrc = newImagePath;
                        showMessage('Profile image updated successfully', 'success');
                    }
                    profileImage.style.opacity = '1'; // Restore full opacity
                })
                .catch(error => {
                    console.error('Error updating profile image:', error);
                    showMessage('An error occurred while updating the profile image. Please try again later.', 'error');
                    // Revert to the original image on error
                    profileImage.src = profileImage.dataset.originalSrc;
                    profileImage.style.opacity = '1';
                });
        }
    });

    // Add this to your initialization code (e.g., in the DOMContentLoaded event listener)
    profileImage.dataset.originalSrc = profileImage.src;


    // Place order functionality
    const placeOrderButton = document.getElementById('place-order');
    placeOrderButton.addEventListener('click', function () {
        if (cart.length === 0) {
            showMessage('Your cart is empty. Add some items before placing an order.', 'error');
            return;
        }

        const orderData = {
            action: 'create_order',
            items: cart
        };

        fetch('../php/order_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(`Order placed successfully! Order ID: ${data.order_id}`, 'success');
                    cart = [];
                    updateCartUI();
                    updateOrderHistory();
                } else {
                    showMessage(`Failed to place order: ${data.error}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error placing order:', error);
                showMessage('An error occurred while placing the order. Please try again later.', 'error');
            });
    });

    function updateOrderHistory() {
        const orderHistoryContainer = document.querySelector('.order-history-container');

        fetch('../php/order_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'get_user_order_history' })
        })
            .then(response => response.json())
            .then(orders => {
                orderHistoryContainer.innerHTML = '';
                orders.forEach(order => {
                    const orderElement = document.createElement('div');
                    orderElement.classList.add('order-item');
                    orderElement.innerHTML = `
                        <p>Order ID: ${order.order_id}</p>
                        <p>Date: ${new Date(order.order_date).toLocaleString()}</p>
                        <p>Status: <span class="order-status ${order.status}">${order.status}</span></p>
                        <p>Total Amount: $${parseFloat(order.total_amount).toFixed(2)}</p>
                        <button class="btn-order-details" data-order-id="${order.order_id}">View Details</button>
                    `;
                    orderHistoryContainer.appendChild(orderElement);
                });

                attachOrderDetailsListeners();
            })
            .catch(error => {
                console.error('Error fetching order history:', error);
                showMessage('An error occurred while fetching the order history. Please try again later.', 'error');
            });
    }

    function attachOrderDetailsListeners() {
        const orderDetailsButtons = document.querySelectorAll('.btn-order-details');
        orderDetailsButtons.forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId;
                fetchOrderDetails(orderId);
            });
        });
    }

    function fetchOrderDetails(orderId) {
        fetch('../php/order_management.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'get_order_details', order_id: orderId })
        })
            .then(response => response.json())
            .then(details => {
                displayOrderDetails(details);
            })
            .catch(error => {
                console.error('Error fetching order details:', error);
                showMessage('An error occurred while fetching the order details. Please try again later.', 'error');
            });
    }

    function displayOrderDetails(details) {
        const modal = document.getElementById('order-details-modal');
        const modalContent = document.getElementById('order-details-content');

        let itemsHtml = '';
        details.items.forEach(item => {
            itemsHtml += `
            <tr>
                <td>${item.item_name}</td>
                <td>${item.quantity}</td>
                <td>$${parseFloat(item.price).toFixed(2)}</td>
                <td>$${(item.quantity * item.price).toFixed(2)}</td>
            </tr>
        `;
        });

        modalContent.innerHTML = `
            <p>Order ID: ${details.order_id}</p>
            <p>Date: ${new Date(details.order_date).toLocaleString()}</p>
            <p>Status: <span class="order-status ${details.status.toLowerCase()}">${details.status}</span></p>
            <p>Contact Info: ${details.contact_info}</p>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHtml}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="total_price">Total Price</td>
                        <td class="total_price">$${parseFloat(details.total_amount).toFixed(2)}</td>
                    </tr>
                </tfoot>
            </table>
        `;

        modal.style.display = 'block';

        const closeButton = modal.querySelector('.close');
        closeButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Call updateOrderHistory when the page loads
    updateOrderHistory();
});