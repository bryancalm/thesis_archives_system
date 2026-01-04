<?php
include 'database.php';
session_start();

$error = '';
$success = '';

if(isset($_POST['register'])){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $check = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){
        $error = "Username or Email already exists!";
    } else {
        $insert = $conn->prepare("INSERT INTO users (fullname,email,username,password,role) VALUES (?,?,?,?,?)");
        $insert->bind_param("sssss", $fullname, $email, $username, $password, $role);

        if($insert->execute()){
            $user_id = $conn->insert_id;
            include 'activity_log.php';
            logActivity($user_id, "Registered as $role");
            $success = "Registration Successful! You can now login.";
        } else {
            $error = "Something went wrong!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Thesis Archives System - Register</title>

<style>
*{
    box-sizing:border-box;
}

body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    /* NEW SOFT, NOT BRIGHT BACKGROUND */
    background: radial-gradient(circle at top left, #2f3e46 0%, #354f52 30%, #2a363b 70%, #1f262a 100%);
    background-attachment: fixed;
    background-repeat: no-repeat;
}

/* subtle fog effect */
body::before{
    content:'';
    position:fixed;
    width:420px;
    height:420px;
    border-radius:50%;
    background: rgba(255,255,255,0.06);
    top:12%;
    left:8%;
    filter: blur(40px);
}

body::after{
    content:'';
    position:fixed;
    width:360px;
    height:360px;
    border-radius:50%;
    background: rgba(255,255,255,0.05);
    bottom:8%;
    right:10%;
    filter: blur(35px);
}

/* CARD slightly darker to match theme */
.register-wrapper{
    background:#f7f9fa;
    border-radius:18px;
    width:100%;
    max-width:460px;
    padding:40px 35px;
    box-shadow:0 15px 35px rgba(0,0,0,.25);
}

.title{
    font-size:24px;
    font-weight:800;
    margin-bottom:5px;
    color:#0f172a;
    text-align:center;
}

.subtitle{
    text-align:center;
    color:#4b5563;
    font-size:14px;
    margin-bottom:25px;
}

.input-group{
    position:relative;
    margin-bottom:14px;
}

.input-group input,
.input-group select{
    width:100%;
    padding:12px 40px 12px 40px;
    border-radius:999px;
    border:1px solid #cbd5e1;
    font-size:14px;
    outline:none;
    background:#ffffff;
}

.input-icon{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    font-size:14px;
    color:#6b7280;
}

button{
    margin-top:5px;
    width:100%;
    padding:12px;
    border-radius:999px;
    border:none;
    font-size:15px;
    font-weight:700;
    background:#2f855a;
    color:white;
    cursor:pointer;
}

button:hover{
    opacity:.9;
}

.message-error{
    color:#dc2626;
    font-weight:600;
    text-align:center;
    margin-bottom:8px;
}

.message-success{
    color:#16a34a;
    font-weight:600;
    text-align:center;
    margin-bottom:8px;
}

small{
    display:block;
    text-align:center;
    margin-top:10px;
    color:#6b7280;
}

a{
    color:#2563eb;
    font-weight:600;
    text-decoration:none;
}

a:hover{
    text-decoration:underline;
}
</style>

</head>
<body>

<div class="register-wrapper">

    <div class="title">Create your account</div>
    <div class="subtitle">Thesis Archives System Registration</div>

    <?php if($error) echo "<div class='message-error'>$error</div>"; ?>
    <?php if($success) echo "<div class='message-success'>$success</div>"; ?>

    <form method="POST">

        <div class="input-group">
            <span class="input-icon">🧍</span>
            <input type="text" name="fullname" placeholder="Full Name" required>
        </div>

        <div class="input-group">
            <span class="input-icon">📧</span>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <span class="input-icon">👤</span>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <span class="input-icon">🔒</span>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="input-group">
            <span class="input-icon">🎓</span>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="Student">Student</option>
                <option value="Faculty">Faculty / Adviser</option>
                <option value="Admin">Admin</option>
            </select>
        </div>

        <button type="submit" name="register">📝 Register</button>
    </form>

    <small>Already have an account? <a href="login.php">Login here</a></small>

</div>

</body>
</html>
