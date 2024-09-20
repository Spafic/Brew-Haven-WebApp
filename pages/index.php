<?php
require_once '../php/helpers/sessionConfig.php';
require_once '../database/db_connection.php'; // Assuming you have a config file with database connection details
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Brew Haven - Indulge in our artisanal coffee blends crafted to awaken your senses. Discover our menu, story, and sustainable coffee journey.">
    <meta name="keywords" content="coffee, artisanal coffee, sustainable coffee, coffee shop, Brew Haven">
    <title>Brew Haven - Artisanal Coffee Experience</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <script src="assets/js/main.js" defer></script>
    <script src="assets/js/auth.js" defer></script>
</head>

<body>
    <header>
        <nav>
            <div class="logo">Brew Haven</div>
            <ul>
                <li><a href="#hero">Home</a></li> <!-- Correct ID: #hero -->
                <li><a href="#about">About</a></li> <!-- Correct ID: #about -->
                <li><a href="#featured-products">Menu</a></li> <!-- Correct ID: #featured-products -->
                <li><a href="#seasonal-specials">Specials</a></li> <!-- Correct ID: #seasonal-specials -->
                <li><a href="#gallery">Gallery</a></li> <!-- Correct ID: #gallery -->
                <li><a href="#our-story">Our Story</a></li> <!-- Correct ID: #our-story -->
                <li><a href="#testimonials">Testimonials</a></li> <!-- Correct ID: #testimonials -->
                <li><a href="#coffee-process">Coffee Process</a></li> <!-- Correct ID: #coffee-process -->
            </ul>

            <div class="auth-buttons">
                <button id="loginBtn" class="auth-btn">Login</button>
                <button id="registerBtn" class="auth-btn">Register</button>
            </div>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <main>
        <section id="hero">
            <div class="hero-content">
                <h1 class="fade-in-up">Discover the Art of Coffee at <span>Brew Haven</span></h1>
                <p class="fade-in-up">Indulge in our artisanal blends, expertly crafted to awaken your senses</p>
                <div class="hero-cta fade-in-up">
                    <a href="#menu" class="cta-button">Explore Our Menu</a>
                    <a href="#about" class="cta-button cta-secondary">Our Story</a>
                </div>
            </div>
            <div class="hero-image parallax"></div>
        </section>

        <section id="about">
            <div class="section-content">
                <h2 class="fade-in">Our Story</h2>
                <div class="about-grid">
                    <div class="about-image fade-in"></div>
                    <div class="about-text">
                        <p class="fade-in">Brew Haven was born from a passion for exceptional coffee and a desire to
                            create a warm, welcoming space for our community.</p>
                        <p class="fade-in">Our journey began with a simple idea: to serve the perfect cup of coffee in
                            an atmosphere that feels like home.</p>
                        <a href="#" class="learn-more fade-in">Discover More</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="featured-products">
            <div class="section-content">
                <h2 class="fade-in">Signature Brews</h2>
                <div class="product-grid">
                    <div class="product-card fade-in">
                        <div class="product-image-container">
                            <img src="../assets/imgs/espresso.jpeg" alt="Artisanal Espresso" class="product-image">
                        </div>
                        <h3>Artisanal Espresso</h3>
                        <p>Bold & Rich</p>
                    </div>
                    <div class="product-card fade-in">
                        <div class="product-image-container">
                            <img src="../assets/imgs/latte.jpeg" alt="Velvet Latte" class="product-image">
                        </div>
                        <h3>Velvet Latte</h3>
                        <p>Smooth & Creamy</p>
                    </div>
                    <div class="product-card fade-in">
                        <div class="product-image-container">
                            <img src="../assets/imgs/cappuccino.jpg" alt="Cloud Cappuccino" class="product-image">
                        </div>
                        <h3>Cloud Cappuccino</h3>
                        <p>Light & Frothy</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="seasonal-specials">
            <div class="section-content">
                <h2 class="fade-in-up">Seasonal Specials</h2>
                <div class="specials-grid">
                    <div class="special-item fade-in-up">
                        <img src="../assets/imgs/PumpkinSpiceLatte.jpg" alt="Pumpkin Spice Latte">
                        <h3>Pumpkin Spice Latte</h3>
                        <p>Fall favorite with a Brew Haven twist</p>
                    </div>
                    <div class="special-item fade-in-up">
                        <img src="../assets/imgs/IcedCaramelMacchiato.jpg" alt="Iced Caramel Macchiato">
                        <h3>Iced Caramel Macchiato</h3>
                        <p>Perfect summer refreshment</p>
                    </div>
                    <div class="special-item fade-in-up">
                        <img src="../assets/imgs/MintMocha.jpg" alt="Mint Mocha">
                        <h3>Mint Mocha</h3>
                        <p>Cool and indulgent winter warmer</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="gallery">
            <div class="section-content">
                <h2 class="fade-in">Coffee Moments</h2>
                <div class="gallery-grid">
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/gallary8.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/matcha.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/gallary3.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/caramelfrappe.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/gallary2.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/gallary6.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/BlueBerry.jpg');">
                    </div>
                    <div class="gallery-item fade-in" style="background-image: url('../assets/imgs/gallary7.jpg');">
                    </div>
                </div>
            </div>
        </section>

        <section id="our-story">
            <div class="section-content">
                <h2 class="fade-in">Our Coffee Journey</h2>
                <div class="story-timeline">
                    <div class="timeline-item fade-in">
                        <div class="timeline-content">
                            <div class="year">2010</div>
                            <h3>The Beginning</h3>
                            <p>Brew Haven started as a small coffee cart in the local farmers market, serving
                                hand-brewed coffee to early morning shoppers.</p>
                        </div>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-content">
                            <div class="year">2013</div>
                            <h3>First Café Opens</h3>
                            <p>We opened our first brick-and-mortar café, bringing our passion for coffee to a cozy
                                corner in downtown.</p>
                        </div>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-content">
                            <div class="year">2016</div>
                            <h3>Roasting Our Own</h3>
                            <p>We began roasting our own beans, allowing us to control quality and develop unique blends
                                for our customers.</p>
                        </div>
                    </div>
                    <div class="timeline-item fade-in">
                        <div class="timeline-content">
                            <div class="year">2020</div>
                            <h3>Expanding Horizons</h3>
                            <p>Despite challenges, we opened two new locations and launched our online store, bringing
                                Brew Haven to coffee lovers everywhere.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials">
            <div class="section-content">
                <h2 class="fade-in">What Our Customers Say</h2>
                <div class="testimonial-container">
                    <div class="testimonial-slider">
                        <div class="testimonial-card fade-in">
                            <p>Brew Haven is my daily sanctuary. The attention to detail in every cup and the warm
                                ambiance make it my favorite spot in the city. It's not just coffee; it's an experience.
                            </p>
                            <div class="customer-info">
                                <img src="../assets/imgs/sarah.jpg" alt="Sarah M." class="customer-image">
                                <div class="customer-details">
                                    <div class="customer-name">Sarah M.</div>
                                    <div class="customer-title">Creative Director</div>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-card fade-in">
                            <p>I've never tasted coffee this good! The baristas are true artists, and their passion
                                shows in every cup. Brew Haven has completely changed my coffee experience.</p>
                            <div class="customer-info">
                                <img src="../assets/imgs/john.jpg" alt="John D." class="customer-image">
                                <div class="customer-details">
                                    <div class="customer-name">John D.</div>
                                    <div class="customer-title">Software Engineer</div>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-card fade-in">
                            <p>The commitment to sustainability at Brew Haven is admirable. It's refreshing to enjoy
                                great coffee while knowing that it's responsibly sourced and served. Keep up the
                                fantastic work!</p>
                            <div class="customer-info">
                                <img src="../assets/imgs/emma.jpg" alt="Emma L." class="customer-image">
                                <div class="customer-details">
                                    <div class="customer-name">Emma L.</div>
                                    <div class="customer-title">Environmental Scientist</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="slider-controls">
                        <button class="slider-control active" data-index="0"></button>
                        <button class="slider-control" data-index="1"></button>
                        <button class="slider-control" data-index="2"></button>
                    </div>
                </div>
            </div>
        </section>

        <section id="coffee-process">
            <div class="section-content">
                <h2 class="fade-in">From Bean to Cup</h2>
                <div class="process-grid">
                    <div class="process-item fade-in">
                        <img src="../assets/imgs/EthicalSourcing.jpg" alt="Coffee Sourcing">
                        <h3>Ethical Sourcing</h3>
                        <p>We partner directly with farmers to ensure fair prices and sustainable practices.</p>
                    </div>
                    <div class="process-item fade-in">
                        <img src="../assets/imgs/ArtisanalRoasting.jpg" alt="Coffee Roasting">
                        <h3>Artisanal Roasting</h3>
                        <p>Our master roasters bring out the unique flavors of each bean origin.</p>
                    </div>
                    <div class="process-item fade-in">
                        <img src="../assets/imgs/PreciseBrewing.jpg" alt="Coffee Brewing">
                        <h3>Precise Brewing</h3>
                        <p>We use state-of-the-art equipment and techniques to brew the perfect cup every time.</p>
                    </div>
                </div>
            </div>
        </section>





        <!-- Login Modal  -->
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Login</h2>
                <form id="loginForm" action="../php/login.php" method="post">
                    <div class="form-group">
                        <label for="loginEmail">Username or Email</label>
                        <input type="text" id="loginEmail" name="email" required>
                        <span class="error" id="loginEmailError"></span>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" name="password" required>
                        <span class="error" id="loginPasswordError"></span>
                    </div>
                    <button type="submit" class="btn-primary">Login</button>
                    <div class="general-error" id="loginGeneralError"></div>
                </form>
            </div>
        </div>


        <!-- Registration Modal -->
        <div id="registerModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Register</h2>
                <form id="registerForm" action="../php/register.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="registerName">Full Name</label>
                        <input type="text" id="registerName" name="name" required>
                        <span class="error" id="registerNameError"></span>
                    </div>
                    <div class="form-group">
                        <label for="registerEmail">Email</label>
                        <input type="email" id="registerEmail" name="email" required>
                        <span class="error" id="registerEmailError"></span>
                    </div>
                    <div class="form-group">
                        <label for="registerPhone">Phone Number</label>
                        <input type="tel" id="registerPhone" name="phone" required pattern="[0-9]{11}"
                            title="Phone number must be 11 digits" oninput="validatePhoneInput(this)">
                        <span class="error" id="registerPhoneError"></span>
                        <script>
                            function validatePhoneInput(input) {
                                input.value = input.value.replace(/\D/g, '');
                            }
                        </script>
                    </div>
                    <div class="form-group">
                        <label for="registerLocation">Location</label>
                        <input type="text" id="registerLocation" name="location" required>
                        <span class="error" id="registerLocationError"></span>
                    </div>
                    <div class="form-group">
                        <label for="registerPassword">Password</label>
                        <input type="password" id="registerPassword" name="password" required>
                        <span class="error" id="registerPasswordError"></span>
                    </div>
                    <div class="form-group">
                        <label for="registerConfirmPassword">Confirm Password</label>
                        <input type="password" id="registerConfirmPassword" name="confirmPassword" required>
                        <span class="error" id="registerConfirmPasswordError"></span>
                    </div>
                    <div class="form-group">
                        <label for="registerProfileImage">Profile Image (optional)</label>
                        <input type="file" id="registerProfileImage" name="profile_image" accept="image/*">
                        <span class="error" id="registerProfileImageError"></span>
                    </div>
                    <button type="submit" name="submit" class="btn-primary">Register</button>
                    <div class="general-error" id="registerGeneralError"></div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section fade-in">
                <h3>Visit Us</h3>
                <p>123 Artisan Avenue<br>Brewville, BV 12345</p>
            </div>
            <div class="footer-section fade-in">
                <h3>Hours</h3>
                <p>Mon-Fri: 7am - 8pm<br>Sat-Sun: 8am - 9pm</p>
            </div>
            <div class="footer-section fade-in">
                <h3>Contact</h3>
                <p>Phone: (555) 123-4567<br>Email: hello@brewhaven.com</p>
            </div>
            <div class="footer-section fade-in">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Brew Haven. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>