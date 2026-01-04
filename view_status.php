<?php
include 'database.php';
session_start();

// Only allow students
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "Student"){
    header("Location: dashboard.php");
    exit;
}

// Fetch student's theses
$student_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT t.*, u.fullname AS adviser_name, d.name AS department_name
    FROM thesis t
    LEFT JOIN users u ON t.adviser_id = u.id
    LEFT JOIN departments d ON t.department_id = d.id
    WHERE t.student_id = ?
    ORDER BY t.created_at DESC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Thesis Submissions</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
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

        header h2 {
            margin: 0;
            font-size: 1.8rem;
        }

        header nav a {
            margin-left: 15px;
            padding: 8px 16px;
            border-radius: 5px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            text-decoration: none;
        }

        header nav a:hover {
            opacity: 0.85;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2.page-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: #111827;
        }

        .search-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .search-filters input, .search-filters select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: #2563eb;
            color: #fff;
            font-weight: 600;
        }

        table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table tr:hover {
            background-color: #e0f2fe;
        }

        a.download-link {
            display: inline-block;
            padding: 6px 12px;
            background: linear-gradient(135deg, #10b981, #34d399);
            color: #fff;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }

        a.download-link:hover {
            opacity: 0.85;
        }

        p.back-link {
            text-align: center;
            margin-top: 25px;
        }

        p.back-link a {
            padding: 10px 18px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        p.back-link a:hover {
            opacity: 0.85;
        }

        @media (max-width: 768px) {
            table th, table td {
                padding: 10px;
                font-size: 0.9rem;
            }
            .search-filters {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>

<header>
    <h2>My Thesis Submissions</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2 class="page-title">My Thesis Submissions</h2>

    <div class="search-filters">
        <input type="text" id="search" placeholder="Search by title, keywords, adviser...">
        <select id="filter-year">
            <option value="">All Years</option>
            <?php
            $years = $conn->query("SELECT DISTINCT YEAR(created_at) AS year FROM thesis WHERE student_id = $student_id ORDER BY year DESC");
            while($y = $years->fetch_assoc()){
                echo "<option value='".$y['year']."'>".$y['year']."</option>";
            }
            ?>
        </select>
        <select id="filter-adviser">
            <option value="">All Advisers</option>
            <?php
            $advisers = $conn->query("SELECT DISTINCT u.id, u.fullname FROM thesis t LEFT JOIN users u ON t.adviser_id = u.id WHERE t.student_id = $student_id");
            while($a = $advisers->fetch_assoc()){
                echo "<option value='".$a['id']."'>".$a['fullname']."</option>";
            }
            ?>
        </select>
        <select id="filter-department">
            <option value="">All Departments</option>
            <?php
            $departments = $conn->query("SELECT DISTINCT d.id, d.name FROM thesis t LEFT JOIN departments d ON t.department_id = d.id WHERE t.student_id = $student_id");
            while($d = $departments->fetch_assoc()){
                echo "<option value='".$d['id']."'>".$d['name']."</option>";
            }
            ?>
        </select>
    </div>

    <table id="thesis-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Adviser</th>
                <th>Department</th>
                <th>Status</th>
                <th>Uploaded At</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['adviser_name']; ?></td>
                    <td><?php echo $row['department_name']; ?></td>
                    <td>
                        <?php 
                            if($row['status'] == "Pending") echo "<span style='color:orange;font-weight:bold;'>Pending</span>";
                            elseif($row['status'] == "Approved") echo "<span style='color:green;font-weight:bold;'>Approved</span>";
                            else echo "<span style='color:red;font-weight:bold;'>Rejected</span>";
                        ?>
                    </td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <?php if($row['status'] == "Approved"): ?>
                            <a class="download-link" href="<?php echo $row['file']; ?>" target="_blank">Download</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No thesis submissions yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <p class="back-link"><a href="dashboard.php">Back to Dashboard</a></p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    function fetchTheses(){
        var search = $('#search').val();
        var year = $('#filter-year').val();
        var adviser = $('#filter-adviser').val();
        var department = $('#filter-department').val();

        $.ajax({
            url: 'fetch_theses.php',
            type: 'GET',
            data: {
                search: search,
                year: year,
                adviser: adviser,
                department: department
            },
            success: function(data){
                $('#thesis-table tbody').html(data);
            }
        });
    }

    $('#search').on('keyup', fetchTheses);
    $('#filter-year, #filter-adviser, #filter-department').on('change', fetchTheses);
});
</script>

</body>
</html>
