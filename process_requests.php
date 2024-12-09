<?php
session_start();
$conn = new mysqli("localhost", "root", "", "user_management");

// Check if the user is an editor
if ($_SESSION['role'] !== 'editor') {
    header("Location: login.php");
    exit();
}

// Handle request processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    // Decrement stock if approved
    if ($status === 'approved') {
        $stmt = $conn->prepare("SELECT item_id, quantity FROM requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $item_id = $result['item_id'];
        $quantity = $result['quantity'];

        $stmt = $conn->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $item_id);
        $stmt->execute();
    }
}

// Fetch pending requests
$result = $conn->query("SELECT r.id, u.username, i.name AS item_name, r.quantity, r.status 
                        FROM requests r 
                        JOIN users u ON r.user_id = u.id 
                        JOIN items i ON r.item_id = i.id 
                        WHERE r.status = 'pending'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Process Requests</title>
</head>
<body>
    <h1>Process Requests</h1>
    <?php while ($row = $result->fetch_assoc()): ?>
        <form method="POST">
            <p>
                <strong>User:</strong> <?= $row['username'] ?><br>
                <strong>Item:</strong> <?= $row['item_name'] ?><br>
                <strong>Quantity:</strong> <?= $row['quantity'] ?><br>
            </p>
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button type="submit" name="status" value="approved">Approve</button>
            <button type="submit" name="status" value="denied">Deny</button>
        </form>
    <?php endwhile; ?>
</body>
</html>
