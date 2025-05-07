<?php
session_start();
include 'Config/connect.php';
include 'Config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT username, first_name FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Handle password change
$passwordChangeMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch();

    if ($userData && password_verify($currentPassword, $userData['password'])) {
        // Update password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedNewPassword, $userId]);
        $passwordChangeMessage = "Password changed successfully.";
    } else {
        $passwordChangeMessage = "Current password is incorrect.";
    }
}

// Handle new record
$recordMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_record'])) {
    $text = $_POST['text'] ?? '';
    $issue = $_POST['issue'] ?? '';
    $comment = $_POST['comment'] ?? '';
    $location = $_POST['location'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $pdo->prepare("INSERT INTO records (user_id, text, created_at, ip_address) VALUES (?, ?, NOW(), ?)");
    // For simplicity, concatenating the fields for the 'text' column; you can create separate columns if you prefer
    $fullText = "Text: $text; Issue: $issue; Comment: $comment; Location: $location";
    $stmt->execute([$userId, $fullText, $ip]);
    $recordMessage = "Record added successfully.";
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie(session_name(), '', time() - 3600);
    header('Location: index.php');
    exit;
}

// Fetch all records for viewing
$stmt = $pdo->query("SELECT r.id, r.user_id, r.text, r.created_at, r.ip_address, u.username FROM records r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 15px; max-width:600px; }
        form { margin-bottom: 20px; }
        label { display: block; margin-top: 10px; }
        input[type=text], input[type=password], textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        textarea { resize: vertical; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; }
        .record-user { font-weight: bold; }
        .record-actions a { margin-right: 10px; }
        .message { color: green; }
        .error { color: red; }
        #logoutBtn { float: right; }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
    <a href="?logout=1" id="logoutBtn">Logout</a>

    <h2>Change Password</h2>
    <?php if ($passwordChangeMessage): ?>
        <p class="<?php echo strpos($passwordChangeMessage, 'successfully') !== false ? 'message' : 'error'; ?>">
            <?php echo htmlspecialchars($passwordChangeMessage); ?>
        </p>
    <?php endif; ?>
    <form method="POST">
        <label>Current Password:
            <input type="password" name="current_password" required>
        </label>
        <label>New Password:
            <input type="password" name="new_password" required>
        </label>
        <input type="submit" name="change_password" value="Change Password">
    </form>

    <h2>Add Record</h2>
    <?php if ($recordMessage): ?>
        <p class="message"><?php echo htmlspecialchars($recordMessage); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Text:
            <textarea name="text" rows="2" required></textarea>
        </label>
        <label>Issue:
            <input type="text" name="issue" maxlength="255">
        </label>
        <label>Comment:
            <input type="text" name="comment" maxlength="255">
        </label>
        <label>Location:
            <input type="text" name="location" maxlength="255">
        </label>
        <input type="submit" name="add_record" value="Add Record">
    </form>

    <h2>All Records</h2>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Text</th>
                <th>Created At</th>
                <th>IP Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $rec): ?>
                <tr>
                    <td class="record-user"><?php echo htmlspecialchars($rec['username']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($rec['text'])); ?></td>
                    <td><?php echo htmlspecialchars($rec['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($rec['ip_address']); ?></td>
                    <td class="record-actions">
                        <?php if ($rec['user_id'] == $userId): ?>
                            <a href="edit_record.php?id=<?php echo $rec['id']; ?>">Edit</a>
                            <a href="delete_record.php?id=<?php echo $rec['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        <?php else: ?>
                            &ndash;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($records) === 0): ?>
                <tr><td colspan="5">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
</content>
</create_file>
