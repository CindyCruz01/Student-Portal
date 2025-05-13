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

$database = new Database();
$conn = $database->getConnection();

// Handle semester selection
$selectedSemester = $_GET['semester'] ?? 'Spring';

// Updated SQL: exclude courses where user is already enrolled or waitlisted
$sql = "
SELECT * FROM courses 
WHERE semester = :semester 
AND courseId NOT IN (
    SELECT courseId FROM enrollments WHERE userId = :userId
)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':semester', $selectedSemester);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Available semesters
$semesters = ['Spring', 'Summer', 'Fall'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Courses by Semester</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>

<?php include 'master.php'; ?>

<div class="container text-center">
    <h2>Available Courses - <?php echo htmlspecialchars($selectedSemester); ?> Semester</h2>

    <form method="GET" class="form-inline" style="margin-bottom: 20px;">
        <label for="semester">Select Semester:</label>
        <select name="semester" id="semester" class="form-control">
            <?php foreach ($semesters as $semester): ?>
                <option value="<?php echo $semester; ?>" <?php if ($semester == $selectedSemester) echo 'selected'; ?>>
                    <?php echo $semester; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">View Courses</button>
    </form>

    <?php if (count($courses) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Max Enrollment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['courseCode']); ?></td>
                        <td><?php echo htmlspecialchars($course['courseName']); ?></td>
                        <td><?php echo $course['maxEnrollment']; ?></td>
                        <td>
                            <a href="enroll.php?courseId=<?php echo $course['courseId']; ?>" class="btn btn-success btn-sm">Enroll</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No available courses for this semester, or you are already enrolled/waitlisted in all of them.</p>
    <?php endif; ?>
</div>

</body>
</html>
