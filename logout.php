<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // Redirect to user login by default, or admin_login.php if we want generic
exit();
