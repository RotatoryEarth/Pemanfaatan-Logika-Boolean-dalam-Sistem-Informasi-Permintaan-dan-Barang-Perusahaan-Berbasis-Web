<?php
session_start();
$conn = new mysqli("localhost", "root", "", "user_management");

// Check if the user is a viewer
if ($_SESSION['role'] !== 'viewer') {
    header("Location: login.php");
    exit();
}

// Handle item requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id']; // Fetch the user ID from the session

    if (!$user_id) {
        die("User ID not found in session. Please log in again.");
    }

    // Check if the requested quantity exceeds the stock
    $stmt = $conn->prepare("SELECT stock FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($quantity <= 0 || $quantity > $item['stock']) {
        die("Invalid quantity. Please request a valid amount.");
    }

    // Insert the request into the database
    $stmt = $conn->prepare("INSERT INTO requests (user_id, item_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $item_id, $quantity);
    $stmt->execute();

    echo "<script>alert('Request submitted successfully!');</script>";
}

// Fetch items
$result = $conn->query("SELECT * FROM items WHERE stock > 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('https://i.ibb.co.com/RN32j9T/Phases-Spaces-Inc-1.png'); /* Placeholder for the background image */
            background-size: cover; /* Ensures the image covers the entire background */
            background-position: center; /* Centers the image */
            background-attachment: fixed; /* Keeps the background fixed when scrolling */
            color: white;
        }
        .header {
            display: flex;
            align-items: center;
            background-color: rgba(224, 90, 0, 0.9); /* Added transparency for better blending */
            padding: 20px;
            color: white;
            font-size: 28px;
        }
        .header img {
            height: 50px;
            margin-right: 15px;
        }
        .header .title {
            flex-grow: 1;
            font-weight: bold;
        }
        .header .home-icon {
            background-color: white;
            padding: 10px;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
        }
        .home-icon img {
            height: 20px;
            width: 20px;
        }
        .container {
            padding: 30px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
        }
        .card {
            background-color: #E05A00;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            padding: 20px;
            color: white;
            text-align: left;
            position: relative;
        }
        .card h3 {
            margin: 0;
            font-size: 24px; /* Increased font size for item name */
            font-weight: bold;
        }
        .card .description {
            background-color: white; /* Description box */
            color: black;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: left;
            height: 60px;
            overflow-y: auto;
        }
        .card .stock-container {
            background-color: #D3D3D3; /* Grey box container for stock */
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            text-align: left;
        }
        .card .stock-container .stock {
            color: black; /* Stock text color set to black */
            font-size: 16px;
            font-weight: bold;
        }
        .card button {
            background-color: black;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 15px;
            cursor: pointer;
            font-size: 16px;
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
        }
        .scroll-container {
            max-height: calc(100vh - 140px);
            overflow-y: auto;
            padding-right: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Logo">
        <div class="title">Available Items</div>
        <a href="index.php" class="home-icon">
            <img src="home-icon.png" alt="Home">
        </a>
    </div>
    <div class="container">
        <div class="scroll-container">
            <div class="grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <div class="description"><?= htmlspecialchars($row['description']) ?></div>
                    <div class="stock-container">
                        <span class="stock">Stock: <?= $row['stock'] ?></span>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="item_id" value="<?= $row['id'] ?>">
                        <input type="number" name="quantity" placeholder="Quantity" min="1" max="<?= $row['stock'] ?>" required>
                        <button type="submit">Request</button>
                    </form>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>
