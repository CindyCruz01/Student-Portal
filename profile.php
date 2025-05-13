<?php 
error_reporting(E_ALL ^ E_NOTICE);
require 'master.php';
// Start the session and check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Include the database connection
require_once 'database.php';

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Get the logged-in user's username from the session
$userId = $_SESSION['userId'];

// SQL query that fetches user data from the database
$sql = "SELECT userId, firstName, lastName, phone, email FROM studentdb WHERE userId = :userId";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userId', $userId);
$stmt->execute();

// Fetch the user data
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user not found, redirect to login
if (!$user) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title> Profile Page </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.gooqleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/is/bootstrap.min.js"></script>
</head>
<body>
    
    
    <div class="container text-center">
    <h1>Student Profile</h1>
     <!-- Display User Profile Data in a Table -->
     <table class="table table-bordered">
        <tr>

        <tr>
            <th>User ID</th>
            <td><?php echo htmlspecialchars($user['userId']); ?></td>
        </tr>
         <th>First Name</th>
            <td><?php echo htmlspecialchars($user['firstName']); ?></td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td><?php echo htmlspecialchars($user['lastName']); ?></td>
        </tr>
        
        <tr>
            <th>Phone</th>
            <td><?php echo htmlspecialchars($user['phone']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
    </table>
    </div>

</body>
</html>