<?php
function db_connect() {
	
	static $connection;
	
	if(!isset($connection)) {
		$config = parse_ini_file('db/config.ini');
		$connection = mysqli_connect('localhost',$config['username'],$config['password'],$config['dbname']);
	}

	if($connection === false) {
		return mysqli_connect_error();
	}
	
	return $connection;
}
function db_query($query)
{
	$connection = db_connect();
	
	$result = mysqli_query($connection,$query);
	
	return $result;

}

function db_error() {
    $connection = db_connect();
    return mysqli_error($connection);
}

function db_select($query) {
	
	$rows = array();
	$result = db_query($query);
	if($result === false) {
		return false;
	} else {
		while($row = mysqli_fetch_assoc($result))
		{
			$rows[] = $row['name'];
		}
	}
	
	return $rows;
}
$keyword = $_REQUEST['term'];
$rows = db_select("SELECT DISTINCT name FROM COURSES WHERE COURSES.name LIKE '%$keyword%'");
if($rows === false) {
	$error = db_error();
	exit("database connection failed");
}
echo json_encode($rows);
?>











