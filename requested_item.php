<?php
// Start session and include database connection
session_start();
require_once "db_connection.php";

// Ensure the user is logged in and has a viewer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'viewer') {
    header("Location: login.php");
    exit();
}

// Get the user's ID from the session
$user_id = $_SESSION['user_id'];

// Fetch the user's item requests from the database
$sql = "SELECT items.name AS item_name, requests.quantity, requests.status 
        FROM requests 
        JOIN items ON requests.item_id = items.id 
        WHERE requests.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requested Items</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your stylesheet link -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        h1 {
            text-align: center;
            color: #333;
            padding: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status-processed {
            color: orange;
        }
        .status-approved {
            color: green;
        }
        .status-denied {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Requested Items</h1>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['item_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td>
                                <?php
                                if ($row['status'] === 'processed') {
                                    echo '<span class="status-processed">Processed</span>';
                                } elseif ($row['status'] === 'approved') {
                                    echo '<span class="status-approved">Your request has been approved</span>';
                                } elseif ($row['status'] === 'denied') {
                                    echo '<span class="status-denied">Your request has been denied</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No requested items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the prepared statement and connection
$stmt->close();
$conn->close();
?>
