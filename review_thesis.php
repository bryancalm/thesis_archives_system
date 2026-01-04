<?php
include 'database.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "Faculty"){
    header("Location: dashboard.php");
    exit;
}

$faculty_id = $_SESSION['user_id'];
$error = '';
$success = '';

if(isset($_POST['review'])){
    $thesis_id = $_POST['thesis_id'];
    $action = $_POST['action'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO review_logs (thesis_id, reviewer_id, comment, action) VALUES (?,?,?,?)");
    $stmt->bind_param("iiss", $thesis_id, $faculty_id, $comment, $action);

    if($stmt->execute()){
        $update = $conn->prepare("UPDATE thesis SET status=? WHERE id=?");
        $update->bind_param("si", $action, $thesis_id);
        $update->execute();

        include 'activity_log.php';
        logActivity($faculty_id, "$action thesis ID $thesis_id with comment: $comment");

        $success = "Review submitted successfully!";
    } else {
        $error = "Failed to submit review.";
    }
}

$stmt = $conn->prepare("
    SELECT t.*, u.fullname AS student_name, d.name AS department_name
    FROM thesis t
    LEFT JOIN users u ON t.student_id = u.id
    LEFT JOIN departments d ON t.department_id = d.id
    WHERE t.adviser_id = ? AND t.status='Pending'
    ORDER BY t.created_at ASC
");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Review Thesis</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
    padding: 0;
}

header {
    background-color: #1f2937;
    color: #fff;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
header h2 { margin:0; font-size:1.5rem; }
header nav a {
    margin-left: 15px;
    padding: 8px 16px;
    border-radius: 5px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #fff;
    text-decoration: none;
}
header nav a:hover { opacity: 0.85; }

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.message { text-align: center; font-weight: bold; margin-bottom: 20px; }
.message.error { color: #dc3545; }
.message.success { color: #198754; }

.card {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.card h3 { margin-top:0; color: #111827; font-size: 1.2rem; }
.card p { margin: 5px 0; }
.card a { color: #2563eb; text-decoration: none; }
.card a:hover { text-decoration: underline; }

form textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    resize: vertical;
}

form select, form button {
    padding: 10px;
    border-radius: 8px;
    margin-top: 5px;
    font-size: 14px;
}

form button {
    background: linear-gradient(135deg, #10b981, #34d399);
    border: none;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
    transition: 0.3s;
}
form button:hover { opacity: 0.85; }

.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #2563eb;
    font-weight: bold;
    text-decoration: none;
}
.back-link:hover { text-decoration: underline; }

@media(max-width:768px){
    .container { margin: 20px 15px; }
}
</style>
</head>
<body>

<header>
    <h2>Pending Thesis Submissions</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">

    <?php if($error) echo "<div class='message error'>$error</div>"; ?>
    <?php if($success) echo "<div class='message success'>$success</div>"; ?>

    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><strong>Student:</strong> <?php echo htmlspecialchars($row['student_name']); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($row['department_name']); ?></p>
                <p><strong>File:</strong> <a href="<?php echo $row['file']; ?>" target="_blank">View</a></p>

                <form method="POST">
                    <input type="hidden" name="thesis_id" value="<?php echo $row['id']; ?>">
                    <label>Comment:</label>
                    <textarea name="comment" required></textarea>
                    <label>Action:</label>
                    <select name="action" required>
                        <option value="Approved">Approve</option>
                        <option value="Rejected">Reject</option>
                    </select>
                    <button type="submit" name="review">Submit Review</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No pending thesis submissions.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
