<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // جلب البيانات من النموذج
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $dob = htmlspecialchars($_POST['dob']);
    $address = htmlspecialchars($_POST['address']);

    // إنشاء سطر لتخزينه في الملف
    $data = "الاسم: $name | الهاتف: $phone | تاريخ الميلاد: $dob | العنوان: $address\n";

    // تخزين البيانات في ملف students.txt
    $file = 'students.txt';
    file_put_contents($file, $data, FILE_APPEND);

    echo "تم تسجيل الطالب بنجاح!";
} else {
    echo "طريقة غير مسموحة!";
}
?>