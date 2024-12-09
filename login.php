<?php
session_start();
$conn = new mysqli("localhost", "root", "", "user_management");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?)");
    $query->bind_param("ss", $username, $password);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id']; // Save the user ID in the session

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: index.php");
        } elseif ($user['role'] === 'editor') {
            header("Location: index.php");
        } elseif ($user['role'] === 'viewer') {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('https://i.ibb.co.com/RN32j9T/Phases-Spaces-Inc-1.png');">
    <div class="absolute top-10 left-10 flex items-center space-x-4">
        <img alt="Boolean Logic Logo" class="w-12 h-12" height="50" src="https://storage.googleapis.com/a1aa/image/2A5rcgOIsGIuGh26fbO9fGsd3cJB9Twnu0iQwS0aKM57mJ4TA.jpg" width="50"/>
        <span class="text-white text-4xl font-bold">Boolean Logic</span>
    </div>
    <div class="bg-orange-600 p-8 rounded-lg shadow-lg w-80">
        <h2 class="text-white text-3xl mb-6">Log in</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <input class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" name="username" placeholder="Username" required type="text"/>
            </div>
            <div class="mb-6">
                <input class="w-full p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500" name="password" placeholder="Password" required type="password"/>
            </div>
            <button class="w-full p-3 bg-black text-white rounded-lg hover:bg-gray-800" type="submit">Submit</button>
        </form>
    </div>
</body>
</html>