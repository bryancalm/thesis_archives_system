<?php
include 'database.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "Admin"){
    header("Location: dashboard.php");
    exit;
}

$result = $conn->query("
    SELECT a.*, u.fullname
    FROM activity_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Activity Logs</title>
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

.card {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

table th {
    background-color: #f3f4f6;
    font-weight: 600;
}

table tr:hover { background-color: #f9fafb; }

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
    table th, table td { padding: 10px; font-size: 0.9rem; }
}
</style>
</head>
<body>

<header>
    <h2>Activity Logs</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="card">
        <table>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Date/Time</th>
            </tr>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" style="text-align:center;">No activity logs found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
