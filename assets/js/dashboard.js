document.addEventListener('DOMContentLoaded', function() {
    // Sidebar functionality
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const mainContent = document.querySelector('.main-content');

    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('sidebar-closed');
    });

    // Navigation functionality
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const sections = document.querySelectorAll('.dashboard-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
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
    
    changeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const field = this.dataset.field;
            const currentValue = this.previousElementSibling.textContent;
            updateField.textContent = field;
            updateValue.value = currentValue;
            userInfoModal.style.display = 'block';
        });
    });
    
    userInfoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const field = updateField.textContent;
        const newValue = updateValue.value;
    
        // Here you would typically send an AJAX request to update the value in the database
        // For this example, we'll just update the UI
        document.querySelector(`[data-field="${field}"]`).previousElementSibling.textContent = newValue;
        
        // Close the modal
        userInfoModal.style.display = 'none';
        showMessage('User information updated successfully', 'success');
    });

    // Password change functionality
    const changePasswordBtn = document.getElementById('change-password');
    const passwordModal = document.getElementById('password-modal');
    const passwordForm = document.getElementById('password-change-form');

    changePasswordBtn.addEventListener('click', function() {
        passwordModal.style.display = 'block';
    });

    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Here you would typically send an AJAX request to update the password in the database
        // For this example, we'll just close the modal and show a success message
        passwordModal.style.display = 'none';
        showMessage('Password changed successfully', 'success');
    });

    // Close modal functionality
    const closeButtons = document.querySelectorAll('.close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = 'none';
        });
    });

    // Cart functionality
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    const cartItems = document.querySelector('.cart-items');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    let cart = [];

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
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

    // Place order functionality
    const placeOrderButton = document.getElementById('place-order');
    placeOrderButton.addEventListener('click', function() {
        if (cart.length === 0) {
            showMessage('Your cart is empty. Add some items before placing an order.', 'error');
            return;
        }

        // Here you would typically send an AJAX request to process the order
        // For this example, we'll just clear the cart and show a message
        showMessage('Order placed successfully!', 'success');
        cart = [];
        updateCartUI();
    });

    // Order history functionality
    const orderDetailsButtons = document.querySelectorAll('.btn-order-details');
    orderDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            // Here you would typically fetch order details via AJAX
            // For this example, we'll just show a message
            showMessage(`Fetching details for Order ID: ${orderId}`, 'info');
        });
    });

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
    const profileImageInput = document.createElement('input');
    profileImageInput.type = 'file';
    profileImageInput.accept = 'image/*';
    profileImageInput.style.display = 'none';
    document.body.appendChild(profileImageInput);

    changeProfileImageBtn.addEventListener('click', function() {
        profileImageInput.click();
    });

    profileImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.profile-image').src = e.target.result;
                // Here you would typically upload the image to the server
                showMessage('Profile image updated successfully', 'success');
            };
            reader.readAsDataURL(file);
        }
    });
});