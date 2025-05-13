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

// Check if user is already enrolled or waitlisted
$checkSql = "SELECT * FROM enrollments WHERE userId = :userId AND courseId = :courseId";
$stmt = $conn->prepare($checkSql);
$stmt->execute([':userId' => $userId, ':courseId' => $courseId]);
$existing = $stmt->fetch();

if ($existing) {
    echo "<script>alert('You are already enrolled or waitlisted for this course.'); window.location.href='courses.php';</script>";
    exit();
}

// Get course max enrollment
$courseSql = "SELECT maxEnrollment FROM courses WHERE courseId = :courseId";
$stmt = $conn->prepare($courseSql);
$stmt->execute([':courseId' => $courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

$maxEnrollment = $course['maxEnrollment'];

// Count current enrolled students
$countSql = "SELECT COUNT(*) FROM enrollments WHERE courseId = :courseId AND status = 'enrolled'";
$stmt = $conn->prepare($countSql);
$stmt->execute([':courseId' => $courseId]);
$enrolledCount = $stmt->fetchColumn();

// Determine status
$status = ($enrolledCount < $maxEnrollment) ? 'enrolled' : 'waitlisted';

// Insert enrollment
$insertSql = "INSERT INTO enrollments (userId, courseId, status) VALUES (:userId, :courseId, :status)";
$stmt = $conn->prepare($insertSql);
$stmt->execute([':userId' => $userId, ':courseId' => $courseId, ':status' => $status]);

// Feedback to user
$message = ($status === 'enrolled') 
    ? "You have been successfully enrolled in the course."
    : "The course is full. You have been added to the waitlist.";

echo "<script>alert('$message'); window.location.href='courses.php';</script>";
