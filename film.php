<!DOCTYPE html>
<html lang="ar" dir="rtl">
<!-- تعيين لغة الصفحة للعربية وتوجيه النص من اليمين لليسار -->

<head>
    <meta charset="UTF-8">
    <!-- تحديد ترميز الأحرف لضبط عرض اللغة العربية -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">
    <!-- ضبط عرض الصفحة حسب عرض الجهاز مع السماح بالتكبير حتى 5 أضعاف -->
    <title>مشغل الأفلام</title>
    <!-- عنوان الصفحة الذي يظهر في تبويب المتصفح -->
    <link rel="icon" href="/favicon.ico">
    <!-- أيقونة الموقع التي تظهر بجوار العنوان في المتصفح -->
    
    <!-- استيراد خط فستات من جوجل فونتس مع أوزان متعددة -->
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- استيراد أيقونات الماتريال من جوجل -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
    <link rel="stylesheet" href="css1.css">
    <style>
   
    </style>
</head>
<body>
    <??>
    <!-- شريط التنقل العلوي -->
    <div class="div_top">
        <!-- الأيقونات على اليسار -->
        <div class="acunt">
            <a href="#"><i class="material-icons acunt acunt1">person</i></a>
        </div>
        <div class="darkmood">
            <a href="#"><i class="material-icons">nightlight</i></a>
        </div>
        <div class="add">
            <button class="button" aria-label="إضافة محتوى جديد">
                <span class="button-content">
                    <i class="material-icons-outlined" aria-hidden="true">add_circle</i>
                    <span style="color: white;" class="button-text">اضيف حديثا</span>
                </span>
            </button>
        </div>
        
        <!-- الشعار على اليمين -->
        <div class="alanwan">
            <img src="https://www.raed.net/img?id=1276981" alt="شعار الموقع">
        </div>
    </div>
    
    <!-- مربع البحث -->
    <form action="search.php" method="GET">
        <div class="searsh1">
            <div class="search-box">
            <a href="#" class="searsh_aicon">
                <i class="material-icons-outlined">search</i>
            </a>
            <input type="search" name="id" id="searsh2" placeholder="بحث">
            </div>
        </div>
    </form>  
      
     <!--
    <div class="searsh1" dir="rtl">
        <a href="searsh_icon"></a>
        <i class="material-icons-outlined searsh_aicon">search</i>
       أيقونة البحث 
        <input type="search" name="" id="searsh2" placeholder="بــحـــــث">
        حقل إدخال البحث 
    </div>-->
    
    <!-- تصنيفات الأفلام -->
    <div dir="rtl" class="altasnef">
        <!-- تصنيف الأفلام -->
        <div class="div_film">
            <a href="film1.php">
               <img src="https://www.raed.net/img?id=1282868" class="film" alt="أفلام"><br>
                <span class="span_altasnef">افــــــــــلام</span>
            </a>
        </div>
        
        <!-- تصنيف المسلسلات -->
        <div class="div_film">
            <a href="">
                <svg class="mosalsal" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFB100" d="M46,37H2c-0.553,0-1-0.447-1-1V8c0-0.552,0.447-1,1-1h44c0.553,0,1,0.448,1,1v28C47,36.553,46.553,37,46,37z M45,9H3v26h42V9z M21,16c0.214,0,0.4,0.082,0.563,0.196l7.771,4.872C29.72,21.205,30,21.566,30,22c0,0.325-0.165,0.601-0.405,0.783l-7.974,4.981C21.449,27.904,21.238,28,21,28c-0.553,0-1-0.447-1-1V17C20,16.448,20.447,16,21,16z M15,39h18c0.553,0,1,0.447,1,1s-0.447,1-1,1H15c-0.553,0-1-0.447-1-1S14.447,39,15,39z"/>
                </svg><br>
                <span class="span_altasnef">مســـلـســلات</span>
            </a>
        </div>

        <!-- تصنيف التلفزيون -->
        <div class="div_film">
            <a href="">
              <img src="https://www.raed.net/img?id=1282875" class="tv" alt="تلفزيون"><br>
                <span class="span_altasnef">تــلــفــزيــون</span>
            </a>
        </div>
        
        <!-- تصنيف المنوعات -->
        <div class="div_film div_mnua">
            <a href="">
              <img class="mnua" src="https://www.raed.net/img?id=1282882" alt="منوعات"><br>
                <span class="span_altasnef">انـــمــــي</span>
            </a>
        </div>        
    </div>
    
    <!-- قسم الأفلام المميزة -->
<div class="featured-section">
    <div dir="rtl" class="div_almumayzh">
        <div class="span_almumayzh">
            <span><b>الــمـميــزة</b></span>
            <div class="hr1"></div>
        </div>
        
        <div class="div_almumayzh1_1">
            <div class="div_img_almumayzh">
                <!-- قائمة الأفلام المميزة مع روابطها -->
                <a href="">
                    <img src="https://www.raed.net/img?id=1282886" alt="">
                </a>
                <a href="">
                    <img src="https://www.raed.net/img?id=1282885" alt="">
                </a>
                <a href="">
                    <img src="https://www.raed.net/img?id=1282886" alt="">
                </a>
                <a href="">
                    <img src="https://www.raed.net/img?id=1282885" alt="">
                </a>
                <a href="">
                    <img src="https://www.raed.net/img?id=1282886" alt="">
                </a>
                <a href="">
                    <img src="https://www.raed.net/img?id=1282885" alt="">
                </a>
            </div>
        </div>
    </div>
</div>
<!-- مسافة فاصلة -->
<div class="section-spacer"></div>

    <!-- قسم الأفلام الرئيسي -->
<div class="movies-section">
    <div class="div_alaflam">
        <div dir="rtl" class="div_alaflam_span_hr">
            <div class="span_alaflam">
                <span style="margin-top: 1.25rem;padding: 0 0.5375rem;"><b>الأفـــــلام</b></span>
                <div class="div_button_aksam_alaflam">
                    <button class="button_alkaymh" onclick="toggleDropdown('dropdownMovies', this)">الأقــســام ▼</button>
                    <div class="div_kaymt_aksam_alaflam" id="dropdownMovies">
                        <a href="#">عـــــربــي</a>
                        <a href="#">أجـنـبــي</a>
                        <a href="#">هـــنـــدي</a>
                        <a href="#">تـــركــــي</a>
                        <a href="#">آســـيـــوي</a>
                    </div>
                </div>
            </div>
            <div class="hr2"></div>
        </div>
    
        <div class="div_alaflam_1">
            <div class="div_img_alaflam">
                <?php
                include('alatsal.php');
                $result = mysqli_query($con, "SELECT * FROM film2");

                while($row = mysqli_fetch_array($result)){
                    echo "
                    <a href='tfaseil_alfilm.php?id=".$row['id']."'>  <!-- إضافة معرّف الفيلم إلى الرابط -->
                        <img src='".$row['img']."' alt='".$row['name']."'>
                    </a>
                    ";
                }
                ?>
            </div>
        </div>
    </div>
</div>
    <div class="div_almazeed">
        <button>
            <a href="">
                الـمزيـد
                <i class="material-icons add2" id="add">add</i>
            </a>
        </button>
    </div><br><br><br>
    <!-- كود الجافاسكريبت للتحكم في القائمة المنسدلة -->
    <script>
    // دالة تبديل عرض/إخفاء القائمة المنسدلة
    function toggleDropdown() {
        var dropdown = document.getElementById('dropdownList');
        var button = document.querySelector('.button_alkaymh');
        dropdown.classList.toggle("show");
        button.classList.toggle("active");
    }
    
    // إغلاق القائمة عند النقر خارجها
    window.onclick = function(event) {
        if (!event.target.matches('.button_alkaymh')) {
            var dropdowns = document.getElementsByClassName("div_kaymt_aksam_alaflam");
            var button = document.querySelector('.button_alkaymh');
            for (let i = 0; i < dropdowns.length; i++) {
                dropdowns[i].classList.remove('show');
            }
            button.classList.remove('active');
        }
    }
    </script>
    <!-- بعد قسم الأفلام مباشرة (قبل إغلاق الجسم) -->
<div class="div_alaflam">
    <div dir="rtl" class="div_alaflam_span_hr">
        <div class="span_alaflam">
            <span style="margin-top: 1.25rem;padding: 0 0.5375rem;"><b>المـسـلـسـلات</b></span>
            <div class="div_button_aksam_alaflam">
                <button class="button_alkaymh" onclick="toggleDropdown('dropdownSeries', this)">الأقــســام ▼</button>
                <div class="div_kaymt_aksam_alaflam" id="dropdownSeries">
                    <a href="#">عـــــربــي</a>
                    <a href="#">أجـنـبــي</a>
                    <a href="#">تـــركــــي</a>
                    <a href="#">كـــوري</a>
                    <a href="#">أنـــيـمــي</a>
                </div>
            </div>
        </div>
        <div class="hr2"></div>
    </div>
    <div class="div_alaflam_1">
        <div class="div_img_alaflam">
            <a href=""><img src="https://www.raed.net/img?id=1282885" alt=""></a>
            <a href=""><img src="https://www.raed.net/img?id=1282886" alt=""></a>
            <a href=""><img src="https://www.raed.net/img?id=1282885" alt=""></a>
            <a href=""><img src="https://www.raed.net/img?id=1282886" alt=""></a>
            <a href=""><img src="https://www.raed.net/img?id=1282885" alt=""></a>
            <a href=""><img src="https://www.raed.net/img?id=1282886" alt=""></a>
        </div>
    </div>
</div>
<div class="div_almazeed">
    <button>
        <a href="">
            الـمزيـد
            <i class="material-icons add2" id="add">add</i>
        </a>
    </button>
</div><br><br><br>
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
<!-- تحديث دالة الجافاسكريبت قبل إغلاق وسم الـ script -->
<script>
    // دالة معدلة لدعم جميع القوائم المنسدلة
    function toggleDropdown(dropdownId, button) {
        // إغلاق جميع القوائم المفتوحة
        document.querySelectorAll('.div_kaymt_aksam_alaflam.show').forEach(function(openDropdown) {
            if (openDropdown.id !== dropdownId) {
                openDropdown.classList.remove('show');
                openDropdown.previousElementSibling.classList.remove('active');
            }
        });
        
        // تبديل القائمة الحالية
        var dropdown = document.getElementById(dropdownId);
        dropdown.classList.toggle("show");
        button.classList.toggle("active");
    }
    
    // إغلاق القوائم عند النقر خارجها
    window.onclick = function(event) {
        if (!event.target.matches('.button_alkaymh')) {
            document.querySelectorAll('.div_kaymt_aksam_alaflam.show').forEach(function(dropdown) {
                dropdown.classList.remove('show');
                dropdown.previousElementSibling.classList.remove('active');
            });
        }
    }
    </script>
    <script>
        // تغيير الوضع الليلي
        document.querySelector('.darkmood i').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            
            // تغيير لون الأيقونة
            if (document.body.classList.contains('dark-mode')) {
                this.style.color = '#ffffff'; // لون فاتح في الوضع الليلي
            } else {
                this.style.color = 'var(--primary-color)'; // العودة للون الذهبي
            }
        });
        
        // تغيير لون أيقونة الحساب عند النقر
        document.querySelector('.acunt i').addEventListener('click', function() {
            this.style.color = this.style.color === 'rgb(255, 255, 255)' ? 
                              'var(--primary-color)' : 
                              '#ffffff';
        });
        </script>
</body>
</html>