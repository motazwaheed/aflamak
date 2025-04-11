<?php
include('alatsal.php');

// جلب التصنيفات وتحسين الأداء
$categories_query = "SELECT c.id, c.name, GROUP_CONCAT(mc.movie_id) as movie_ids 
                    FROM categories c
                    LEFT JOIN movie_categories mc ON c.id = mc.category_id
                    GROUP BY c.id";
$categories_result = mysqli_query($con, $categories_query);

$all_categories = [];
$movie_categories_map = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $all_categories[$row['id']] = $row['name'];
    if (!empty($row['movie_ids'])) {
        $movie_ids = explode(',', $row['movie_ids']);
        foreach ($movie_ids as $movie_id) {
            if (!isset($movie_categories_map[$movie_id])) {
                $movie_categories_map[$movie_id] = [];
            }
            $movie_categories_map[$movie_id][] = $row['id'];
        }
    }
}

// جلب معايير التصفية مع تحسين الأمان
$year = isset($_GET['year']) ? (int)$_GET['year'] : null;
$category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$country = isset($_GET['country']) ? mysqli_real_escape_string($con, $_GET['country']) : null;
$quality = isset($_GET['quality']) ? mysqli_real_escape_string($con, $_GET['quality']) : null;
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : null;

// بناء الاستعلام مع تحسين الأداء
$query = "SELECT DISTINCT f.* FROM film2 f 
          LEFT JOIN movie_categories mc ON f.id = mc.movie_id 
          WHERE 1=1";

if ($year) $query .= " AND f.year = $year";
if ($country) $query .= " AND f.albld = '$country'";
if ($quality) $query .= " AND f.quality = '$quality'";
if ($search) $query .= " AND (f.name LIKE '%$search%' OR f.description LIKE '%$search%')";
if ($category) $query .= " AND mc.category_id = $category";

// إضافة الترقيم
$per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// جلب العدد الكلي للنتائج
$count_query = str_replace('SELECT DISTINCT f.*', 'SELECT COUNT(DISTINCT f.id) as total', $query);
$count_result = mysqli_query($con, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $per_page);

$query .= " ORDER BY f.id DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أفلامك - تصفح الأفلام والمسلسلات</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FFB100;
            --primary-dark: #e69c00;
            --dark: #1a1a1a;
            --darker: #121212;
            --secondary: #222831;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.7);
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 12px rgba(0,0,0,0.15);
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--dark);
            color: var(--text);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .page-header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        
        .page-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        
        .page-header p {
            color: var(--text-light);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Search Box */
        .search-box {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }
        
        .search-box input {
            flex: 1;
            padding: 15px 20px 15px 50px;
            border: none;
            border-radius: var(--radius);
            background: var(--darker);
            color: white;
            font-size: 1rem;
            outline: none;
            transition: var(--transition);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(255, 177, 0, 0.2);
        }
        
        .search-box button {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
        }
        
        /* Filter Section */
        .filter-section {
            background: var(--darker);
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            border: 1px solid rgba(255,177,0,0.1);
            box-shadow: var(--shadow);
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .filter-group select {
            width: 100%;
            padding: 12px 15px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.2);
            background: var(--secondary);
            color: white;
            font-family: 'Tajawal', sans-serif;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .filter-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(255, 177, 0, 0.2);
        }
        
        .filter-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        
        .filter-btn {
            background: var(--primary);
            color: black;
            border: none;
            padding: 12px 25px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: bold;
            font-family: 'Tajawal', sans-serif;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .reset-btn {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
            padding: 8px 12px;
            border-radius: var(--radius-sm);
        }
        
        .reset-btn:hover {
            background: rgba(255,177,0,0.1);
        }
        
        /* Movies Grid */
        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .movie-card {
            background: var(--secondary);
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow);
            position: relative;
        }
        
        .movie-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }
        
        .movie-poster {
            position: relative;
            height: 380px;
            overflow: hidden;
        }
        
        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .movie-card:hover .movie-poster img {
            transform: scale(1.05);
        }
        
        .movie-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary);
            color: black;
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            font-weight: bold;
            font-size: 0.8rem;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .movie-info {
            padding: 20px;
        }
        
        .movie-title {
            font-size: 1.2rem;
            margin: 0 0 10px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text);
        }
        
        .movie-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
            min-height: 24px;
        }
        
        .category-badge {
            background: rgba(255,177,0,0.1);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            display: inline-block;
            transition: var(--transition);
        }
        
        .category-badge:hover {
            background: rgba(255,177,0,0.2);
        }
        
        .movie-meta {
            display: flex;
            justify-content: space-between;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .movie-rating {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            color: gold;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--darker);
            color: white;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .page-link:hover, .page-link.active {
            background: var(--primary);
            color: black;
            border-color: var(--primary);
        }
        
        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .loading-spinner {
            border: 3px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            border-top: 3px solid var(--primary);
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* No Results */
        .no-results {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
            color: var(--text-light);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            }
            
            .movie-poster {
                height: 320px;
            }
        }
        
        @media (max-width: 768px) {
            .filter-group {
                min-width: 100%;
            }
            
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
            
            .movie-poster {
                height: 280px;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
            
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
            
            .movie-poster {
                height: 240px;
            }
            
            .movie-info {
                padding: 15px;
            }
        }
        /* أضف هذا في قسم الـ styles */
.nice-select {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.2);
    background: var(--darker);
    color: white;
    font-family: 'Tajawal';
    cursor: pointer;
    transition: all 0.3s;
}

.nice-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255,177,0,0.2);
}

.movie-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin: 8px 0;
}

.category-tag {
    display: inline-block;
    background: rgba(255,177,0,0.15);
    color: var(--primary);
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    transition: all 0.3s;
}

.category-tag:hover {
    background: rgba(255,177,0,0.3);
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
    <div class="container">
        <div class="page-header">
            <h1>تصفح الأفلام والمسلسلات</h1>
            <p>استمتع بمشاهدة أحدث وأروع الأعمال السينمائية</p>
        </div>
        
        <!-- Search Box -->
        <form method="get" class="search-box">
            <input type="text" name="search" placeholder="ابحث عن فيلم أو مسلسل..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit"><i class="material-icons">search</i></button>
        </form>
        
        <!-- Filters Section -->
        <div class="filter-section">
            <form method="get">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search ?? '') ?>">
                
                <div class="filter-row">
                <div class="filter-group">
    <label>التصنيف</label>
    <select name="category" class="nice-select">
        <option value="">كل التصنيفات</option>
        <?php foreach ($all_categories as $id => $name): ?>
            <option value="<?= $id ?>" <?= $category == $id ? 'selected' : '' ?>>
                <?= htmlspecialchars($name) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

                    <div class="filter-group">
                        <label>السنة</label>
                        <select name="year">
                            <option value="">كل السنوات</option>
                            <?php
                            $years = mysqli_query($con, "SELECT DISTINCT year FROM film2 ORDER BY year DESC");
                            while ($row = mysqli_fetch_assoc($years)) {
                                $selected = $year == $row['year'] ? 'selected' : '';
                                echo "<option value='{$row['year']}' $selected>{$row['year']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- داخل <div class="filter-row"> -->
<div class="filter-group">
    <label>التصنيف</label>
    <select name="category" class="nice-select">
        <option value="">كل التصنيفات</option>
        <?php foreach ($all_categories as $id => $name): ?>
            <option value="<?= $id ?>" <?= isset($_GET['category']) && $_GET['category'] == $id ? 'selected' : '' ?>>
                <?= htmlspecialchars($name) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                    <div class="filter-group">
                        <label>التصنيف</label>
                        <select name="category">
                            <option value="">كل التصنيفات</option>
                            <?php foreach ($all_categories as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $category == $id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>الدولة</label>
                        <select name="country">
                            <option value="">كل الدول</option>
                            <?php
                            $countries = mysqli_query($con, "SELECT DISTINCT albld FROM film2 WHERE albld != '' ORDER BY albld");
                            while ($row = mysqli_fetch_assoc($countries)) {
                                $selected = $country == $row['albld'] ? 'selected' : '';
                                echo "<option value='{$row['albld']}' $selected>{$row['albld']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>الجودة</label>
                        <select name="quality">
                            <option value="">كل الجودات</option>
                            <option value="FHD" <?= $quality == 'FHD' ? 'selected' : '' ?>>FHD</option>
                            <option value="HD" <?= $quality == 'HD' ? 'selected' : '' ?>>HD</option>
                            <option value="SD" <?= $quality == 'SD' ? 'selected' : '' ?>>SD</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <a href="film1.php" class="reset-btn">
                        <i class="material-icons" style="font-size: 1.1rem; vertical-align: middle;">refresh</i>
                        إعادة تعيين
                    </a>
                    <button type="submit" class="filter-btn">
                        <i class="material-icons" style="font-size: 1.1rem;">filter_alt</i>
                        تصفية النتائج
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Movies Grid -->
        <div class="movies-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($movie = mysqli_fetch_assoc($result)): ?>
                    <div class="movie-card">
                        <a href="tfaseil_alfilm.php?id=<?= $movie['id'] ?>">
                            <div class="movie-poster">
                                <img src="<?= htmlspecialchars($movie['img']) ?>" alt="<?= htmlspecialchars($movie['name']) ?>" onerror="this.src='img/default-poster.jpg'">
                                <span class="movie-badge">FHD</span>
                                <?php if (!empty($movie['altkyem'])): ?>
                                    <div class="movie-rating">
                                        <i class="material-icons" style="font-size: 1rem;">star</i>
                                        <?= number_format($movie['altkyem'], 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="movie-info">
                                <h3 class="movie-title"><?= htmlspecialchars($movie['name']) ?></h3>
                                
                                    <!-- أضف هذا الكود هنا -->
                                <div class="movie-categories">
                                    <?php if (isset($movie_categories_map[$movie['id']])): ?>
                                        <?php foreach ($movie_categories_map[$movie['id']] as $cat_id): ?>
                                            <span class="category-tag"><?= $all_categories[$cat_id] ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="movie-meta">
                                    <span><?= $movie['year'] ?></span>
                                    <span><?= $movie['albld'] ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="material-icons" style="font-size: 3rem; margin-bottom: 15px; color: var(--text-light);">search_off</i>
                    <h3 style="color: var(--text); margin-bottom: 10px;">لا توجد نتائج</h3>
                    <p>لم نتمكن من العثور على أفلام تطابق معايير البحث الخاصة بك</p>
                    <a href="film1.php" class="filter-btn" style="display: inline-flex; margin-top: 15px;">
                        <i class="material-icons">refresh</i>
                        عرض جميع الأفلام
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="page-link" title="الصفحة السابقة">
                        <i class="material-icons">chevron_right</i>
                    </a>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1) echo '<span class="page-link" style="cursor: default;">...</span>';
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-link <?= $page == $i ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; 
                
                if ($end < $total_pages) echo '<span class="page-link" style="cursor: default;">...</span>';
                ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="page-link" title="الصفحة التالية">
                        <i class="material-icons">chevron_left</i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // حفظ حالة الفلاتر عند التصفح
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة تأثير تحميل عند النقر على الروابط
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.href.includes('film1.php')) return;
                document.body.style.opacity = '0.7';
            });
        });
        
        // إضافة تأثير عند التمرير على البطاقات
        document.querySelectorAll('.movie-card').forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const angleX = (y - centerY) / 20;
                const angleY = (centerX - x) / 20;
                
                this.style.transform = `translateY(-10px) rotateX(${angleX}deg) rotateY(${angleY}deg)`;
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-10px) rotateX(0) rotateY(0)';
            });
        });
    });
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
</body>
</html>