$search = mysqli_real_escape_string($con, $_GET['q']);
$result = mysqli_query($con, "SELECT * FROM film2 WHERE name LIKE '%$search%' OR description LIKE '%$search%' OR actors LIKE '%$search%'");