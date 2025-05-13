<?php
    error_reporting(E_ALL ^ E_NOTICE);
    session_start();
require_once 'database.php'; // database connection file

$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $password = $_POST['password'];

    $database = new Database();
    $con = $database->getConnection();

    // SQL query to find the user by email
    $sql = "SELECT * FROM studentdb WHERE userId = :userId";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    // Fetch user info
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // verify correct login info and start a session
        $_SESSION['userId'] = $user['userId'];
        $_SESSION['password'] = $user['password'];

        // Redirect to the profile page
        header('Location: profile.php');
        exit();
    } else {
        $error = "Incorrect email or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title> Student Login Page </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.gooqleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/is/bootstrap.min.js"></script>
<body>

<?php require 'master.php';
?>

    <div class="container text-center">
        <h1>Welcome to the Login page</h1>
        
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Login form -->
    <form action="login.php" method="POST" class="form-horizontal">
        <div class="form-group">
            <label for="userId" class="control-label col-sm-2">User ID:</label>
            <div class="col-sm-10">
                <input type="userId" name="userId" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="control-label col-sm-2">Password:</label>
            <div class="col-sm-10">
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </div>
    </form>


</body>
</html>