# Brew Haven WebApp ☕️

Welcome to the **Brew Haven WebApp** repository! This project is designed to manage and showcase a variety of Coffee products. Below you will find the file structure and a brief description of each directory and its contents.

<p align="center">
    <img src="./assets/imgs/BrewHaven.jpeg" alt="Brew Haven Banner" height="300">
</p>

## ✨ Features

1. **🔒 Authentication and Security**: Secure user authentication with input sanitization to prevent SQL and JS injection.
2. **📋 User Dashboard**: Detailed user dashboard with order history and action reports.
3. **📊 Admin Dashboard**: Comprehensive admin dashboard with statistics and metrics.
4. **📈 Graphs and Charts**: Visual representation of profits and best sellers over time.
5. **✅ Input Validation**: Robust input validation to ensure data integrity and security.

## 🚀 Getting Started

To get started with the Brew Haven WebApp, clone the repository and follow the setup instructions provided in the [`database`](https://github.com/Spafic/Brew-Haven-WebApp/blob/main/database) directory to initialize your database.

```sh
git clone https://github.com/Spafic/Brew-Haven-WebApp.git
cd Brew-Haven-WebApp
```

### Running the Project

You can run the project using either **XAMPP** or **WAMP**:

#### Using XAMPP

1. Download and install [XAMPP](https://www.apachefriends.org/index.html).
2. Start Apache and MySQL from the XAMPP control panel.
3. Place the project files in the `htdocs` directory.
4. Open your browser and navigate to `http://localhost/Brew-Haven-WebApp`.

#### Using WAMP

1. Download and install [WAMP](http://www.wampserver.com/en/).
2. Start the WAMP server.
3. Place the project files in the `www` directory.
4. Open your browser and navigate to `http://localhost/Brew-Haven-WebApp`.

### Database Connection

1. Create a database using phpMyAdmin or any MySQL client.
2. Import the SQL scripts from the `database/` directory `setup_database.sql` file.

## 📁 File Structure

```
Brew-Haven-WebApp/
├── assets/                
│   ├── css/                # 🎨 Stylesheets for the application.
│   ├── imgs/               # 🖼️ Image assets for products and UI elements.
│   │   ├── errors/         # 🚨 Images for error pages (e.g., 404 or 500 error illustrations).
│   │   ├── items/          # 🛍️ Images representing the coffee products and items.
│   │   ├── users/          # 👤 User profile images and avatars.
│   │   └── Others/         # 📄 General images used across various pages (e.g., banners, backgrounds).             
│   └── js/                 # ✨ JavaScript files for interactivity and UI.
├── database/               # 🗄️ Directory containing database scripts and migrations.
│   ├── db_connection.php   # 🔌 PHP script to establish and manage the connection to the MySQL database.
│   └── setup_database.sql  # 📄 SQL script for creating and initializing the database schema and tables.
├── includes/               # 📦 Common UI includes.
├── pages/
│   ├── server/
│   │   ├── 500.php         # 🚨 Internal server error page.
│   ├── dashboard.php   # 📊 Admin dashboard page.
│   ├── index.php       # 🏠 Homepage of the application.
│   └── staffDashboard.php # 👥 Dashboard for staff users.
├── php/
│   ├── admin/
│   │   ├── chart_data_handler.php # 📈 Backend for chart data.
│   │   ├── items_handler.php      # 🛠️ CRUD operations for items.
│   │   ├── orders_handler.php     # 📦 Order management backend.
│   │   ├── stats_handler.php      # 📊 Fetch and process statistical data.
│   │   └── users_handler.php      # 👤 User management operations.
│   ├── helpers/
│   │   └── sessionConfig.php      # 🔒 Session configuration.
│   ├── login.php              # 🔑 User login and session handling.
│   ├── order_management.php   # 📦 Order handling operations.
│   ├── register.php           # 📝 User registration.
│   └── updateUserInfo.php     # 🛠️ Update user information.
├── README.md                          # 📄 Project documentation.
└── LICENSE                            # 📄 Project MIT LICENSE.
```

## 🛠️ Technologies Used

- **HTML5**: <img src="https://img.icons8.com/color/48/000000/html-5.png" alt="HTML5" width="24" height="24"/> Markup language for structuring the web content.
- **CSS3**: <img src="https://img.icons8.com/color/48/000000/css3.png" alt="CSS3" width="24" height="24"/> Stylesheet language for designing the web pages.
- **JavaScript**: <img src="https://img.icons8.com/color/48/000000/javascript.png" alt="JavaScript" width="24" height="24"/> Programming language for interactivity and dynamic content.
- **PHP**: <img src="https://img.icons8.com/officel/48/000000/php-logo.png" alt="PHP" width="24" height="24"/> Server-side scripting language for backend operations.
- **MySQL**: <img src="https://img.icons8.com/color/48/000000/mysql-logo.png" alt="MySQL" width="24" height="24"/> Relational database management system for data storage.

## 🤝 Contributing

We welcome contributions to improve the Brew Haven WebApp. Please fork the repository and submit pull requests for any enhancements or bug fixes.

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](./LICENSE) file for more details.

## 📞 Contact

For any questions or support, please open an issue in the repository or contact me at [Click Here](mailto:omar.mamon203@gmail.com).

