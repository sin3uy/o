<?php
// صفحة فك ضغط الملفات في الاستضافة

// تعريف المتغيرات الأساسية
$directory = '.'; // المجلد الحالي، يمكن تغييره إلى المسار المطلوب
$allowed_extensions = array('zip', 'rar', 'tar', 'gz'); // امتدادات الملفات المسموح بها

// معالجة طلب فك الضغط
if (isset($_GET['action']) && $_GET['action'] == 'extract' && isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $file_path = $directory . '/' . $file;
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    // التحقق من أن الملف موجود ومن الامتداد المسموح به
    if (file_exists($file_path) && in_array($ext, $allowed_extensions)) {
        $success = false;
        $message = '';
        
        // فك الضغط حسب نوع الملف
        if ($ext == 'zip') {
            $zip = new ZipArchive;
            if ($zip->open($file_path) === TRUE) {
                $zip->extractTo($directory);
                $zip->close();
                $success = true;
                $message = "تم فك ضغط الملف بنجاح!";
            } else {
                $message = "فشل فك ضغط الملف.";
            }
        } elseif ($ext == 'rar') {
            // يحتاج إلى تثبيت مكتبة RAR على السيرفر
            $rar_file = rar_open($file_path);
            if ($rar_file) {
                $list = rar_list($rar_file);
                foreach ($list as $entry) {
                    $entry->extract($directory);
                }
                rar_close($rar_file);
                $success = true;
                $message = "تم فك ضغط الملف بنجاح!";
            } else {
                $message = "فشل فك ضغط الملف أو المكتبة غير مثبتة.";
            }
        } elseif ($ext == 'tar' || $ext == 'gz') {
            $phar = new PharData($file_path);
            $phar->extractTo($directory);
            $success = true;
            $message = "تم فك ضغط الملف بنجاح!";
        }
        
        // عرض رسالة النتيجة
        echo '<div class="alert ' . ($success ? 'alert-success' : 'alert-danger') . '">' . $message . '</div>';
    } else {
        echo '<div class="alert alert-danger">الملف غير موجود أو غير مسموح به.</div>';
    }
}

// الحصول على قائمة الملفات في الدليل
$files = scandir($directory);
$compressed_files = array();

foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($ext, $allowed_extensions)) {
            $compressed_files[] = $file;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فك ضغط الملفات في الاستضافة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .file-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .file-name {
            font-weight: bold;
        }
        .file-size {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">فك ضغط الملفات في الاستضافة</h1>
        
        <?php if (empty($compressed_files)): ?>
            <div class="alert alert-info">لا توجد ملفات مضغوطة في هذا الدليل.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($compressed_files as $file): ?>
                    <div class="list-group-item file-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="file-name"><?php echo htmlspecialchars($file); ?></span>
                            <span class="file-size">(<?php echo formatSizeUnits(filesize($directory . '/' . $file)); ?>)</span>
                        </div>
                        <a href="?action=extract&file=<?php echo urlencode($file); ?>" class="btn btn-primary" onclick="return confirm('هل أنت متأكد من فك ضغط هذا الملف؟')">فك الضغط</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// دالة لتحويل حجم الملف إلى صيغة مقروءة
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
?>