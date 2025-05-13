<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
require_once 'database.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];

$database = new Database();
$conn = $database->getConnection();

// Fetch enrolled courses
$sqlEnrolled = "
SELECT c.courseCode, c.courseName, c.semester, e.status, c.courseId
FROM enrollments e
JOIN courses c ON e.courseId = c.courseId
WHERE e.userId = :userId AND e.status = 'enrolled'
ORDER BY c.semester ASC";
$stmt = $conn->prepare($sqlEnrolled);
$stmt->execute([':userId' => $userId]);
$enrolledCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch waitlisted courses
$sqlWaitlist = "
SELECT c.courseCode, c.courseName, c.semester, e.status, c.courseId
FROM enrollments e
JOIN courses c ON e.courseId = c.courseId
WHERE e.userId = :userId AND e.status = 'waitlisted'
ORDER BY c.semester ASC";
$stmt = $conn->prepare($sqlWaitlist);
$stmt->execute([':userId' => $userId]);
$waitlistedCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>

<?php include 'master.php'; ?>

<div class="container text-center">
    <h2>My Enrolled Courses</h2>

    <?php if (count($enrolledCourses) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrolledCourses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['courseCode']); ?></td>
                        <td><?php echo htmlspecialchars($course['courseName']); ?></td>
                        <td><?php echo $course['semester']; ?></td>
                        <td><span class="label label-success"><?php echo $course['status']; ?></span></td>
                        <td>
                            <a href="cancel_enrollment.php?courseId=<?php echo $course['courseId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this enrollment?');">Cancel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not enrolled in any courses.</p>
    <?php endif; ?>

    <h2>My Waitlisted Courses</h2>

    <?php if (count($waitlistedCourses) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Semester</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($waitlistedCourses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['courseCode']); ?></td>
                        <td><?php echo htmlspecialchars($course['courseName']); ?></td>
                        <td><?php echo $course['semester']; ?></td>
                        <td><span class="label label-warning"><?php echo $course['status']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You are not waitlisted for any courses.</p>
    <?php endif; ?>
</div>

</body>
</html>
