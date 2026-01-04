<?php
include 'database.php';
session_start();
// Only Admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "Admin"){
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

// Add Department
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    if($stmt->execute()){
        $success = "Department added!";
    } else {
        $error = "Failed to add department.";
    }
}

// Delete Department
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM departments WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $success = "Department deleted!";
    } else {
        $error = "Failed to delete department.";
    }
}

// Fetch Departments
$result = $conn->query("SELECT * FROM departments ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Departments</title>
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
    max-width: 700px;
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
    margin-bottom: 30px;
}

.card form input[type="text"] {
    width: calc(100% - 120px);
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    margin-right: 10px;
}

.card form button {
    padding: 10px 20px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
.card form button:hover { opacity: 0.85; }

.table-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
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

table th { background-color: #f3f4f6; font-weight: 600; }
table tr:hover { background-color: #f9fafb; }

.delete-btn {
    padding: 6px 12px;
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.delete-btn:hover { opacity: 0.85; }

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
    .card form input[type="text"] { width: 100%; margin-bottom:10px; }
    .card form button { width: 100%; }
    table th, table td { padding: 10px; font-size: 0.9rem; }
}
</style>
</head>
<body>

<header>
    <h2>Manage Departments</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">

    <?php if($error) echo "<div class='message error'>$error</div>"; ?>
    <?php if($success) echo "<div class='message success'>$success</div>"; ?>

    <div class="card">
        <form method="POST">
            <input type="text" name="name" placeholder="Department Name" required>
            <button type="submit" name="add">Add Department</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>
                            <a href="manage_departments.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this department?')" class="delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" style="text-align:center;">No departments found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
