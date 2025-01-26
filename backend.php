<?php
// البريد الإلكتروني الذي سيتم إرسال الطلبات إليه
$to = "info@asulmusaa.com"; // استبدل "your-email@example.com" ببريدك

// عنوان البريد
$subject = "طلب حجز جديد";

// التحقق من طلب POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات من النموذج
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $unitType = htmlspecialchars(trim($_POST['unitType']));
    $notes = htmlspecialchars(trim($_POST['notes']));

    // التحقق من صحة البيانات
    $errors = [];

    if (empty($fullName) || mb_strlen($fullName) < 6) {
        $errors[] = "يرجى إدخال الاسم الكامل بشكل صحيح (6 أحرف على الأقل).";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "يرجى إدخال بريد إلكتروني صحيح.";
    }
    if (empty($phone) || !preg_match('/^(05)[0-9]{8}$/', $phone)) {
        $errors[] = "يرجى إدخال رقم جوال صحيح يبدأ بـ 05.";
    }
    if (empty($unitType)) {
        $errors[] = "يرجى اختيار نوع الوحدة.";
    }

    // إذا كانت هناك أخطاء، إرسال الرد
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // محتوى البريد
    $message = "
        <h3>تفاصيل الطلب:</h3>
        <p><strong>الاسم الكامل:</strong> $fullName</p>
        <p><strong>البريد الإلكتروني:</strong> $email</p>
        <p><strong>رقم الجوال:</strong> $phone</p>
        <p><strong>نوع الوحدة:</strong> $unitType</p>
        <p><strong>ملاحظات:</strong> $notes</p>
    ";

    // إعداد رؤوس البريد
    $headers = "From: noreply@asulmusaa.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // إرسال البريد
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => 'تم إرسال طلب الحجز بنجاح!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في إرسال الطلب. حاول مرة أخرى لاحقًا.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طلب غير صالح.']);
}
?>
