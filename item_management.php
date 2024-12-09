<?php
session_start();
$conn = new mysqli("localhost", "root", "", "user_management");

// Check if the user is an editor
if ($_SESSION['role'] !== 'editor') {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];
        $stmt = $conn->prepare("INSERT INTO items (name, description, stock) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $description, $stock);
        $stmt->execute();
    } elseif (isset($_POST['edit_item'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];
        $stmt = $conn->prepare("UPDATE items SET name = ?, description = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $description, $stock, $id);
        $stmt->execute();
    } elseif (isset($_POST['delete_item'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Fetch items
$result = $conn->query("SELECT * FROM items");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Item Management</title>
</head>
<body>
    <h1>Item Management</h1>
    <form method="POST">
        <h2>Add Item</h2>
        <input type="text" name="name" placeholder="Item Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="stock" placeholder="Stock" required>
        <button type="submit" name="add_item">Add</button>
    </form>
    <h2>Item List</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="text" name="name" value="<?= $row['name'] ?>" required>
            <textarea name="description"><?= $row['description'] ?></textarea>
            <input type="number" name="stock" value="<?= $row['stock'] ?>" required>
            <button type="submit" name="edit_item">Edit</button>
            <button type="submit" name="delete_item">Delete</button>
        </form>
    <?php endwhile; ?>
</body>
</html>
