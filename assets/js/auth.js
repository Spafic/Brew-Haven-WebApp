
// Auth modals

document.addEventListener('DOMContentLoaded', () => {
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const closeBtns = document.querySelectorAll('.close');
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');
    const loginEmailInput = document.getElementById('loginEmail');
    const loginPasswordInput = document.getElementById('loginPassword');
    const loginEmailError = document.getElementById('loginEmailError');
    const loginPasswordError = document.getElementById('loginPasswordError');
    const loginGeneralError = document.getElementById('loginGeneralError');

    function openModal(modal) {
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeModal(modal) {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(loginModal);
            closeModal(registerModal);
        });
    });

    loginBtn.addEventListener('click', () => openModal(loginModal));
    registerBtn.addEventListener('click', () => openModal(registerModal));


    window.addEventListener('click', (e) => {
        if (e.target === loginModal) closeModal(loginModal);
        if (e.target === registerModal) closeModal(registerModal);
    });

    // Regex patterns
    const nameRegex = /^[a-zA-Z\s]{2,50}$/;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail|yahoo|outlook)\.com$/;
    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?!.*[<>'"/;`%])(?!.*\s).{8,}$/;
    const phoneRegex = /^[0-9]{11}$/;
    const usernameOrEmailRegex = /^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9.-]+\.)?[a-zA-Z]{2,}|^[a-zA-Z0-9._%+-]{2,50}$/;
    const locationRegex = /^[a-zA-Z0-9\s,.-]{2,100}$/; // Simple regex to allow letters, numbers, spaces, commas, periods, and hyphens

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

    function showFieldError(field, error) {
        const errorSpan = document.getElementById(`${field.id}Error`);
        if (errorSpan) {
            errorSpan.textContent = error;
            errorSpan.style.display = 'block';
        }
        field.classList.add('error-input');
    }

    function clearFieldError(field) {
        const errorSpan = document.getElementById(`${field.id}Error`);
        if (errorSpan) {
            errorSpan.textContent = '';
            errorSpan.style.display = 'none';
        }
        field.classList.remove('error-input');
    }

    function showGeneralError(form, error) {
        let errorContainer = form.querySelector('.general-error');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'general-error';
            errorContainer.style.color = 'red';
            errorContainer.style.marginTop = '10px';
            form.querySelector('button[type="submit"]').insertAdjacentElement('afterend', errorContainer);
        }
        errorContainer.textContent = error;
    }

    function clearAllErrors(form) {
        form.querySelectorAll('.error').forEach(errorSpan => {
            errorSpan.textContent = '';
            errorSpan.style.display = 'none';
        });
        form.querySelectorAll('input').forEach(input => {
            input.classList.remove('error-input');
        });
        const generalError = form.querySelector('.general-error');
        if (generalError) {
            generalError.textContent = '';
        }
    }

    function validateInput(input, regex, errorMessage) {
        const sanitizedValue = sanitizeInput(input.value.trim());
        if (!regex.test(sanitizedValue)) {
            showFieldError(input, errorMessage);
            return false;
        }
        clearFieldError(input);
        return true;
    }

    function validateImage(input) {
        const file = input.files[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (!allowedTypes.includes(file.type)) {
                showFieldError(input, 'Please select a valid image file (JPEG, PNG, or GIF).');
                return false;
            }

            if (file.size > maxSize) {
                showFieldError(input, 'Image size should be less than 10MB.');
                return false;
            }

            clearFieldError(input);
            return true;
        }
        return true; // Image is optional, so return true if no file is selected
    }

    function togglePasswordVisibility() {
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const passwordField = toggle.previousElementSibling;
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;
                toggle.classList.toggle('show-password');
            });
        });
    }

    togglePasswordVisibility();

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearAllErrors(loginForm);

        const loginButton = loginForm.querySelector('button[type="submit"]');
        loginButton.disabled = true;
        loginButton.textContent = 'Logging in...';

        const formData = new FormData(loginForm);

        try {
            const response = await fetch('../php/login.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            console.log('Login response:', data); // For debugging

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, error]) => {
                        if (field === 'general') {
                            showGeneralError(loginForm, error);
                        } else {
                            showFieldError(document.getElementById(`login${field.charAt(0).toUpperCase() + field.slice(1)}`), error);
                        }
                    });
                } else {
                    showGeneralError(loginForm, 'An error occurred during login. Please try again.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showGeneralError(loginForm, 'An error occurred during login. Please try again.');
        } finally {
            loginButton.disabled = false;
            loginButton.textContent = 'Login';
        }
    });

    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        clearAllErrors(registerForm);

        const name = document.getElementById('registerName');
        const email = document.getElementById('registerEmail');
        const phone = document.getElementById('registerPhone');
        const location = document.getElementById('registerLocation');
        const password = document.getElementById('registerPassword');
        const confirmPassword = document.getElementById('registerConfirmPassword');
        const profileImage = document.getElementById('registerProfileImage');

        let isValid = true;

        isValid = validateInput(name, nameRegex, 'Please enter a valid name') && isValid;
        isValid = validateInput(email, emailRegex, 'Please enter a valid email') && isValid;
        isValid = validateInput(phone, phoneRegex, 'Please enter a valid phone number') && isValid;
        isValid = validateInput(location, locationRegex, 'Please enter a valid location') && isValid;
        isValid = validateInput(password, passwordRegex, 'Password must contain at least 8 characters, including UPPER/lowercase, number, and special character') && isValid;

        if (password.value !== confirmPassword.value) {
            showFieldError(confirmPassword, 'Passwords do not match');
            isValid = false;
        } else {
            clearFieldError(confirmPassword);
        }

        isValid = validateImage(profileImage) && isValid;

        if (!isValid) {
            showGeneralError(registerForm, 'Failed to submit, make sure your input is entered correctly');
            return;
        }

        const formData = new FormData(registerForm);

        fetch('../php/register.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../pages/dashboard.php';
                } else {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, error]) => {
                            if (field === 'general') {
                                showGeneralError(registerForm, error);
                            } else {
                                showFieldError(document.getElementById(`register${field.charAt(0).toUpperCase() + field.slice(1)}`), error);
                            }
                        });
                    } else {
                        showGeneralError(registerForm, 'Failed to submit due to wrong data');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showGeneralError(registerForm, 'An error occurred during registration.');
            });
    });
});
