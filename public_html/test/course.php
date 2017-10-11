<!doctype html>
<html>
<head>
	<title>Course Details</title>
	<meta charset=utf-8>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type='text/javascript' src='autocomplete.js'></script>
	<script type='text/javascript' src='loadHeader.js'></script>
	<script>$(function(){$("#tabs").tabs();});</script>
	<link rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css'/>
	<link rel='stylesheet' href='courses.css'/>
</head>
<body>
	<div id='header'>
	</div>
	<?php
		$course = $_REQUEST['course'];
		$m = new mysqli("localhost","scott","tiger","courses");
		if ($m->connect_errno){die("Database connection failed");}
		$sql = "
		SELECT *
		  FROM course
	        WHERE course.id = '$course'
		";
		$result = $m->query($sql) or die($m->error);
		$row = $result->fetch_assoc();

		$sqlModules = "
		SELECT title, id
		  FROM cm LEFT JOIN module ON cm.module=module.id
		 WHERE course LIKE '%$course%'
		";
		$moduleResult = $m->query($sqlModules) or die($m->error);
	?>
	<div id='title'>
	<?php
		echo "<h1>$row[title]</h1>";
	?>
	</div>
	<div id='courseDetails'>
		<?php
			echo "<p>Course code: $row[id]</p>";
			echo "<p>Level: $row[level]</p>";
			echo "<p>Award: $row[award]</p>";
			echo "<p>Department: $row[dept]</p>";
			echo "<p id='courseSummary'>$row[summary]</p>";
		?>
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1" class='tabHeader'>Overview</a></li>
				<li><a href="#tabs-2" class='tabHeader'>What you learn</a></li>
				<li><a href="#tabs-3" class='tabHeader'>Modules</a></li>
				<li><a href="#tabs-4" class='tabHeader'>Careers</a></li>
				<li><a href="#tabs-5" class='tabHeader'>Fees</a></li>
			</ul>
			<div id="tabs-1">
				<?php
					echo "<p>$row[overview]</p>";
				?>
			</div>
			<div id="tabs-2">
				<?php
					echo "<p>$row[wyl]</p>";
				?>
			</div>
			<div id="tabs-3">
				<?php
					echo "<p>Modules currently available for study whilst on this program are shown below. These may change however and should only be taken as a guide</p>";
					echo "<ul id='moduleList'>";
						while($moduleRow = $moduleResult->fetch_assoc())
						{
							echo "<li><a class='moduleLink' href='module.php?module=$moduleRow[id]'>$moduleRow[title]</a></li>";
						}
					echo "</ul>";
				?>
			</div>
			<div id="tabs-4">
				<?php
					echo "<p>$row[careers]</p>";
				?>
			</div>
			<div id="tabs-5">
				<p>Placeholder text for fee information. Placeholder text for fee information. Placeholder text for fee information. Placeholder text for fee information. Placeholder text for fee information. Placeholder text for fee information. Placeholder text for fee information. </p>
			</div>
		</div>
	</div>
</body>
</html>
