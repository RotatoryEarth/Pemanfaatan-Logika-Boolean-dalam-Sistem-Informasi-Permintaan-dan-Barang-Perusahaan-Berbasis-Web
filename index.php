<?php
session_start();
$conn = new mysqli("localhost", "root", "", "user_management");

// Check if the user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); // Redirect to login if the user is not logged in
    exit();
}

// Get the user's role from the session
$user_role = $_SESSION['role'];

// Define dashboard content based on user role
if ($user_role === 'admin') {
    $dashboard_title = "Admin Dashboard";
    $dashboard_items = [
        ["Manage Users", "View and manage all users in the system.", "user_management.php"],
        ["View Reports", "Access detailed reports of item requests and stock levels.", "reports.php"]
    ];
} elseif ($user_role === 'editor') {
    $dashboard_title = "Editor Dashboard";
    $dashboard_items = [
        ["Manage Items", "Add, edit, or remove items from the inventory.", "item_management.php"],
        ["Process Requests", "Approve or deny item requests from users.", "process_requests.php"]
    ];
} elseif ($user_role === 'viewer') {
    $dashboard_title = "Viewer Dashboard";
    $dashboard_items = [
        ["Requested Items", "Check your requested item status.", "requested_item.php"],
        ["Request Items", "Submit requests for items from the inventory.", "item_request.php"]
    ];
} else {
    echo "Invalid role.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $dashboard_title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #333;
            color: #fff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #77aaff 3px solid;
        }
        header a {
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            list-style: none;
        }
        header li {
            float: left;
            display: inline;
            padding: 0 20px 0 20px;
        }
        header #branding {
            float: left;
        }
        header #branding h1 {
            margin: 0;
        }
        header nav {
            float: right;
            margin-top: 10px;
        }
        .dashboard {
            margin: 20px 0;
        }
        .dashboard h2 {
            color: #333;
        }
        .dashboard .card {
            background: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .dashboard .card h3 {
            margin: 0;
        }
        .dashboard .card p {
            margin: 10px 0 0 0;
        }
        .dashboard .card .button {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .dashboard .card .button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><i class="fas fa-cogs"></i> <?= $dashboard_title ?></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="dashboard">
            <h2><?= $dashboard_title ?></h2>
            <?php foreach ($dashboard_items as $item): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($item[0]) ?></h3>
                    <p><?= htmlspecialchars($item[1]) ?></p>
                    <a href="<?= htmlspecialchars($item[2]) ?>" class="button">Go</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
