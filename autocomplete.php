<?php
$m = new mysqli('localhost','scott','tiger','courses');
if ($m->connect_errno){
	die("Database connection failed");
}
$keyword = $_REQUEST['term'];
$m->set_charset("utf8");
$sql = "
SELECT DISTINCT title
  FROM course
 WHERE course.title LIKE '%$keyword%' 
";
$result = $m->query($sql) or die($m->error);
while($row = $result->fetch_assoc())
{
	$data[] = $row['title'];
}

echo json_encode($data);
?>