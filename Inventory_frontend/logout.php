<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging out...</title>
  <script>
    sessionStorage.removeItem('ai_chat_logs_history');
    sessionStorage.removeItem('ai_chat_is_open');
    sessionStorage.removeItem('ai_chat_draft_text');
    window.location.href = "login.php";
  </script>
</head>
<body>
</body>
</html>

