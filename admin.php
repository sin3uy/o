<?php
// تحديد ملف البيانات
$file = 'students.txt';

// التحقق من وجود الملف
if (!file_exists($file)) {
    die("لا توجد بيانات مسجلة بعد.");
}

// قراءة البيانات من الملف
$data = file_get_contents($file);

// تقسيم البيانات إلى سطور
$students = explode("\n", trim($data));
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض بيانات الطلاب</title>
    <style>
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>بيانات الطلاب</h2>
    <table>
        <tr>
            <th>#</th>
            <th>الاسم الكامل</th>
            <th>رقم الهاتف</th>
            <th>تاريخ الميلاد</th>
            <th>العنوان</th>
        </tr>
        <?php
        // عرض بيانات كل طالب
        $count = 1;
        foreach ($students as $student) {
            if (trim($student) === "") continue; // تخطي السطور الفارغة
            // تقسيم السطر إلى أعمدة بناءً على الفاصل " | "
            $columns = explode('|', $student);

            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            foreach ($columns as $column) {
                echo "<td>" . htmlspecialchars(trim(explode(':', $column)[1])) . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>