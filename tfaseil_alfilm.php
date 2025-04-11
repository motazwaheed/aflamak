<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('alatsal.php');

// تحقق من وجود المعلمة id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("خطأ: معرّف الفيلم غير صالح أو غير موجود. الرابط يجب أن يكون مثل: tfaseil_alfilm.php?id=1");
}

$movie_id = (int)$_GET['id'];
$query = "SELECT * FROM film2 WHERE id = $movie_id LIMIT 1";
$result = mysqli_query($con, $query);

if (!$result) {
    die("خطأ في الاستعلام: " . mysqli_error($con));
}

if (mysqli_num_rows($result) == 0) {
    die("خطأ: لا يوجد فيلم بهذا المعرّف في قاعدة البيانات.");
}

$movie = mysqli_fetch_assoc($result);
?>




<?php
// 1. تضمين ملف الاتصال بقاعدة البيانات
include('alatsal.php');

// 2. التحقق من وجود معرّف الفيلم (ID) في الرابط
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: film.php"); // إعادة التوجيه إذا لم يكن هناك ID
    exit();
}

// 3. تنظيف المعلمة لمنع الثغرات الأمنية
$movie_id = (int)$_GET['id'];

// 4. استعلام SQL لجلب بيانات الفيلم
$query = "SELECT * FROM film2 WHERE id = $movie_id LIMIT 1";
$result = mysqli_query($con, $query);

// 5. التحقق من وجود نتائج
if(mysqli_num_rows($result) == 0) {
    header("Location: film.php"); // إعادة توجيه إذا لم يوجد الفيلم
    exit();
}

// 6. تحويل النتيجة إلى مصفوفة رابطية
$movie = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['name']) ?></title>
    <!-- روابط خارجية -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"> <!-- أيقونات جوجل -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet"> <!-- خط عربي -->
    <script>
// لحفظ الفيلم في القائمة
function addToWatchlist() {
    // هنا يمكنك إضافة كود AJAX لحفظ البيانات في قاعدة البيانات
    Swal.fire({
        icon: 'success',
        title: 'تم الحفظ!',
        text: 'تمت إضافة الفيلم إلى قائمتك',
        confirmButtonText: 'حسناً',
        confirmButtonColor: '#FFB100'
    });
}

// للمشاركة
function shareMovie() {
    if (navigator.share) {
        navigator.share({
            title: '<?= htmlspecialchars($movie['name']) ?>',
            text: 'شاهد هذا الفيلم الرائع',
            url: window.location.href
        }).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'تمت المشاركة!',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#FFB100'
            });
        });
    } else {
        // Fallback لنظام المشاركة التقليدي
        const shareUrl = `https://wa.me/?text=شاهد فيلم <?= urlencode($movie['name']) ?> على ${encodeURIComponent(window.location.href)}`;
        window.open(shareUrl, '_blank');
    }
}
</script>

<!-- أضف هذه المكتبات في <head> -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* المتغيرات العامة للألوان */
        :root {
            --primary: #FFB100; /* اللون الذهبي الرئيسي */
            --dark: #1a1a1a; /* الخلفية الداكنة */
            --secondary: #222831; /* الخلفية الثانوية */
            --text: #ffffff; /* لون النص الأبيض */
            --radius: 10px; /* زوايا دائرية */
           
            
        }
        
        /* إعدادات عامة للصفحة */
        body {
            font-family: 'Tajawal', sans-serif; /* استخدام الخط العربي */
            background-color: var(--dark); /* لون الخلفية */
            color: var(--text); /* لون النص */
            margin: 0; /* إزالة الهوامش */
            padding: 0; /* إزالة الحشو */
            line-height: 1.6; /* تباعد الأسطر */
        }
        
        /* حاوية الفيلم الرئيسية */
        .movie-container {
            max-width: 1200px; /* أقصى عرض للحاوية */
            /*margin: 0 auto; /* توسيط الحاوية */
            padding: 20px; /* حشو داخلي */
        }
        .breadcrumbs {
    margin-bottom: 20px;
    padding: 10px 0;
    font-size: 1rem;
    font-weight: 600;
    padding-right: 5px;
    color: rgba(255,255,255,0.7);
    direction: rtl; /* للتأكد من اتجاه النص من اليمين */
    /*background-color: #FFB100;*/
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
}

.breadcrumbs a {
    color: var(--primary);
    text-decoration: none;
    transition: all 0.3s;
}

.breadcrumbs a:hover {
    text-decoration: underline;
    color: #ffc107; /* لون ذهبي فاتح عند التمرير */
}

.breadcrumbs span.separator {
    margin: 0 8px;
    color: rgba(255,255,255,0.5);
}
        /* رأس صفحة الفيلم (البوستر والمعلومات) */
        .movie-header {
            display: flex; /* استخدام Flexbox للترتيب */
            flex-wrap: wrap; /* السماح بلف العناصر على شاشات صغيرة */
            gap: 30px; /* المسافة بين العناصر */
            margin-bottom: 30px; /* هامش سفلي */
        }
        
        /* قسم بوستر الفيلم */
        .movie-poster {
            flex: 1; /* مرونة في الحجم */
            min-width: 300px; /* أقل عرض مسموح */
            border-radius: var(--radius); /* زوايا دائرية */
            overflow: hidden; /* إخفاء ما يتجاوز الحاوية */
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); /* ظل خفيف */
        }
        
        .movie-poster img {
            width: 100%; /* عرض كامل للحاوية */
            height: auto; /* ارتفاع تلقائي */
            display: block; /* تجنب الفراغات تحت الصورة */
        }
        
        /* قسم معلومات الفيلم */
        .movie-info {
            flex: 2; /* يأخذ ضعف مساحة البوستر */
            min-width: 300px; /* أقل عرض مسموح */
        }
        
        /* عنوان الفيلم */
        .movie-title {
            font-size: 2.2rem; /* حجم كبير للنص */
            margin-bottom: 10px; /* هامش سفلي */
            color: var(--primary); /* لون ذهبي */
            font-weight: 700; /* سمك الخط */
        }
        
        /* معلومات الميتا (التقييم، السنة، المدة) */
        .movie-meta {
            display: block;
            /*display: flex; /* ترتيب أفقي */
            flex-wrap: wrap; /* السماح باللف */
            gap: 20px; /* مسافة بين العناصر */
            margin-bottom: 20px; /* هامش سفلي */
        }
        
        /* كل عنصر ميتا (مثال: التقييم) */
        .meta-item {
            display: flex; /* ترتيب أفقي */
            align-items: center; /* محاذاة عمودية */
            gap: 2.5rem; /* مسافة بين الأيقونة والنص */
        }
        
        /* التقييم */
        .rating {
            gap: 2.5rem;
            color: gold; /* لون النجوم */
            font-weight: bold; /* نص سميك */
        }
        
        /* وصف الفيلم */
        .movie-description {
            line-height: 1.6; /* تباعد الأسطر */
            margin-bottom: 25px; /* هامش سفلي */
            font-size: 1.1rem; /* حجم النص */
        }
        
        /* تفاصيل الفيلم (المخرج، الممثلين...) */
        .movie-details {
            display: grid; /* استخدام Grid للترتيب */
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* أعمدة متجاوبة */
            gap: 15px; /* مسافة بين العناصر */
            margin-bottom: 30px; /* هامش سفلي */
        }
        
        /* كل عنصر تفصيل */
        .detail-item {
            background: rgba(255,255,255,0.05); /* خلفية شبه شفافة */
            padding: 10px; /* حشو داخلي */
            border-radius: var(--radius); /* زوايا دائرية */
        }
        
        /* عنوان العنصر (مثال: "المخرج") */
        .detail-label {
            color: var(--primary); /* لون ذهبي */
            font-size: 0.9rem; /* حجم صغير */
            margin-bottom: 5px; /* هامش سفلي */
        }
        .div_almushahdh{
            width: 100%;
            height: 5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            background-color: #FFB100;
            /*border-radius:1rem 1rem 0 0;*/

        }
        .div_almushahdh i{
            font-size: 2rem;
            position: relative;
            top: 0.3rem;
        }
        /* قسم مشغل الفيديو */
        .player-section {
            margin-bottom: 1px; /* هامش سفلي كبير */
           /* border: 1px solid #FFB100;*/
            /*border-radius: 1rem;*/
        }
        
        /* حاوية الفيديو */
        .video-container {
            position: relative; /* لتحديد موقع العناصر الداخلية */
            /*padding-bottom: 56.25%; /* نسبة 16:9 للمشغل */
           /* height: 0; /* ارتفاع صفر مع الاعتماد على padding */
            overflow: hidden; /* إخفاء ما يتجاوز الحاوية */
            border-radius: var(--radius); /* زوايا دائرية */
            background: #000; /* خلفية سوداء */
           /* border-radius: 0 0 1rem 1rem;*/
        }
        .video-container video {
            width: 100%;
            max-height: 400px; /* أو أي ارتفاع يناسبك */
            border: none;
            display: block;
            object-fit: cover; /* عشان يقطع الصورة الزائدة بدل ما يمدها */
            border-radius: 0 0 1rem 1rem;
        }

        /* إطار الفيديو */

        
        /* قسم التحميل */
        .download-section {
           /* background: rgba(0,0,0,0.3); /* خلفية شبه شفافة */
            padding: 0px; /* حشو داخلي */
            margin-bottom: 0px; /* هامش سفلي */
           /* border: 1px solid #FFB100;
            border-radius: 1rem;*/
            
        }
        .url_download{
            width: 100%;
            max-height: 5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            /*background-color: #FFB100;
            border-radius:1rem 1rem 0 0;*/
        }
        .url_download i{
            font-size: 2rem;
            position: relative;
            top: 0.3rem;
            color: white;
        }
        /* عنوان قسم التحميل */
      
        
        /* خيارات الجودة */
        .quality-options {
            display: flex; /* ترتيب أفقي */
            flex-wrap: wrap; /* السماح باللف */
            gap: 10px; /* مسافة بين الأزرار */
            justify-content: space-between; /* توزيع المسافة بينهم */
        }
        
        /* أزرار التحميل */
        .quality-btn {
            flex: 0 0 48%;
            box-sizing: border-box;
            background: var(--secondary); /* لون الخلفية */
            color: white; /* لون النص */
            border: none; /* إزالة الحدود */
            padding: 10px 15px; /* حشو داخلي */
            margin-right: 10px;
            border-radius: var(--radius); /* زوايا دائرية */
            display: flex; /* ترتيب أفقي */
            align-items: center; /* محاذاة عمودية */
            justify-content: center;
            gap: 8px; /* مسافة بين الأيقونة والنص */
            cursor: pointer; /* مؤشر يد */
            transition: all 0.3s; /* تأثير حركي */
            font-family: 'Tajawal', sans-serif; /* خط عربي */
        }
        
        /* تأثير عند تمرير المؤشر */
        .quality-btn:hover {
            background: var(--primary); /* لون ذهبي */
            color: black; /* لون نص أسود */
        }
        /* أنماط جديدة */
.poster-wrapper {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    transition: transform 0.3s;
}

.poster-wrapper:hover {
    transform: scale(1.03);
}

.poster-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 30%);
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 15px;
}

.quality-badge {
    background: var(--primary);
    color: #000;
    padding: 4px 10px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.8rem;
}

.rating-badge {
    background: rgba(0,0,0,0.7);
    color: gold;
    padding: 4px 10px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: bold;
}

.movie-meta-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn-watchlist, .btn-share {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-watchlist:hover {
    background: var(--primary);
    color: #000;
}

.btn-share:hover {
    background: #4267B2;
    border-color: #4267B2;
}

.meta-bar {
    display: block;
    gap: 20px;
    margin-bottom: 25px;
    padding-bottom: 0px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    color: rgba(255,255,255,0.8);
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 15px;
    margin-top: 20px;
}

.meta-item i {
    color: var(--primary);
    width: 40px;
    height: 40px;
    background: rgba(255,177,0,0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.rating-section {
    background: rgba(255,255,255,0.05);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rating-stars {
    color: gold;
    display: flex;
    gap: 2px;
}

.rating-value {
    font-weight: bold;
    font-size: 1.1rem;
}

.rating-value span {
    color: var(--primary);
    font-size: 1.3rem;
}

.btn-rate {
    background: rgba(255,177,0,0.2);
    color: var(--primary);
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-rate:hover {
    background: var(--primary);
    color: #000;
}

.movie-description-box {
    margin-bottom: 30px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 5px;

    
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--primary);
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.description-content {
    line-height: 1.8;
    color: rgba(255,255,255,0.9);
    font-size: 1.05rem;
}

.movie-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.detail-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 15px;
    display: flex;
    gap: 15px;
    transition: all 0.3s;
}

.detail-card:hover {
    background: rgba(255,177,0,0.05);
    border-color: rgba(255,177,0,0.3);
}

.detail-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,177,0,0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.detail-content h4 {
    margin: 0 0 5px 0;
    color: var(--primary);
    font-size: 0.95rem;
}

.detail-content p {
    margin: 0;
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
}

/* تأثيرات للجوائز إذا وجدت */
.awards-badge {
    background: linear-gradient(135deg, #f5af19, #f12711);
    color: white;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 0.8rem;
    margin-right: 10px;
    display: inline-block;
}

/* للهواتف */
@media (max-width: 768px) {
    .movie-meta-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .action-buttons {
        width: 100%;
        justify-content: space-between;
    }
    
    .movie-details-grid {
        grid-template-columns: 1fr;
    }
}
        @media (min-width: 1024px) {
            /* حاوية الفيلم الرئيسية */
            .movie-container {
                max-width: 100%; /* أقصى عرض للحاوية */
                margin: 0 auto; /* توسيط الحاوية */
                padding: 20px; /* حشو داخلي */
            }
            .div_almushahdh{
                width: 100%;
                height: 5rem;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 1.5rem;
                background-color: #FFB100;
            }
            .div_almushahdh i{
                font-size: 2.3rem;
            }
            /* حاوية الفيديو */
            .video-container {
                position: relative; /* لتحديد موقع العناصر الداخلية */
                /*padding-bottom: 56.25%; /* نسبة 16:9 للمشغل */
            /* height: 0; /* ارتفاع صفر مع الاعتماد على padding */
                overflow: hidden; /* إخفاء ما يتجاوز الحاوية */
                border-radius: var(--radius); /* زوايا دائرية */
                background: #000; /* خلفية سوداء */
            }
            .video-container video {
                width: 100%;
                max-height: 50rem; /* أو أي ارتفاع يناسبك */
                border: none;
                display: block;
                object-fit: cover; /* عشان يقطع الصورة الزائدة بدل ما يمدها */
               /* border-radius: 0 0 1rem 1rem;*/
            }
            .url_download{
            width: 100%;
            max-height: 5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            background-color: #FFB100;
           /* border-radius:1rem 1rem 0 0;*/
        }
        .url_download i{
            font-size: 2rem;
            position: relative;
            top: 0.3rem;
            color: white;
        }
        .quality-btn {
            flex: 0 0 45%;
            margin: 10px;
            box-sizing: border-box;
            padding: 20px 15px; /* حشو داخلي */
        }
            }
            /* ============= إستعلامات الوسائط للشاشات الصغيرة ============= */
            @media (max-width: 768px) {
            /* تعديلات للهواتف والأجهزة اللوحية */
            .movie-title {
                font-size: 1.8rem; /* تصغير حجم العنوان */
            }
            
            .movie-details {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* أعمدة أصغر */
            }
            
            .movie-poster, .movie-info {
                min-width: 100%; /* عرض كامل على الهواتف */
            }
            
            .download-title {
                font-size: 1.1rem; /* تصغير حجم النص */
            }
            
            .quality-btn {
                padding: 15px 1px; /* أزرار أصغر */
                margin: 8px;
                font-size: 0.9rem; /* نص أصغر */
                flex: 0 0 43%;
                box-sizing: border-box;
            }
            .player-section {
                margin-bottom: 0px; /* هامش سفلي كبير */
                border: 1px solid #FFB100;
                /*border-radius: 1rem;*/
            }
            .div_almushahdh{
                width: 100%;
                height: 3.5rem;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 1.2rem;
                background-color: #FFB100;
            }
            .div_almushahdh i{
                font-size: 1.8rem;
            }
            .video-container video {
                width: 100%;
                max-height: 200px; /* أو أي ارتفاع يناسبك */
                border: none;
                display: block;
                object-fit: cover; /* عشان يقطع الصورة الزائدة بدل ما يمدها */
              /*  border-radius: 0 0 1rem 1rem;*/
            }
            .url_download{
            width: 100%;
            max-height: 3.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2rem;
            background-color: #FFB100;
            /*border-radius:1rem 1rem 0 0;*/
        }
        .url_download i{
            font-size: 1.8rem;
            position: relative;
            top: 0.5rem;
            left: 0.5rem;
            color: white;
        }
        }
        
        /* للشاشات الصغيرة جدًا (أقل من 480px) */
        @media (max-width: 480px) {
            .movie-container {
                padding: 10px; /* حشو أقل */
            }
            
            .movie-title {
                font-size: 1.5rem; /* عنوان أصغر */
            }
            
            .movie-description {
                font-size: 1rem; /* نص أصغر */
            }
            
            .meta-item {
                font-size: 0.9rem; /* نص معلومات أصغر */
            }
        }
        /* تعديلات قسم التعليقات */
.comments-section {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
}

@media (max-width: 768px) {
    .comments-section {
        max-width: 95%;
        margin: 30px auto;
        padding: 15px;
    }
    
    .comments-section h3 {
        font-size: 1.3rem;
    }
    
    .comment-form textarea {
        min-height: 80px;
    }
}
.div_altwasl{
    width: 100%;
    height: 3rem;
    background: none;
    display: flex;
    justify-content: center;
    align-items: center;

}
.div_altwasl button{
    background: none;
    border: none;
    text-align: center;
}
.div_altwasl .icon{
    width: 1.5rem;
    height: 1.7rem;
}
.div_Aflamak{
    width: 100%;
    height: 1rem;
}
.div_Aflamak p{
    color: rgb(100, 100, 100);
    font-size: 0.8rem;
    display: flex;
    justify-content: center;
    align-items: center;
}
    </style>
</head>
<body>

<div class="breadcrumbs">
    <a href="index.php"><i class="material-icons" style="font-size: 1rem; vertical-align: middle;">home</i> الرئيسية</a>
    <i class="material-icons" style="font-size: 1rem; vertical-align: middle; margin: 0 5px; color: var(--primary);">chevron_left</i>
    <a href="film.php"><i class="material-icons" style="font-size: 1rem; vertical-align: middle;">movie</i> الأفلام</a>
    <i class="material-icons" style="font-size: 1rem; vertical-align: middle; margin: 0 5px; color: var(--primary);">chevron_left</i>
    <span class="current"><?= htmlspecialchars($movie['name']) ?></span>
</div>
    <div class="movie-container">
    <!-- رأس الصفحة المعدل -->
    <div class="movie-header">
        <!-- بوستر الفيلم (تم التعديل) -->
        <div class="movie-poster">
            <div class="poster-wrapper">
                <img src="<?= htmlspecialchars($movie['img']) ?>" 
                     alt="<?= htmlspecialchars($movie['name']) ?>"
                     onerror="this.src='img/default_poster.jpg'">
                <div class="poster-overlay">
                    <span class="quality-badge">FHD</span>
                    <?php if(!empty($movie['altkyem'])): ?>
                    <div class="rating-badge">
                        <i class="material-icons">star</i>
                        <span><?= number_format($movie['altkyem'], 1) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- قسم المعلومات المعدل -->
        <div class="movie-info">
            <div class="movie-meta-header">
                <h1 class="movie-title"><?= htmlspecialchars($movie['name']) ?></h1>
                <div class="action-buttons">
                    <button class="btn-watchlist">
                        <i class="material-icons">bookmark_add</i>
                        <span>حفظ</span>
                    </button>
                    <button class="btn-share">
                        <i class="material-icons">share</i>
                        <span>مشاركة</span>
                    </button>
                </div>
            </div>
            
            <!-- شريط المعلومات المعدل -->
            <div class="meta-bar">
                <?php if(!empty($movie['year'])): ?>
                <div class="meta-item">
                    <i class="material-icons">calendar_today</i>
                    <span><?= $movie['year'] ?></span>
                </div>
                <?php endif; ?>
                
                <div class="meta-item">
                    <i class="material-icons">schedule</i>
                    <span><?= $movie['mudt_alfilm'] ?? '2h 15m' ?></span>
                </div>
                
                <?php if(!empty($movie['category'])): ?>
                <div class="meta-item">
                    <i class="material-icons">category</i>
                    <span><?= htmlspecialchars($movie['category']) ?></span>
                </div>
                <?php endif; ?>
                
                <div class="meta-item">
                    <i class="material-icons">language</i>
                    <span><?= $movie['albld'] ?? 'غير محدد' ?></span>
                </div>
            </div>
            <div class="movie-details-grid">
        <?php if(!empty($movie['director'])): ?>
        <div class="detail-card">
            <div class="detail-icon">
                <i class="material-icons">movie_creation</i>
            </div>
            <div class="detail-content">
                <h4>المخرج</h4>
                <p><?= htmlspecialchars($movie['director']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($movie['actors'])): ?>
        <div class="detail-card">
            <div class="detail-icon">
                <i class="material-icons">group</i>
            </div>
            <div class="detail-content">
                <h4>التمثيل</h4>
                <p><?= htmlspecialchars($movie['actors']) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="detail-card">
            <div class="detail-icon">
                <i class="material-icons">high_quality</i>
            </div>
            <div class="detail-content">
                <h4>الجودة</h4>
                <p>FHD 1080p</p>
            </div>
        </div>
        
        <div class="detail-card">
            <div class="detail-icon">
                <i class="material-icons">translate</i>
            </div>
            <div class="detail-content">
                <h4>اللغة</h4>
                <p>العربية (مدبلج)</p>
            </div>
        </div>
    </div><br>
            <!-- قسم التقييم التفاعلي المعدل -->
            <div class="rating-section">
                <div class="rating-display">
                    <div class="rating-stars">
                        <?php
                        $rating = isset($movie['altkyem']) ? $movie['altkyem'] : 0;
                        $stars = round(($rating / 10) * 5); // تحويل من 10 إلى 5 نجوم
                        for($i=1; $i<=5; $i++): ?>
                            <i class="material-icons"><?= $i <= $stars ? 'star' : 'star_border' ?></i>
                        <?php endfor; ?>
                    </div>
                    <div class="rating-value">
                        <span><?= number_format($rating, 1) ?></span>/10
                    </div>
                </div>
                <button class="btn-rate" onclick="showRatingModal()">
                    <i class="material-icons">star_rate</i>
                    <span>قيم الفيلم</span>
                </button>
            </div>
            
            <!-- وصف الفيلم المعدل -->
            <div class="movie-description-box">
                <h3 class="section-title">
                    <i class="material-icons">description</i>
                    <span>القصة</span>
                </h3>
                <div class="description-content">
                    <p><?= nl2br(htmlspecialchars($movie['description'] ?? 'لا يوجد وصف متاح')) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- تفاصيل الفيلم المعدلة -->
    
</div>
        <!-- قسم المشاهدة -->
        <?php if(!empty($movie['vurl_alfilm'])): ?>
           
        <div class="player-section">
            <div class="div_almushahdh">
                <h2><i class="material-icons icon">play_circle_filled</i> مشاهدة الفيلم</h2>
            </div>
            <div class="video-container">
                <video controls width="100%" poster="<?= htmlspecialchars($movie['img']) ?>">
                    <source src="<?= htmlspecialchars($movie['vurl_alfilm']) ?>" type="video/mp4">
                    متصفحك لا يدعم تشغيل الفيديو.
                </video>
            </div>
        </div>
        <?php endif; ?>
        <br><br>
        <!-- قسم التحميل -->
        <div class="download-section">
            <div class="url_download">
                <h2 class="download-title"><i class="material-icons">cloud_download</i> روابط التحميل</h2>
            </div><br>
            <div class="quality-options">
                <button class="quality-btn">
                    <i class="material-icons">hd</i>
                    <span>1080p</span>
                    <span>(2.4GB)</span>
                </button>
                
                <button class="quality-btn">
                    <i class="material-icons">hd</i>
                    <span>720p</span>
                    <span>(1.5GB)</span>
                </button>
                
                <button class="quality-btn">
                    <i class="material-icons">sd</i>
                    <span>480p</span>
                    <span>(800MB)</span>
                </button>
                
                <button class="quality-btn">
                    <i class="material-icons">smartphone</i>
                    <span>360p</span>
                    <span>(400MB)</span>
                </button>
                
            </div><br>
        </div>
 <!-- قسم التعليقات -->
<div class="comments-section" style="margin: 40px auto; padding: 7px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid rgba(255,177,0,0.2); max-width: 800px;">
    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; justify-content: center; background-color:#FFB100;">
        <i class="material-icons">comment</i>
        التعليقات
    </h3>
    
    <!-- نموذج الإضافة -->
    <form id="comment-form" style="margin-bottom: 30px;">
        <textarea 
            name="comment" 
            placeholder="أضف تعليقك..." 
            required
            style="width: 100%; padding: 0px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,177,0,0.3); border-radius: 8px; color: white; margin-bottom: 10px; font-family: 'Tajawal'; min-height: 100px;"
        ></textarea>
        <button 
            type="submit" 
            style="background: var(--primary); color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-family: 'Tajawal'; font-weight: bold; display: flex; align-items: center; gap: 8px; margin: 0 auto;"
        >
            <i class="material-icons">send</i>
            نشر التعليق
        </button>
    </form>
    
    <!-- قائمة التعليقات -->
    <div class="comments-list">
        <?php
        $comments_query = "SELECT * FROM comments WHERE movie_id = $movie_id ORDER BY created_at DESC";
        $comments_result = mysqli_query($con, $comments_query);
        
        if(mysqli_num_rows($comments_result) > 0): 
            while($comment = mysqli_fetch_assoc($comments_result)): 
        ?>
            <div style="border-bottom: 1px solid rgba(255,177,0,0.1); padding: 15px 0;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <div style="width: 45px; height: 45px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: black; font-weight: bold; font-size: 1.2rem;">
                        <?= substr($comment['username'] ?? 'زائر', 0, 1) ?>
                    </div>
                    <div style="flex: 1;">
                        <strong style="display: block; color: var(--primary);"><?= htmlspecialchars($comment['username'] ?? 'زائر') ?></strong>
                        <small style="color: rgba(255,255,255,0.5); font-size: 0.8rem;"><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></small>
                    </div>
                </div>
                <p style="margin: 0 0 0 60px; color: rgba(255,255,255,0.9); line-height: 1.7; word-break: break-word;"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
            </div>
        <?php 
            endwhile;
        else: 
        ?>
            <p style="text-align: center; color: rgba(255,255,255,0.5); padding: 20px;">لا توجد تعليقات بعد. كن أول من يعلق!</p>
        <?php endif; ?>
    </div>
</div>
        <!-- ========== نهاية نظام التعليقات ========== -->
    </div>
    
    <!-- مودال التقييم -->
<div id="ratingModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeRatingModal()">&times;</span>
        <h3>قيم هذا الفيلم</h3>
        <div class="modal-rating-stars">
            <?php for($i=1; $i<=5; $i++): ?>
                <i class="material-icons" onclick="setRating(<?= $i ?>)">star_border</i>
            <?php endfor; ?>
        </div>
        <div class="rating-feedback">
            <span id="ratingText">اضغط على النجوم للتقييم</span>
        </div>
        <button class="submit-rating" onclick="submitRating()">تأكيد التقييم</button>
    </div>
</div>

<script>
let currentSelectedRating = 0;

function showRatingModal() {
    document.getElementById('ratingModal').style.display = 'flex';
}

function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
}

function setRating(rating) {
    currentSelectedRating = rating;
    const stars = document.querySelectorAll('.modal-rating-stars i');
    const ratingTexts = [
        'سيء جداً',
        'سيء',
        'متوسط',
        'جيد',
        'ممتاز'
    ];
    
    stars.forEach((star, index) => {
        star.textContent = index < rating ? 'star' : 'star_border';
    });
    
    document.getElementById('ratingText').textContent = ratingTexts[rating-1];
}

function submitRating() {
    if(currentSelectedRating === 0) {
        alert('الرجاء اختيار تقييم');
        return;
    }
    
    fetch(`rate_movie.php?id=<?= $movie['id'] ?>&rating=${currentSelectedRating * 2}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('شكراً لتقييمك!');
                location.reload();
            }
        });
}

// إغلاق المودال عند النقر خارج المحتوى
window.onclick = function(event) {
    const modal = document.getElementById('ratingModal');
    if(event.target == modal) {
        closeRatingModal();
    }
}
</script>
<br><br>
<div class="div_altwasl">
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284171" alt="" class="icon">
        </a>
    </button>
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284168" alt="" class="icon">
        </a>
    </button>
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284170" alt="" class="icon">
        </a>
    </button>
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284159" alt="" class="icon">
        </a>
    </button>
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284161" alt="" class="icon">
        </a>
    </button>
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284175" alt="" class="icon">
        </a>
    </button>
    <button>
        <a href="">
            <img src="https://www.raed.net/img?id=1284177" alt="" class="icon">
        </a>
    </button>
</div>
<div class="div_Aflamak">
    <p>جـمـيـع الـحقـوق محـفـوضـة لـ شـبـكـة افـلامـك   &copy;  2025</p>
</div>
<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: var(--secondary);
    padding: 25px;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    position: relative;
    animation: modalFadeIn 0.3s;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.close-modal {
    position: absolute;
    top: 15px;
    left: 15px;
    font-size: 24px;
    cursor: pointer;
    color: rgba(255,255,255,0.6);
    transition: all 0.3s;
}

.close-modal:hover {
    color: var(--primary);
}

.modal h3 {
    text-align: center;
    color: var(--primary);
    margin-bottom: 20px;
}

.modal-rating-stars {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-bottom: 20px;
}

.modal-rating-stars i {
    font-size: 36px;
    color: #666;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-rating-stars i:hover {
    color: gold;
    transform: scale(1.1);
}

.rating-feedback {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.1rem;
    color: rgba(255,255,255,0.8);
}

.submit-rating {
    background: var(--primary);
    color: #000;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    display: block;
    width: 100%;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.submit-rating:hover {
    background: #ffc107;
    transform: translateY(-2px);
}
</style>
<script>
// معالجة إرسال التعليق
document.getElementById('comment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('movie_id', <?= $movie_id ?>);
    
    fetch('add_comment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'تم نشر تعليقك!',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#FFB100'
            }).then(() => {
                location.reload(); // إعادة تحميل الصفحة لعرض التعليق الجديد
            });
        }
    });
});
</script>
</body>
</html>