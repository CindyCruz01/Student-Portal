<?php
include_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // assign POST variables
    $userId = htmlspecialchars($_POST['userId']);
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

    // Connect Database class
    $database = new Database();
    $con = $database->getConnection();

    // SQL insert statement
    $sql = "INSERT INTO studentdb (userId, firstName, lastName, phone, email, password) 
            VALUES ('$userId', '$firstName', '$lastName', '$phone', '$email', '$password')";

    // Execute the query
    if ($database->executeQuery($con, $sql)) {
        echo "Registration successful!";
    } else {
        echo "Error: Unable to register. Please try again.";
    }
}
?>
