<?php
include 'database.php';
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$role = $_SESSION['role'];
$fullname = $_SESSION['fullname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f6f8;
    color: #333;
}

a { text-decoration: none; transition: 0.3s; }
h2, h3 { margin: 0; padding: 0; }

header {
    background-color: #1f2937;
    color: #fff;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.header-left { display: flex; flex-direction: column; }
.header-left h2 { font-size: 1.8rem; }
.header-left span { font-size: 0.95rem; color: #a1a1aa; margin-top: 4px; }

header nav a {
    margin-left: 15px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-radius: 5px;
    color: #fff;
}
header nav a:hover { opacity: 0.85; }

.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.card {
    background-color: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.card h3 {
    color: #111827;
    margin-bottom: 15px;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
}
.card h3 svg { margin-right: 10px; width: 22px; height: 22px; fill: #2563eb; }

.card a {
    display: inline-block;
    margin: 10px 0;
    padding: 10px 18px;
    border-radius: 6px;
    background: linear-gradient(135deg, #10b981, #34d399);
    color: #fff;
    font-weight: bold;
    transition: 0.3s;
}
.card a:hover { opacity: 0.85; }

ul.notifications { list-style: none; padding-left: 0; }
ul.notifications li {
    background-color: #f9fafb;
    padding: 12px 15px;
    border-left: 5px solid #2563eb;
    border-radius: 6px;
    margin-bottom: 12px;
    font-size: 0.95rem;
}
ul.notifications li small { color: #6b7280; }

footer {
    text-align: center;
    padding: 20px 0;
    margin: 40px auto 20px;
    font-size: 0.9rem;
    color: #6b7280;
}

.student a { background: linear-gradient(135deg, #10b981, #34d399); }
.faculty a { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.admin a { background: linear-gradient(135deg, #ef4444, #f87171); }

/* About System Section */
.about-system {
    margin-top: 50px;
}
.about-system h2 {
    text-align: center;
    color: #111827;
    margin-bottom: 30px;
}
.about-system-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.about-card {
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: center;
}
.about-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}
.about-card h4 {
    color: #2563eb;
    margin-bottom: 12px;
}
.about-card p { font-size: 0.95rem; color: #374151; }

@media (max-width: 768px) {
    header { flex-direction: column; align-items: flex-start; }
    header nav a { margin: 10px 0 0 0; }
    .container { grid-template-columns: 1fr; }
    .about-system-cards { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<header>
    <div class="header-left">
        <h2>Welcome, <?php echo $fullname; ?></h2>
        <span>Role: <?php echo $role; ?></span>
    </div>
    <nav>
        <a href="profile.php">My Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">

<?php if($role == "Student"): ?>
    <div class="card student">
        <h3>
            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            Student Dashboard
        </h3>
        <a href="upload_thesis.php">Upload Thesis</a><br>
        <a href="view_status.php">View Submission Status</a>
    </div>

    <div class="card">
        <h3>
            <svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 002 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4a1.5 1.5 0 00-3 0v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            Recent Reviews / Notifications
        </h3>
        <?php
        $stmt = $conn->prepare("
            SELECT r.comment, r.action, t.title, r.created_at
            FROM review_logs r
            INNER JOIN thesis t ON r.thesis_id = t.id
            WHERE t.student_id = ?
            ORDER BY r.created_at DESC
            LIMIT 5
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $reviews = $stmt->get_result();
        ?>
        <?php if($reviews->num_rows > 0): ?>
            <ul class="notifications">
                <?php while($row = $reviews->fetch_assoc()): ?>
                    <li>
                        <strong>Thesis:</strong> <?php echo $row['title']; ?><br>
                        <strong>Status:</strong> <?php echo $row['action']; ?><br>
                        <strong>Comment:</strong> <?php echo $row['comment']; ?><br>
                        <small><?php echo $row['created_at']; ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>
    </div>

<?php elseif($role == "Faculty"): ?>
    <div class="card faculty">
        <h3>
            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 3.87 3.13 7 7 7s7-3.13 7-7c0-3.87-3.13-7-7-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>
            Faculty Dashboard
        </h3>
        <a href="review_thesis.php">Review Student Submissions</a><br>
        <a href="activity_logs.php">View My Activity Logs</a>
    </div>

<?php elseif($role == "Admin"): ?>
    <div class="card admin">
        <h3>
            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            Admin Dashboard
        </h3>
        <a href="manage_users.php">Manage Users</a><br>
        <a href="manage_departments.php">Manage Departments</a><br>
        <a href="manage_programs.php">Manage Programs</a><br>
        <a href="activity_logs.php">View Activity Logs</a>
    </div>
<?php endif; ?>

</div>

<!-- About the System Section -->
<div class="about-system">
    <h2>About the System</h2>
    <div class="about-system-cards">
        <div class="about-card">
            <h4>Upload & Track Thesis</h4>
            <p>Students can upload their thesis, track submission status, and download approved files easily.</p>
        </div>
        <div class="about-card">
            <h4>Faculty Review</h4>
            <p>Faculty members can review submissions, provide comments, and approve or reject theses efficiently.</p>
        </div>
        <div class="about-card">
            <h4>Notifications & Updates</h4>
            <p>The system provides real-time notifications for students on thesis reviews and status changes.</p>
        </div>
        <div class="about-card">
            <h4>Activity Logging</h4>
            <p>All actions in the system are logged to ensure accountability and easy monitoring by admins.</p>
        </div>
    </div>
</div>

<footer>
    &copy; <?php echo date('Y'); ?> Thesis Management System — Pangasinan State University
</footer>

</body>
</html>
