<?php
include 'database.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "Student"){
    exit;
}

$student_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';
$year = $_GET['year'] ?? '';
$adviser = $_GET['adviser'] ?? '';
$department = $_GET['department'] ?? '';

$sql = "
    SELECT t.*, u.fullname AS adviser_name, d.name AS department_name
    FROM thesis t
    LEFT JOIN users u ON t.adviser_id = u.id
    LEFT JOIN departments d ON t.department_id = d.id
    WHERE t.student_id = ?
";

$params = [$student_id];
$types = "i";

if($search != ''){
    $sql .= " AND (t.title LIKE ? OR t.keywords LIKE ? OR u.fullname LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if($year != ''){
    $sql .= " AND YEAR(t.created_at) = ?";
    $params[] = $year;
    $types .= "i";
}

if($adviser != ''){
    $sql .= " AND t.adviser_id = ?";
    $params[] = $adviser;
    $types .= "i";
}

if($department != ''){
    $sql .= " AND t.department_id = ?";
    $params[] = $department;
    $types .= "i";
}

$sql .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>
                <td>".$row['title']."</td>
                <td>".$row['adviser_name']."</td>
                <td>".$row['department_name']."</td>
                <td>";
        if($row['status'] == "Pending") echo "<span style='color:orange;font-weight:bold;'>Pending</span>";
        elseif($row['status'] == "Approved") echo "<span style='color:green;font-weight:bold;'>Approved</span>";
        else echo "<span style='color:red;font-weight:bold;'>Rejected</span>";
        echo "</td>
                <td>".$row['created_at']."</td>
                <td>";
        if($row['status'] == "Approved") echo "<a class='download-link' href='".$row['file']."' target='_blank'>Download</a>";
        else echo "N/A";
        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align:center;'>No thesis submissions found.</td></tr>";
}
?>
