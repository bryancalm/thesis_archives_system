<?php
include 'database.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "Student"){ 
    header("Location: dashboard.php"); exit; 
}

$error = '';
$success = '';

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $abstract = $_POST['abstract'];
    $keywords = $_POST['keywords'];
    $adviser_id = $_POST['adviser'];
    $course = $_POST['course'];
    $department_id = $_POST['department'];

    if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
        $allowed_ext = ['pdf','doc','docx'];
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed_ext)){
            $new_file_name = time().'_'.$file_name;
            $upload_dir = "uploads/";
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_path = $upload_dir.$new_file_name;

            if(move_uploaded_file($file_tmp, $file_path)){
                $stmt = $conn->prepare("INSERT INTO thesis (student_id,title,abstract,keywords,adviser_id,course,department_id,file) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->bind_param("isssisis", $_SESSION['user_id'], $title, $abstract, $keywords, $adviser_id, $course, $department_id, $file_path);
                
                if($stmt->execute()){
                    include 'activity_log.php';
                    logActivity($_SESSION['user_id'], "Uploaded thesis: $title");
                    $success = "Thesis uploaded successfully!";
                } else {
                    $error = "Database error: Could not save thesis.";
                }
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type! Only PDF, DOC, DOCX allowed.";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}

$advisers = $conn->query("SELECT id, fullname FROM users WHERE role='Faculty'");
$departments = $conn->query("SELECT id, name FROM departments");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Thesis</title>
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

        header h2 { margin: 0; font-size: 1.8rem; }
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
            max-width: 500px; /* compact width like a card */
            margin: 40px auto; /* centered */
            background-color: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }

        h2.page-title {
            text-align: center;
            margin-bottom: 25px;
            color: #111827;
            font-size: 1.8rem;
        }

        form label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #111827;
        }

        form input[type="text"],
        form textarea,
        form select,
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }

        form textarea { resize: vertical; }

        form button {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            width: 100%;
            font-weight: bold;
            transition: 0.3s;
        }

        form button:hover { opacity: 0.85; }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .message.error { color: #dc3545; }
        .message.success { color: #198754; }

        a.back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }
        a.back:hover { text-decoration: underline; }

        @media (max-width: 576px){
            .container { margin: 30px 15px; padding: 25px 20px; }
        }
    </style>
</head>
<body>

<header>
    <h2>Upload Thesis</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2 class="page-title">Upload Thesis</h2>

    <?php if($error) echo "<div class='message error'>$error</div>"; ?>
    <?php if($success) echo "<div class='message success'>$success</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" name="title" required placeholder="Enter thesis title">

        <label for="abstract">Abstract</label>
        <textarea name="abstract" rows="4" required placeholder="Enter thesis abstract"></textarea>

        <label for="keywords">Keywords</label>
        <input type="text" name="keywords" required placeholder="Enter keywords separated by commas">

        <label for="adviser">Adviser</label>
        <select name="adviser" required>
            <option value="">Select Adviser</option>
            <?php while($row = $advisers->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['fullname']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="course">Course</label>
        <input type="text" name="course" required placeholder="Enter course name">

        <label for="department">Department</label>
        <select name="department" required>
            <option value="">Select Department</option>
            <?php while($row = $departments->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="file">Upload Thesis File</label>
        <input type="file" name="file" required>

        <button type="submit" name="submit">Upload Thesis</button>
    </form>

    <a href="dashboard.php" class="back">← Back to Dashboard</a>
</div>

</body>
</html>
