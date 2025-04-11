<?php
include('config.php');

// جلب جميع معاملات التصفية
$filters = [
    'category' => $_GET['category'] ?? '',
    'rating' => $_GET['rating'] ?? '',
    'year' => $_GET['year'] ?? '',
    'language' => $_GET['language'] ?? '',
    'quality' => $_GET['quality'] ?? '',
    'sort' => $_GET['sort'] ?? 'title',
    'order' => $_GET['order'] ?? 'asc',
    'page' => (int)($_GET['page'] ?? 1)
];

// بناء الاستعلام الديناميكي
$sql = "SELECT * FROM films WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM films WHERE 1=1";
$params = [];

// إضافة الفلاتر
foreach (['category', 'rating', 'year', 'language', 'quality'] as $filter) {
    if (!empty($filters[$filter])) {
        $sql .= " AND $filter = ?";
        $count_sql .= " AND $filter = ?";
        $params[] = $filters[$filter];
    }
}

// الترتيب
$allowed_sorts = ['title', 'year', 'rating'];
$sort = in_array($filters['sort'], $allowed_sorts) ? $filters['sort'] : 'title';
$order = $filters['order'] === 'desc' ? 'DESC' : 'ASC';
$sql .= " ORDER BY $sort $order";

// الترقيم
$per_page = 20;
$offset = ($filters['page'] - 1) * $per_page;
$sql .= " LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

// التنفيذ
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// إذا كان طلب AJAX
if(isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $movies = [];
    while($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    
    echo json_encode([
        'movies' => $movies,
        'pagination' => generate_pagination($filters, $conn, $count_sql, $per_page)
    ]);
    exit;
}

function generate_pagination($filters, $conn, $count_sql, $per_page) {
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total / $per_page);

    ob_start();
    ?>
    <div class="pagination">
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="#" class="page-link <?= $i == $filters['page'] ? 'active' : '' ?>" 
               data-page="<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php
    return ob_get_clean();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>أفلام</title>
    <!-- إضافة Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
    }
    .no-results {
        color: red;
        padding: 20px;
        text-align: center;
        font-size: 1.2em;
    }
</style>
    <style>
        .filters {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            padding: 20px;
            background: #f5f5f5;
        }
        .film-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            padding: 20px;
        }
        .film-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .pagination {
            text-align: center;
            padding: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<form id="filters-form">
    <input type="hidden" name="page" value="1">

    <!-- القوائم المنسدلة -->
    <select name="category" class="select2">
        <option value="">جميع الأقسام</option>
        <?php 
        $categories = $conn->query("SELECT DISTINCT category FROM films");
        while($cat = $categories->fetch_assoc()): ?>
        <option value="<?= $cat['category'] ?>" <?= $cat['category'] == $filters['category'] ? 'selected' : '' ?>>
            <?= $cat['category'] ?>
        </option>
        <?php endwhile; ?>
    </select>

    <!-- أضف نفس النمط لباقي الفلاتر -->

    <!-- الترتيب -->
    <select name="sort">
        <option value="title" <?= $filters['sort'] == 'title' ? 'selected' : '' ?>>حسب العنوان</option>
        <option value="year" <?= $filters['sort'] == 'year' ? 'selected' : '' ?>>حسب السنة</option>
        <option value="rating" <?= $filters['sort'] == 'rating' ? 'selected' : '' ?>>حسب التقييم</option>
    </select>

    <select name="order">
        <option value="asc" <?= $filters['order'] == 'asc' ? 'selected' : '' ?>>تصاعدي</option>
        <option value="desc" <?= $filters['order'] == 'desc' ? 'selected' : '' ?>>تنازلي</option>
    </select>
</form>

<div class="film-grid"><!-- سيتم تعبئته عبر AJAX --></div>
<div class="pagination"><!-- سيتم تعبئته عبر AJAX --></div>



    <script>
$(document).ready(function() {
    // تهيئة Select2
    $('.select2').select2({
        placeholder: "اختر تصنيفًا",
        allowClear: true
    });

    // حدث التصفية والترتيب
    $('#filters-form').on('change', 'select, input', function() {
        loadMovies();
    });

    // حدث الترقيم
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        $('#filters-form input[name="page"]').val($(this).data('page'));
        loadMovies();
    });

    // تحميل الأفلام أول مرة
    loadMovies();
});

function loadMovies() {
    const formData = $('#filters-form').serialize();
    
    $.ajax({
        url: 'index.php?ajax=1',
        data: formData,
        success: function(response) {
            const data = JSON.parse(response);
            
            // تحديث الشبكة
            const moviesHtml = data.movies.map(movie => `
                <div class="film-card">
                    <img src="${movie.poster}" alt="${movie.title}">
                    <h3>${movie.title}</h3>
                </div>
            `).join('');
            
            $('.film-grid').html(moviesHtml.length ? moviesHtml : '<div class="no-results">لا توجد نتائج</div>');
            
            // تحديث الترقيم
            $('.pagination').html(data.pagination);
        },
        error: function() {
            alert('حدث خطأ أثناء التحميل');
        }
    });
}
</script>
</body>
</html>