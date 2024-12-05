<?php
session_start();

// Check if user is logged in and has the correct role (admin)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php'; // Include your DB connection

// Process form submission to add a new operator
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // You should hash the password before saving
    $level = 'operator'; // Fixed level for the operator

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $error_message = "Username already exists!";
    } else {
        // Insert new operator into database
        $stmt = $conn->prepare("INSERT INTO users (Username, Password, Level) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $level]);
        header('Location: list_operator.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Operator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Tambah Operator</h3>
        <a href="list_operator.php" class="btn btn-secondary mb-3">Kembali</a>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Operator</button>
        </form>
    </div>
</body>

</html>