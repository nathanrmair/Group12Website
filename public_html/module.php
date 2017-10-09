<!doctype html>
<html>
<head>
	<title>Module Details</title>
	<meta charset=utf-8>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type='text/javascript' src='autocomplete.js'></script>
	<script type='text/javascript' src='loadHeader.js'></script>
	<link rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css'/>
	<link rel='stylesheet' href='courses.css'/>
</head>
<body>
	<div id='header'>
	</div>
	<div id='title'>
	<?php
		$module = $_REQUEST['module'];
		$connection = new mysqli("localhost","scott","tiger","courses");
		if ($connection->connect_errno){die("Database connection failed");}
		$sqlModule = "
			SELECT *
			  FROM module
		        WHERE id = '$module'
		";
		$resultModule = $connection->query($sqlModule) or die($connection->error);
		$row = $resultModule->fetch_assoc();
		$sqlCourses = "
			SELECT DISTINCT title, id, award
			  FROM cm JOIN course ON cm.course=course.id
			 WHERE module = '$module'
			 ORDER BY (num) DESC
		";
		$resultCourses = $connection->query($sqlCourses) or die($connection->error);
		echo "<h1>$row[title]</h1>";
	?>
	</div>
	<div id='moduleDetails'>
		<?php
			echo "<p>Level: $row[level]</p>";
			echo "<p>Credits: $row[credits]</p>";
			echo "<p id='moduleSummary'>Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. Placeholder text for module description. </p>";
			echo "<p>This module is currently available within the following programs. Please note that this may change and should be taken only as a guide.</p>";
			echo "<ul id='courseList'>";
				while($courseRow = $resultCourses->fetch_assoc())
				{
					echo "<li><a href='course.php?course=$courseRow[id]'>$courseRow[title]($courseRow[award])</a></li>";
				}
			echo "</ul>";
		?>
	</div>
</body>
</html>