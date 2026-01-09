<?php
// admin_panel.php
session_start();
require_once 'config/db.php';

// Auth Check
if (!isset($_SESSION['admin_logged_in'])) {
  header('Location: admin_login.php');
  exit();
}

$uploadDir = __DIR__ . "/docs/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$success = $error = "";

// Handle Form Toggle (Status)
$statusFile = 'status.json';
if (!file_exists($statusFile)) {
  file_put_contents($statusFile, json_encode(['enabled' => true]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 1. Handle Toggle
  if (isset($_POST['form_toggle'])) {
    $newStatus = isset($_POST['enabled']);
    file_put_contents($statusFile, json_encode(['enabled' => $newStatus]));
    $success = "Form status updated.";
  }

  // 2. Handle File Upload
  if (isset($_FILES['pdf_file'])) {
    $filename = basename($_FILES['pdf_file']['name']);
    $targetPath = $uploadDir . $filename;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

    if ($fileType != "pdf") {
      $error = "Only PDF files are allowed.";
    } else {
      if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $targetPath)) {
        $success = "File uploaded: $filename";
      } else {
        $error = "Failed to upload file.";
      }
    }
  }

  // 3. Handle Document Settings Update
  if (isset($_POST['selected_file'])) {
    $selectedFile = trim($_POST['selected_file']);
    $startTime = $_POST['start_time'];
    $expirationTime = $_POST['expiration_time'];

    if (strtotime($startTime) >= strtotime($expirationTime)) {
      $error = "Start time must be before expiration time.";
    } else {
      // Using Prepared Statement
      $conn->query("DELETE FROM document_settings"); // clear old settings
      $stmt = $conn->prepare("INSERT INTO document_settings (file_name, start_time, expiration_time) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $selectedFile, $startTime, $expirationTime);

      if ($stmt->execute()) {
        $success = "Document settings updated successfully!";
      } else {
        $error = "Error updating settings: " . $conn->error;
      }
    }
  }

  // 4. Handle Create User
  if (isset($_POST['create_user'])) {
    $userName = trim($_POST['user_name'] ?? '');
    $userUid = trim($_POST['user_uid'] ?? '');

    if (empty($userName) || empty($userUid)) {
      $error = "Name and UID are required.";
    } else {
      // Check for existing UID
      $checkStmt = $conn->prepare("SELECT id FROM users WHERE uid = ?");
      $checkStmt->bind_param("s", $userUid);
      $checkStmt->execute();
      $checkStmt->store_result();

      if ($checkStmt->num_rows > 0) {
        $error = "User with UID '$userUid' already exists.";
      } else {
        $insStmt = $conn->prepare("INSERT INTO users (name, uid) VALUES (?, ?)");
        $insStmt->bind_param("ss", $userName, $userUid);
        if ($insStmt->execute()) {
          $success = "User '$userName' created successfully.";
        } else {
          $error = "Failed to create user: " . $conn->error;
        }
      }
    }
  }
}

$currentStatus = json_decode(file_get_contents($statusFile), true)["enabled"];
$currentSettings = $conn->query("SELECT * FROM document_settings LIMIT 1")->fetch_assoc();
$files = array_diff(scandir($uploadDir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - SecureView</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid #eee;
    }

    .card h3 {
      margin-bottom: 20px;
      border-bottom: 2px solid var(--light-bg);
      padding-bottom: 10px;
      color: var(--primary-color);
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: var(--border-radius);
    }

    /* Switch Toggle */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: var(--success-color);
    }

    input:checked+.slider:before {
      transform: translateX(26px);
    }

    .alert {
      padding: 15px;
      border-radius: var(--border-radius);
      margin-bottom: 20px;
    }

    .alert-success {
      background: #d4edda;
      color: #155724;
    }

    .alert-error {
      background: #f8d7da;
      color: #721c24;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: white;
      padding: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
  </style>
</head>

<body>

  <div class="header">
    <h1>SecureView Admin</h1>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>

  <div class="container">
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">
      <!-- Access Control -->
      <div class="card">
        <h3>Access Control</h3>
        <form method="POST">
          <div class="d-flex align-items-center justify-content-between">
            <span>From Responses</span>
            <label class="switch">
              <input type="checkbox" name="enabled" <?= $currentStatus ? 'checked' : '' ?> onchange="this.form.submit();">
              <span class="slider"></span>
            </label>
          </div>
          <input type="hidden" name="form_toggle" value="1">
          <p class="mt-3 text-center">
            <a href="uploads/forms.php" target="_blank" style="color: var(--primary-color);">View Public Form</a><br>
            <a href="admin_responses.php" style="color: var(--primary-color);">View Form Responses</a>
          </p>
          </p>
        </form>
      </div>

      <!-- File Upload -->
      <div class="card">
        <h3>Upload PDF</h3>
        <form method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="pdf_file">Select PDF File</label>
            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%;">Upload</button>
        </form>
      </div>

      <!-- Create User -->
      <div class="card">
        <h3>Create User</h3>
        <form method="POST">
          <div class="form-group">
            <label for="user_name">Full Name</label>
            <input type="text" name="user_name" id="user_name" class="form-control" placeholder="e.g. John Doe" required>
          </div>
          <div class="form-group">
            <label for="user_uid">User ID (UID)</label>
            <input type="text" name="user_uid" id="user_uid" class="form-control" placeholder="e.g. 1001" required>
          </div>
          <input type="hidden" name="create_user" value="1">
          <button type="submit" class="btn btn-primary" style="width: 100%;">Add User</button>
        </form>
      </div>

      <!-- Document Settings -->
      <div class="card" style="grid-column: 1 / -1;">
        <h3>Active Document Settings</h3>
        <form method="post">
          <div class="dashboard-grid" style="margin-top: 0; box-shadow: none; border: none; padding: 0;">
            <div class="form-group">
              <label for="selected_file">Select Document</label>
              <select name="selected_file" id="selected_file" class="form-control" required>
                <option value="">-- Choose a file --</option>
                <?php foreach ($files as $file): ?>
                  <option value="<?php echo htmlspecialchars($file); ?>"
                    <?php if ($currentSettings && $currentSettings['file_name'] === $file) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($file); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="start_time">Start Time</label>
              <input type="datetime-local" name="start_time" id="start_time" class="form-control" required
                value="<?php echo $currentSettings ? date('Y-m-d\TH:i', strtotime($currentSettings['start_time'])) : ''; ?>">
            </div>

            <div class="form-group">
              <label for="expiration_time">Expiration Time</label>
              <input type="datetime-local" name="expiration_time" id="expiration_time" class="form-control" required
                value="<?php echo $currentSettings ? date('Y-m-d\TH:i', strtotime($currentSettings['expiration_time'])) : ''; ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>

</html>