<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_stock";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$product_name = $_POST['product_name'] ?? '';
$item_number = $_POST['item_number'] ?? '';
$manufacturer = $_POST['manufacturer'] ?? '';
$category = $_POST['category'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$expiry = $_POST['expiry'] ?? '';
$status = $_POST['status'] ?? '';


$stmt = $conn->prepare("INSERT INTO inventory (`Product Name`, `Manufacturer`, `Category`, `Quantity`, `Expiry`, `Status`) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


$stmt->bind_param("sssiss", $product_name, $manufacturer, $category, $quantity, $expiry, $status);


if ($stmt->execute()) {
    $statusMessage = "New record inserted successfully.";
} else {
    $statusMessage = "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory List - AUXILIARY</title>
    <link href="./css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
<body>

<div class="sidebar">
        <div class="logo">AUXILIARY</div>
        <a href="#" class="sidebar-icon"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
        <a href="#" class="sidebar-icon"><i class="fas fa-basket-shopping"></i><span>Inventory</span></a>
        <a href="#" class="sidebar-icon"><i class="fas fa-truck"></i><span>Suppliers</span></a>
        <a href="#" class="sidebar-icon"><i class="fas fa-user-circle"></i><span>User Profile</span></a>
    </div>

    <div class="header-bar">
        <div class="header-left">
            <h1 style="margin: 0; font-size: 20px;">Inventory List</h1>
        </div>
        <div class="header-right">
            <i class="fas fa-bell icon"></i>
            <i class="fas fa-user-circle icon"></i>
            <button class="logout-btn">Logout</button>
        </div>
    </div>
    <div class="center-container">
                <div>
                <?php if (!empty($statusMessage)): ?>
                    <p class="status-msg-box"><?= $statusMessage ?></p>
                    <a class="action" href='inventory.php'><span>Back to Inventory</span></a>
                <?php endif; ?> 
                </div>
            </div>
    </div>
    </body>
</html>