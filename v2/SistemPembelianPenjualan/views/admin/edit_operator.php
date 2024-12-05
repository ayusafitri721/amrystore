<?php
session_start();

// Check if user is logged in and has the correct role (admin)
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../../db/config.php'; // Include your DB connection

// Get operator ID from the URL
if (isset($_GET['id'])) {
    $operatorId = $_GET['id'];

    // Fetch the operator data
    $stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
    $stmt->execute([$operatorId]);
    $operator = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$operator) {
        header('Location: list_operator.php'); // Redirect if operator not found
        exit();
    }
}

// Process form submission to update operator
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE users SET Username = ?, Password = ? WHERE ID = ?");
    $stmt->execute([$username, $password, $operatorId]);
    header('Location: list_operator.php'); // Redirect to the list of operators
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Operator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3>Edit Operator</h3>
        <a href="list_operator.php" class="btn btn-secondary mb-3">Kembali</a>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($operator['Username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Operator</button>
        </form>
    </div>
</body>

</html>