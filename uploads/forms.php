<?php
// uploads/forms.php
require_once '../config/db.php';

// Check Status
$statusFile = '../status.json';
$statusEnabled = true;
if (file_exists($statusFile)) {
  $statusData = json_decode(file_get_contents($statusFile), true);
  $statusEnabled = $statusData['enabled'] ?? true;
}

if (!$statusEnabled) {
  echo "<!DOCTYPE html>
  <html lang='en'>
  <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Form Closed</title>
    <link rel='stylesheet' href='../assets/css/style.css'>
    <style>
      body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
      .message-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
      h1 { color: #dc3545; margin-bottom: 10px; }
    </style>
  </head>
  <body>
    <div class='message-container'>
      <h1>🚫 Form Closed</h1>
      <p>This form is not accepting responses at the moment.</p>
    </div>
  </body>
  </html>";
  exit;
}

$msg = "";
$msgType = "";

// Handle Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name'] ?? '');
  $uid = trim($_POST['uid'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $drive_link = trim($_POST['drive_link'] ?? '');

  if (empty($name) || empty($uid) || empty($email) || empty($drive_link)) {
    $msg = "All fields are required.";
    $msgType = "error";
  } else {
    $stmt = $conn->prepare("INSERT INTO form_responses (name, uid, email, drive_link) VALUES (?, ?, ?, ?)");
    if ($stmt) {
      $stmt->bind_param("ssss", $name, $uid, $email, $drive_link);
      if ($stmt->execute()) {
        $msg = "Your response has been submitted successfully!";
        $msgType = "success";
        // Clear post data to prevent resubmission warning on simple refresh (though PRG pattern is better, keeping simple)
      } else {
        $msg = "Error submitting form: " . $conn->error;
        $msgType = "error";
      }
      $stmt->close();
    } else {
      $msg = "Database error: " . $conn->error;
      $msgType = "error";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Portal - SecureView</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      background-color: #f4f4f4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .form-container {
      background-color: #fff;
      padding: 30px 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
      margin: auto;
    }

    .branding {
      text-align: center;
      margin-bottom: 20px;
    }

    .branding img {
      width: 50px;
      height: 50px;
      margin-bottom: 10px;
    }

    .branding h1,
    h2 {
      margin: 5px 0;
      font-size: 26px;
      color: #333;
    }

    .branding p {
      margin: 0 0 20px;
      font-size: 14px;
      color: #777;
    }

    form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 4px;
      background-color: #198754;
      color: white;
      cursor: pointer;
      font-size: 16px;
    }

    input[type="submit"]:hover {
      background-color: #157347;
    }

    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 4px;
      text-align: center;
    }

    .alert.success {
      background-color: #d1e7dd;
      color: #0f5132;
    }

    .alert.error {
      background-color: #f8d7da;
      color: #842029;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <div class="branding">
      <img src="../assets/lock.png" alt="Lock Icon">
      <h1>SecureView</h1>
      <p>by HackWithEthics</p>
      <h2>Upload Your Work</h2>
    </div>

    <?php if ($msg): ?>
      <div class="alert <?php echo $msgType; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" class="form-control" required placeholder="Enter your full name">

      <label for="uid">UID</label>
      <input type="text" name="uid" id="uid" class="form-control" required placeholder="Enter your UID">

      <label for="email">Email</label>
      <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email">

      <label for="drive_link">Drive Link<span style="font-weight: normal; color: #E4080A;"> {* with shared permission}</span></label>
      <input type="url" name="drive_link" id="drive_link" class="form-control" required placeholder="https://drive.google.com/...">

      <input type="submit" value="Submit Response">
    </form>
  </div>
  <?php include '../includes/footer.php'; ?>
</body>

</html>