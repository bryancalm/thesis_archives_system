<?php
include 'database.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile update
if(isset($_POST['update'])){
    // Profile picture
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0){
        $file_name = $_FILES['profile_pic']['name'];
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if(in_array($file_ext, $allowed)){
            $new_file_name = 'profile_'.$user_id.'_'.time().'.'.$file_ext;
            $upload_dir = "uploads/";
            $profile_path = $upload_dir.$new_file_name;
            move_uploaded_file($file_tmp, $profile_path);
        } else {
            $error = "Invalid profile picture type!";
        }
    } else {
        $profile_path = $user['profile_pic'];
    }

    // Signature
    if(isset($_FILES['signature']) && $_FILES['signature']['error'] == 0){
        $file_name = $_FILES['signature']['name'];
        $file_tmp = $_FILES['signature']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if(in_array($file_ext, $allowed)){
            $new_file_name = 'sign_'.$user_id.'_'.time().'.'.$file_ext;
            $upload_dir = "uploads/";
            $signature_path = $upload_dir.$new_file_name;
            move_uploaded_file($file_tmp, $signature_path);
        } else {
            $error = "Invalid signature type!";
        }
    } else {
        $signature_path = $user['signature'];
    }

    // Update database
    $update = $conn->prepare("UPDATE users SET profile_pic=?, signature=? WHERE id=?");
    $update->bind_param("ssi", $profile_path, $signature_path, $user_id);
    if($update->execute()){
        include 'activity_log.php';
        logActivity($user_id, "Updated profile picture and signature");
        $success = "Profile updated successfully!";
        // refresh user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Failed to update profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
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
    max-width: 600px;
    margin: 50px auto;
    background-color: #fff;
    padding: 30px;
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
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #111827;
}

form input[type="text"],
form input[type="email"],
form input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
form button:hover { opacity: 0.85; }

.message { text-align: center; font-weight: bold; margin-bottom: 20px; }
.message.error { color: #dc3545; }
.message.success { color: #198754; }

img.profile-img { width: 100px; border-radius: 50%; margin-bottom: 10px; }
img.signature-img { width: 150px; margin-bottom: 10px; }

.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #2563eb;
    font-weight: bold;
    text-decoration: none;
}
.back-link:hover { text-decoration: underline; }

@media(max-width: 768px) {
    .container { margin: 30px 15px; padding: 20px; }
}
</style>
</head>
<body>

<header>
    <h2>My Profile</h2>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2 class="page-title">Profile Details</h2>

    <?php if($error) echo "<div class='message error'>$error</div>"; ?>
    <?php if($success) echo "<div class='message success'>$success</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Full Name:</label>
        <input type="text" value="<?php echo $user['fullname']; ?>" disabled>

        <label>Email:</label>
        <input type="email" value="<?php echo $user['email']; ?>" disabled>

        <label>Username:</label>
        <input type="text" value="<?php echo $user['username']; ?>" disabled>

        <label>Profile Picture:</label>
        <?php if(!empty($user['profile_pic'])): ?>
            <img src="<?php echo $user['profile_pic']; ?>" class="profile-img"><br>
        <?php endif; ?>
        <input type="file" name="profile_pic">

        <label>Signature:</label>
        <?php if(!empty($user['signature'])): ?>
            <img src="<?php echo $user['signature']; ?>" class="signature-img"><br>
        <?php endif; ?>
        <input type="file" name="signature">

        <button type="submit" name="update">Update Profile</button>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
