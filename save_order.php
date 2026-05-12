<?php
session_start();
include 'db.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($_SESSION['user_id']) || empty($data['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or Empty Cart']);
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$total_amount = array_reduce($data['cart'], fn($sum, $i) => $sum + ($i['price'] * $i['qty']), 0);

// 1. Prepare Order Insert
$stmt = $conn->prepare("INSERT INTO orders (user_id, username, total_amount, status) VALUES (?, ?, ?, 'Pending')");
$stmt->bind_param("isd", $user_id, $username, $total_amount);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $success = true;

    // 2. Prepare Item Insert
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, food_item, price, quantity) VALUES (?, ?, ?, ?)");
    
    foreach ($data['cart'] as $item) {
        $stmt_items->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['qty']);
        if (!$stmt_items->execute()) {
            $success = false;
        }
    }

    echo json_encode(['status' => $success ? 'success' : 'error']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>