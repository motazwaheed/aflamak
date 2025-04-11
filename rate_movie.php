<?php
include('alatsal.php');

header('Content-Type: application/json');

$movie_id = (int)$_GET['id'];
$rating = min(max((int)$_GET['rating'], 1), 10); // التأكد أن التقييم بين 1 و10

// 1. أولاً: تحديث تقييم الفيلم
$query = "UPDATE film2 SET 
          altkyem = IFNULL((altkyem * num_ratings + $rating) / (num_ratings + 1), $rating),
          num_ratings = IFNULL(num_ratings, 0) + 1
          WHERE id = $movie_id";

if(mysqli_query($con, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
}
?>