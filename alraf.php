<?php
session_start();
require 'alatsal.php'; // تأكد من أن هذا الملف آمن ولا يحتوي على ثغرات

// توليد CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// التحقق من صلاحيات المستخدم (يجب تطبيق نظام مصادقة فعلي)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// جلب التصنيفات مرة واحدة فقط
$categories = [];
$cats_query = mysqli_query($con, "SELECT id, name FROM categories ORDER BY name");
while ($cat = mysqli_fetch_assoc($cats_query)) {
    $categories[] = $cat;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام رفع الأفلام</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #FFB100;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --error-color: #e63946;
        }
        
        * {
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: var(--dark-color);
        }
        
        .upload-container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .file-upload {
            border: 2px dashed var(--accent-color);
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #fffaf0;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-upload:hover {
            background-color: #fff8e6;
            border-color: var(--primary-color);
        }
        
        .upload-label {
            display: block;
            font-size: 18px;
            color: var(--primary-color);
            cursor: pointer;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 300px;
            margin-top: 15px;
            display: none;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .submit-btn:hover {
            background-color: var(--secondary-color);
        }
        
        .error-message {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .select2-container--default .select2-selection--multiple {
            padding: 6px;
            min-height: 42px;
        }
        
        @media (max-width: 768px) {
            .upload-container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h1><i class="fas fa-film"></i> رفع فيلم جديد</h1>
        
        <form id="uploadForm" action="insert.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label for="name"><i class="fas fa-film"></i> اسم الفيلم</label>
                <input type="text" id="name" name="name" required minlength="2" maxlength="255" 
                       placeholder="أدخل اسم الفيلم بالكامل">
                <div class="error-message" id="name-error">يجب أن يكون اسم الفيلم بين 2 و255 حرفًا</div>
            </div>
            
            <div class="form-group">
                <label for="alwasf"><i class="fas fa-align-left"></i> وصف الفيلم</label>
                <textarea id="alwasf" name="alwasf" rows="5" 
                          placeholder="أدخل وصفًا مفصلًا للفيلم"></textarea>
            </div>
            
            <div class="form-group">
                <label for="snt_alentag"><i class="fas fa-calendar-alt"></i> سنة الإنتاج</label>
                <input type="number" id="snt_alentag" name="snt_alentag" min="1900" max="<?= date('Y') ?>" 
                       placeholder="أدخل سنة الإنتاج">
            </div>
            
            <div class="form-group">
                <label for="altkyem"><i class="fas fa-star"></i> التقييم</label>
                <input type="number" id="altkyem" name="altkyem" min="0" max="10" step="0.1" 
                       placeholder="أدخل التقييم من 0 إلى 10">
            </div>
            
            <div class="form-group">
                <label for="almohrg"><i class="fas fa-user-tie"></i> المخرج</label>
                <input type="text" id="almohrg" name="almohrg" maxlength="100" 
                       placeholder="أدخل اسم المخرج">
            </div>
            
            <div class="form-group">
                <label for="name_almumthl"><i class="fas fa-users"></i> الممثلين</label>
                <input type="text" id="name_almumthl" name="name_almumthl" 
                       placeholder="أدخل أسماء الممثلين مفصولة بفواصل">
            </div>
            
            <div class="form-group">
                <label for="albld"><i class="fas fa-globe"></i> البلد</label>
                <input type="text" id="albld" name="albld" maxlength="50" 
                       placeholder="أدخل بلد الإنتاج">
            </div>
            
            <div class="form-group">
                <label for="mudt_alfilm"><i class="fas fa-clock"></i> مدة الفيلم</label>
                <input type="text" id="mudt_alfilm" name="mudt_alfilm" 
                       placeholder="مثال: 2h 15m">
            </div>
            
            <div class="form-group">
                <label for="vurl_alfilm"><i class="fas fa-link"></i> رابط الفيديو</label>
                <input type="url" id="vurl_alfilm" name="vurl_alfilm" 
                       placeholder="أدخل رابط الفيديو (يوتيوب أو سيرفر خارجي)">
            </div>
            
            <div class="form-group">
                <label for="categories"><i class="fas fa-tags"></i> التصنيفات</label>
                <select id="categories" name="categories[]" multiple="multiple" class="form-control" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="file-upload" id="uploadArea">
                <label for="img" class="upload-label">
                    <i class="fas fa-cloud-upload-alt"></i> اختر صورة الفيلم
                    <br>
                    <span style="font-size: 14px; color: #666;">(الحد الأقصى لحجم الصورة: 5MB)</span>
                </label>
                <input type="file" id="img" name="img" accept="image/jpeg, image/png, image/webp" required>
                <img id="preview" class="preview-image" alt="معاينة الصورة">
                <div class="error-message" id="file-error"></div>
            </div>
            
            <button type="submit" name="upload" class="submit-btn">
                <i class="fas fa-upload"></i> رفع الفيلم
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // تهيئة Select2 للتصنيفات
        $('#categories').select2({
            placeholder: "اختر تصنيفات الفيلم",
            allowClear: true,
            dir: "rtl",
            width: '100%'
        });
        
        // عرض معاينة الصورة
        $('#img').change(function(e) {
            const file = e.target.files[0];
            const fileError = $('#file-error');
            
            if (file) {
                // التحقق من نوع الملف
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    fileError.text('نوع الملف غير مدعوم. يرجى اختيار صورة بصيغة JPEG, PNG أو WebP.').show();
                    $(this).val('');
                    return;
                }
                
                // التحقق من حجم الملف (5MB كحد أقصى)
                if (file.size > 5 * 1024 * 1024) {
                    fileError.text('حجم الصورة يجب أن لا يتجاوز 5 ميجابايت').show();
                    $(this).val('');
                    return;
                }
                
                fileError.hide();
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#preview').attr('src', event.target.result).show();
                }
                reader.readAsDataURL(file);
            }
        });
        
        // تحسين تجربة المستخدم لسحب وإفلات الملفات
        const uploadArea = $('#uploadArea')[0];
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary-color)';
            uploadArea.style.backgroundColor = '#fff8e6';
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = 'var(--accent-color)';
            uploadArea.style.backgroundColor = '#fffaf0';
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--accent-color)';
            uploadArea.style.backgroundColor = '#fffaf0';
            
            if (e.dataTransfer.files.length) {
                $('#img')[0].files = e.dataTransfer.files;
                $('#img').trigger('change');
            }
        });
        
        // التحقق من صحة النموذج قبل الإرسال
        $('#uploadForm').submit(function(e) {
            let isValid = true;
            
            // التحقق من اسم الفيلم
            const name = $('#name').val().trim();
            if (name.length < 2 || name.length > 255) {
                $('#name-error').show();
                isValid = false;
            } else {
                $('#name-error').hide();
            }
            
            // التحقق من وجود ملف
            if ($('#img').get(0).files.length === 0) {
                $('#file-error').text('يجب اختيار صورة للفيلم').show();
                isValid = false;
            }
            
            // التحقق من التصنيفات
            if ($('#categories').val() === null || $('#categories').val().length === 0) {
                $('#categories').next('.select2-container').css('border', '1px solid var(--error-color)');
                isValid = false;
            } else {
                $('#categories').next('.select2-container').css('border', '');
            }
            
            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.error-message:visible').first().offset().top - 100
                }, 500);
            }
        });
    });
    </script>
    <a href="logout.php" style="display: block; text-align: center; margin: 20px; color: red;">تسجيل الخروج</a>
</body>
</html>