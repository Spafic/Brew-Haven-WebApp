<?php
require_once __DIR__ . '/../../database/db_connection.php';

function fetchItems($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT item_id, name, price, category, description, product_image, available FROM items");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchItems: " . $e->getMessage());
        return [];
    }
}

function getItem($pdo, $itemId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM items WHERE item_id = :id");
        $stmt->execute([':id' => $itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getItem: " . $e->getMessage());
        return false;
    }
}

function addItem($pdo, $name, $price, $category, $description, $available, $imagePath)
{
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO items (name, price, category, description, available, product_image) 
             VALUES (:name, :price, :category, :description, :available, :product_image)"
        );
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':category' => $category,
            ':description' => $description,
            ':available' => $available,
            ':product_image' => $imagePath
        ]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database error in addItem: " . $e->getMessage());
        return false;
    }
}

function updateItem($pdo, $itemId, $name, $price, $category, $description, $available, $imagePath)
{
    try {
        $sql = "UPDATE items 
                SET name = :name, price = :price, category = :category, description = :description, available = :available";

        $params = [
            ':item_id' => $itemId,
            ':name' => $name,
            ':price' => $price,
            ':category' => $category,
            ':description' => $description,
            ':available' => $available
        ];

        if ($imagePath) {
            $sql .= ", product_image = :product_image";
            $params[':product_image'] = $imagePath;
        }

        $sql .= " WHERE item_id = :item_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Database error in updateItem: " . $e->getMessage());
        return false;
    }
}

function removeItem($pdo, $itemId)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM items WHERE item_id = :item_id");
        $stmt->execute([':item_id' => $itemId]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Database error in removeItem: " . $e->getMessage());
        return false;
    }
}

function handleImageUpload($itemName)
{
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // Set the correct path for the upload directory
        $uploadDir = __DIR__ . '/../../assets/imgs/items/';
        $fileExtension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);

        // Set the default timezone to Egypt
        date_default_timezone_set('Africa/Cairo');

        // Generate the timestamp in the desired format
        $timestamp = date('y_m_d_H_i_s');

        // Use the formatted timestamp in the file name
        $fileName = $itemName . '_' . $timestamp . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        // Ensure the upload directory exists
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Failed to create upload directory: " . $uploadDir);
                return null;
            }
        }

        // Move the uploaded file
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadPath)) {
            // Return the relative path to be stored in the database
            return 'assets/imgs/items/' . $fileName;
        } else {
            error_log("Failed to move uploaded file. Error: " . $_FILES['product_image']['error']);
            error_log("Attempted to move file to: " . $uploadPath);
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_items':
            echo json_encode(fetchItems($pdo));
            break;
        case 'get_item':
            echo json_encode(getItem($pdo, $_GET['id']));
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_item':
            $imagePath = handleImageUpload($_POST['name']);
            if ($imagePath === null) {
                echo json_encode(['success' => false, 'error' => 'Failed to upload image']);
                break;
            }
            $result = addItem($pdo, $_POST['name'], $_POST['price'], $_POST['category'], $_POST['description'], $_POST['available'], $imagePath);
            echo json_encode(['success' => $result !== false, 'id' => $result, 'imagePath' => $imagePath]);
            break;
        case 'update_item':
            $imagePath = handleImageUpload($_POST['name']);
            if ($imagePath === null) {
                // If no new image was uploaded, keep the existing image path
                $imagePath = getItem($pdo, $_POST['item_id'])['product_image'];
            }
            $result = updateItem($pdo, $_POST['item_id'], $_POST['name'], $_POST['price'], $_POST['category'], $_POST['description'], $_POST['available'], $imagePath);
            echo json_encode(['success' => $result !== false, 'imagePath' => $imagePath]);
            break;
        case 'remove_item':
            $result = removeItem($pdo, $_POST['item_id']);
            echo json_encode(['success' => $result !== false]);
            break;
    }
}
