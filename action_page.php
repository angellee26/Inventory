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
    echo "New record inserted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>