<?php
include 'database.php';
session_start();

$error = '';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['fullname'] = $user['fullname'];

            include 'activity_log.php';
            logActivity($user['id'], "Logged in");

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Thesis Archives System - Login</title>

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

    /* SAME BACKGROUND COLOR AS REGISTER PAGE */
    background: radial-gradient(circle at top left, #2f3e46 0%, #354f52 30%, #2a363b 70%, #1f262a 100%);
    background-attachment: fixed;
    background-repeat: no-repeat;
}

/* subtle fog like in register */
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

.login-wrapper{
    background:#f7f9fa;
    border-radius:18px;
    width:100%;
    max-width:430px;
    padding:40px 35px 35px 35px;
    box-shadow:0 12px 28px rgba(0,0,0,.25);
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
    color:#6b7280;
    font-size:14px;
    margin-bottom:25px;
}

.input-group{
    position:relative;
}

.input-group input{
    width:100%;
    padding:12px 40px 12px 40px;
    border-radius:999px;
    border:1px solid #d1d5db;
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

.message{
    color:#dc2626;
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

<div class="login-wrapper">

    <div class="title">Welcome to Thesis Archives System!</div>
    <div class="subtitle">Sign in to access your account</div>

    <?php if($error) echo "<div class='message'>$error</div>"; ?>

    <form method="POST">

        <div class="input-group">
            <span class="input-icon">👤</span>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <br>

        <div class="input-group">
            <span class="input-icon">🔒</span>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <br>

        <button type="submit" name="login">🔑 Login</button>
    </form>

    <small>Don't have an account? <a href="register.php">Register here</a></small>
</div>

</body>
</html>
