<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['cart']) && !empty($input['cart'])) {
    $user_id = $_SESSION['user_id'];
    $total = 0;
    
    foreach ($input['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }

    // I-save ang main order
    $query = "INSERT INTO orders (user_id, total_amount, status) VALUES ('$user_id', '$total', 'Pending')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'DB Error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Empty cart']);
}
?>