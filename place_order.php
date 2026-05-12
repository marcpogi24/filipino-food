<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Login again.']);
    exit();
}

if (isset($input['cart']) && !empty($input['cart'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? 'Customer'; 
    $cart = $input['cart'];
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }

    mysqli_begin_transaction($conn);

    try {
        // Insert sa Main Orders Table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, username, total_amount, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("isd", $user_id, $username, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Insert bawat item sa Order Items Table
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, food_item, price, quantity) VALUES (?, ?, ?, ?)");
        foreach ($cart as $item) {
            $item_stmt->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['qty']);
            $item_stmt->execute();
        }

        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Empty cart.']);
}
?>