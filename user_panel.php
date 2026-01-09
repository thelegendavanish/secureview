<?php
// user_panel.php
session_start();
require_once 'config/db.php';
date_default_timezone_set("Asia/Kolkata");

// 1. Auth Check
if (!isset($_SESSION['uid'])) {
  header('Location: login.php');
  exit();
}

$name = $_SESSION['name'];
$uid = $_SESSION['uid'];
$ip = $_SERVER['REMOTE_ADDR'];

// 2. Get Settings
$settings = $conn->query("SELECT * FROM document_settings LIMIT 1")->fetch_assoc();

if (!$settings) die("No document configured by admin.");

$file_name = $settings['file_name'];
$startTime = strtotime($settings['start_time']);
$endTime = strtotime($settings['expiration_time']);
$currentTime = time();

// 3. Validation
$filePath = __DIR__ . "/docs/" . $file_name;
$fileError = "";
if (empty($file_name) || !file_exists($filePath)) {
  $fileError = "Document not found on server.";
}

// 4. Time Check Status
$status = "waiting"; // waiting, active, expired
if ($currentTime < $startTime) $status = "waiting";
elseif ($currentTime >= $startTime && $currentTime < $endTime) $status = "active";
else $status = "expired";

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SecureView Document Portal</title>
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- PDF.js - Setup Worker explicitly -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';
  </script>

  <style>
    body {
      background-color: #f4f6f8;
      user-select: none;
    }

    .navbar {
      background: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .user-badge {
      font-size: 0.9rem;
      color: var(--secondary-color);
      background: #eee;
      padding: 5px 10px;
      border-radius: 20px;
    }

    .content-area {
      max-width: 1000px;
      margin: 30px auto;
      text-align: center;
      padding: 0 20px;
    }

    .timer-box {
      background: #333;
      color: white;
      padding: 15px 30px;
      border-radius: 50px;
      display: inline-block;
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .message-box {
      padding: 40px;
      background: white;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    /* PDF Container */
    #pdf-wrapper {
      position: relative;
      background: #525659;
      padding: 20px;
      border-radius: var(--border-radius);
      min-height: 500px;
    }

    canvas {
      display: block;
      margin: 0 auto 20px auto;
      max-width: 100%;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    /* Watermark */
    .watermark-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 10;
      pointer-events: none;
      overflow: hidden;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-template-rows: repeat(4, 1fr);
      opacity: 0.15;
    }

    .watermark-item {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 800;
      color: red;
      transform: rotate(-30deg);
      white-space: nowrap;
    }

    .loading-spinner {
      color: white;
      font-size: 1.2rem;
    }
  </style>
  <script>
    // Security: Prevent Right Click & Inspect
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
      if (e.key === 'F12' || (e.ctrlKey && (e.key === 'u' || e.key === 's' || e.key === 'p'))) {
        e.preventDefault();
      }
    });
  </script>
</head>

<body>

  <div class="navbar">
    <div style="font-weight: bold; font-size: 1.2rem;">SecureView Portal</div>
    <div class="user-badge">
      <?php echo htmlspecialchars("$name ($uid)"); ?>
    </div>
    <a href="logout.php" class="btn btn-danger" style="padding: 5px 15px; font-size: 0.9rem;">Logout</a>
  </div>

  <div class="content-area">
    <?php if ($fileError): ?>
      <div class="message-box">
        <h2 style="color: var(--danger-color);">Error</h2>
        <p><?php echo $fileError; ?></p>
      </div>

    <?php elseif ($status === "waiting"): ?>
      <div class="message-box">
        <h2>Document is not yet available</h2>
        <p>Access starts in:</p>
        <div class="timer-box" id="countdown">Loading...</div>
      </div>
      <script>
        (() => {
          let diff = <?php echo $startTime - $currentTime; ?>;
          const timer = setInterval(() => {
            if (diff <= 0) location.reload();
            const h = Math.floor(diff / 3600).toString().padStart(2, '0');
            const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
            const s = (diff % 60).toString().padStart(2, '0');
            document.getElementById('countdown').innerText = `${h}:${m}:${s}`;
            diff--;
          }, 1000);
        })();
      </script>

    <?php elseif ($status === "expired"): ?>
      <div class="message-box">
        <h2 style="color: var(--secondary-color);">Session Expired</h2>
        <p>The time window for this document has ended.</p>
        <p>Thank you for your participation.</p>
      </div>

    <?php elseif ($status === "active"): ?>
      <div class="timer-box" id="countdown" style="font-size: 1.2rem;">
        Time Remaining: <span id="time-display">Loading...</span>
      </div>

      <div id="pdf-wrapper">
        <div id="pdf-loading-msg" class="loading-spinner">Loading Document...</div>
        <div class="watermark-overlay">
          <?php for ($i = 0; $i < 8; $i++): ?>
            <div class="watermark-item"><?php echo htmlspecialchars("$uid - $ip"); ?></div>
          <?php endfor; ?>
        </div>
        <!-- Canvas elements will be appended here -->
      </div>

      <script>
        (() => {
          // 1. Countdown
          let diff = <?php echo $endTime - $currentTime; ?>;
          const timer = setInterval(() => {
            if (diff <= 0) location.reload();
            const h = Math.floor(diff / 3600).toString().padStart(2, '0');
            const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
            const s = (diff % 60).toString().padStart(2, '0');
            document.getElementById('time-display').innerText = `${h}:${m}:${s}`;
            diff--;
          }, 1000);
        })();

        // 2. PDF Rendering
        console.log("Initializing PDF.js...");
        const url = "docs/<?php echo rawurlencode($file_name); ?>";
        console.log("Document URL:", url);

        const container = document.getElementById("pdf-wrapper");
        const loadingMsg = document.getElementById("pdf-loading-msg");

        pdfjsLib.getDocument(url).promise.then(pdf => {
          console.log("PDF Loaded, Payges: " + pdf.numPages);
          loadingMsg.style.display = 'none'; // Hide loading text

          for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            pdf.getPage(pageNum).then(page => {
              const viewport = page.getViewport({
                scale: 1.5
              });
              const canvas = document.createElement("canvas");
              const context = canvas.getContext("2d");
              canvas.height = viewport.height;
              canvas.width = viewport.width;

              // Insert canvas before watermark
              container.appendChild(canvas);

              page.render({
                canvasContext: context,
                viewport: viewport
              });
            });
          }
        }).catch(err => {
          console.error("PDF Load Error:", err);
          loadingMsg.innerHTML = "Error loading document: " + err.message;
          loadingMsg.style.color = "#ffdddd";
        });
      </script>
    <?php endif; ?>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>

</html>