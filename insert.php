<?php
include('alatsal.php');

if(isset($_POST['upload'])){
    // تنظيف المدخلات مع وجود قيم افتراضية إذا لم يتم إدخالها
    $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
    $description = mysqli_real_escape_string($con, $_POST['alwasf'] ?? '');
    $year = mysqli_real_escape_string($con, $_POST['year'] ?? '');
    $rating = isset($_POST['altkyem']) ? $_POST['altkyem'] : '';
    $director = mysqli_real_escape_string($con, $_POST['director'] ?? '');
    $actors = mysqli_real_escape_string($con, $_POST['actors'] ?? '');
    $country = mysqli_real_escape_string($con, $_POST['albld'] ?? '');
    $duration = mysqli_real_escape_string($con, $_POST['mudt_alfilm'] ?? '');
    $video_url = mysqli_real_escape_string($con, $_POST['vurl_alfilm'] ?? '');

     // تحقق من وجود تصنيفات مختارة
     if(empty($_POST['categories'])) {
        die("<script>alert('يجب اختيار تصنيف واحد على الأقل'); history.back();</script>");
    }
    // معالجة الصورة
    $image_name = $_FILES['img']['name'];
    $image_tmp = $_FILES['img']['tmp_name'];
    $image_path = "img/" . basename($image_name);
    
   if(move_uploaded_file($image_tmp, $image_path)){
        // بدء transaction لضمان سلامة البيانات
        mysqli_begin_transaction($con);
        
        try {
            // إدراج الفيلم الرئيسي
            $insert = "INSERT INTO film2 (
                        name, 
                        description, 
                        year, 
                        altkyem, 
                        director, 
                        actors, 
                        albld, 
                        mudt_alfilm, 
                        vurl_alfilm,
                        img
                      ) VALUES (
                        '$name', 
                        '$description', 
                        '$year', 
                        '$rating', 
                        '$director', 
                        '$actors', 
                        '$country', 
                        '$duration', 
                        '$video_url',
                        '$image_path'
                      )";
            
            if(!mysqli_query($con, $insert)){
                throw new Exception(mysqli_error($con));
            }
            
            $movie_id = mysqli_insert_id($con);
            
            // إدراج التصنيفات المتعددة
            foreach($_POST['categories'] as $cat_id) {
                $cat_id = (int)$cat_id;
                $insert_cat = "INSERT INTO movie_categories (movie_id, category_id) 
                              VALUES ($movie_id, $cat_id)";
                
                if(!mysqli_query($con, $insert_cat)){
                    throw new Exception(mysqli_error($con));
                }
            }
            
            // إذا نجحت جميع العمليات
            mysqli_commit($con);
            
            echo "<script>
                alert('تمت إضافة الفيلم بنجاح');
                window.location.href='film.php';
            </script>";
            
        } catch (Exception $e) {
            // التراجع عن جميع العمليات في حالة خطأ
            mysqli_rollback($con);
            
            // حذف الصورة إذا تم رفعها
            if(file_exists($image_path)) {
                unlink($image_path);
            }
            
            echo "<script>alert('حدث خطأ في إضافة الفيلم: " . addslashes($e->getMessage()) . "')</script>";
        }
        
    } else {
        echo "<script>alert('حدث خطأ في رفع الصورة')</script>";
    }
}
?>