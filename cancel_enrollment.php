<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$courseId = $_GET['courseId'] ?? null;

if (!$courseId) {
    die("Invalid course selection.");
}

$database = new Database();
$conn = $database->getConnection();

// Step 1: Remove the user's current enrollment
$deleteSql = "DELETE FROM enrollments WHERE userId = :userId AND courseId = :courseId AND status = 'enrolled'";
$stmt = $conn->prepare($deleteSql);
$stmt->execute([':userId' => $userId, ':courseId' => $courseId]);

if ($stmt->rowCount() === 0) {
    echo "<script>alert('You are not enrolled in this course.'); window.location.href='my_courses.php';</script>";
    exit();
}

// Step 2: Promote first waitlisted user (FIFO)
$waitlistSql = "SELECT id, userId FROM enrollments 
                WHERE courseId = :courseId AND status = 'waitlisted' 
                ORDER BY created_at ASC LIMIT 1";
$stmt = $conn->prepare($waitlistSql);
$stmt->execute([':courseId' => $courseId]);
$next = $stmt->fetch();

if ($next) {
    $promoteSql = "UPDATE enrollments SET status = 'enrolled' WHERE id = :id";
    $stmt = $conn->prepare($promoteSql);
    $stmt->execute([':id' => $next['id']]);

}

// Step 3: Redirect with success message
echo "<script>alert('Enrollment canceled successfully.'); window.location.href='my_courses.php';</script>";
