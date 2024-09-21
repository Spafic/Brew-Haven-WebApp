
# ☕️ Brew Haven WebApp Database

This directory contains the database-related files required for the Brew Haven WebApp.

## 📂 Structure

- `db_connection.php`: Responsible for database configurations and interfacing with the project.
- `setup_database.sql`: Contains the database schema and initial data setup.

## 🛠️ Setup Instructions

### 1. Requirements

- **MySQL**: Ensure MySQL is installed and running. If you're using XAMPP or WAMP, make sure the MySQL server is active.

### 2. Importing the Database

#### Using Command Line

1. Open the command prompt or terminal.
2. Run the following command to import the setup script:

    ```sh
    mysql -u root -p < database/setup_database.sql
    ```

> [!NOTE]
> - Replace `root` with your MySQL username if different.
> - You will be prompted to enter the password. If there is no password, press Enter to skip.
> - If you want to include the password directly in the command, use the format below:
> 
> ```sh
> mysql -u root -pYourPassword < database/setup_database.sql
> ```
> - Do not leave a space between `-p` and the password.

#### Using phpMyAdmin (XAMPP/WAMP)

1. Open your browser and navigate to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Log in using your MySQL credentials:
    - **Username**: `root` (default for XAMPP/WAMP).
    - **Password**: Enter your password if set, otherwise leave it blank.
3. Click on the **Import** tab.
4. Click **Choose File** and select the `setup_database.sql` file located in the `database/` directory.
5. Click **Go** to import the database.

### ℹ️ Additional Notes

- **Default Credentials for XAMPP/WAMP:**
  - **Username**: `root`
  - **Password**: (leave it blank if not set)
- Make sure you have the necessary permissions to execute SQL scripts.

> [!NOTE]
> The `db_connection.php` file is crucial for database configurations and interfacing with the project. Ensure it is correctly set up to match your database credentials and settings.

