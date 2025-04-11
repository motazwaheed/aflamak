<?php
include('alatsal.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = (int)$_POST['movie_id'];
    $comment = trim($_POST['comment']);
    
    // تنظيف المدخلات
    $comment = mysqli_real_escape_string($con, $comment);
    $username = "زائر"; // يمكن تغييرها إذا كان لديك نظام مستخدمين
    
    if(empty($comment)) {
        die(json_encode(['success' => false, 'message' => 'الرجاء إدخال تعليق']));
    }
    
    // إدراج التعليق
    $stmt = $con->prepare("INSERT INTO comments (movie_id, username, text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $movie_id, $username, $comment);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $con->error]);
    }
}