<?php
// مسار الملف النصي لتخزين الرسائل
$file = 'messages.txt';

// إذا كانت هناك رسالة مرسلة، نضيفها إلى الملف
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); // تأمين المدخلات
        file_put_contents($file, $message . "\n", FILE_APPEND | LOCK_EX);
    }
}

// قراءة الرسائل من الملف وعرضها
if (file_exists($file)) {
    $messages = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($messages as $msg) {
        echo '<p>' . $msg . '</p>';
    }
}
?>