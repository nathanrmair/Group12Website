<!doctype html>
<html>
<head>
	<title>Course Search</title>
	<meta charset=utf-8>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type='text/javascript' src='autocomplete.js'></script>
	<script type='text/javascript' src='loadHeader.js'></script>
	<script>$(function(){$("#tabsBottom").tabs();});</script>
	<link rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css'/>
	<link rel='stylesheet' href='courses.css'/>
</head>
<body>
	<div id='header'>
	</div>
	<div id='title'>
		<h1>Course Search Results</h1>
	</div>
	<div id='searchContainerResults'>
		<form id='resultsSearchBar' method="GET" action="results.php">
			<input id='resultsSearchInput' name="search">
			<input id='resultsSearchSubmit' type="submit" value="Search">
		</form>
		<form id='resultsResultPerPage' method="POST">
		<select name="searchesPerPageSelector" id="searchesPerPageSelector">
			<option value="5">5</option>
			<option selected="selected" value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
		</select>
		<input type="submit" value="Results per page">
		</form>
	</div>
	<div id='searchResults'>
	<?php
		$search = $_REQUEST['search'];
		$connection = new mysqli("localhost","scott","tiger","courses");
		if ($connection->connect_errno){die("Database connection failed");}
		// first return the course whose name directly matches the search, then add on any courses that share any modules with those courses. Each of those results are seperately ordered by popularity
		$sqlCourses = "
			SELECT * FROM (
				SELECT id, award, title, level, dept, summary, href
				  FROM course LEFT JOIN cm ON course.id=cm.course
				 WHERE course.title LIKE '%$search%'
				 GROUP BY id
				 ORDER BY SUM(cm.num) DESC
			) DIRECT_RESULTS
			 UNION
			SELECT * FROM(
				SELECT id, award, title, level, dept, summary, href
				  FROM course LEFT JOIN cm ON course.id=cm.course
				 WHERE cm.module IN (SELECT DISTINCT module
			 				  FROM cm
							 WHERE course IN (SELECT id
							  	    	      FROM course
							 	   	     WHERE course.title LIKE '%$search%'))
				 GROUP BY id
				 ORDER BY SUM(cm.num) DESC
			) MATCHING_MODULES
			 UNION
				SELECT id, award, title, level, dept, summary, href
		  		  FROM course
				 WHERE course.summary LIKE '%$search%' OR
					course.overview LIKE '%$search%' OR
					course.wyl LIKE '%$search%' OR
					course.careers LIKE '%$search%'
		";
		$result = $connection->query($sqlCourses) or die($connection->error);

		$searchesPerPage = 10;
		if (isset($_POST['searchesPerPageSelector']))
		{
			$searchesPerPage = $_POST['searchesPerPageSelector'];
		}
		$resultCount = $result->num_rows;
		
		// create a panel layout to show all the results
		if ($resultCount > $searchesPerPage)
		{
		echo "<div id='tabsBottom'>";
			echo "<ul>";
			$tabCounter=0;
			$flag = 1;
			// loop through once to make all the tabs
			while($flag == 1)
			{
				$startingVal = ($searchesPerPage*$tabCounter) + 1;
				$endingVal = ($searchesPerPage*$tabCounter) + $searchesPerPage;
				if ($endingVal >= $resultCount)
				{
					$endingVal = $resultCount;
				}
				echo "<li><a href='#tabsBottom-$startingVal' class='resultsTab'>$startingVal-$endingVal</a></li>";
				$tabCounter++;
				if ($endingVal == $resultCount)
				{
					$flag = 2;
				}
			}
			echo "</ul>";
			$tabCounter=0;
			$indexCounter=0;
			$startingVal = 0;
			$endingVal = 0;
			// loop through the result rows adding to the relevant tab
			while($row = $result->fetch_assoc())
			{

				// if endingVal is less than indexCounter then start new tab
				if($endingVal == $indexCounter)
				{
					// update startingVal and endingVal to new values
					$startingVal = ($searchesPerPage*$tabCounter) + 1;
					$endingVal = ($searchesPerPage*$tabCounter) + $searchesPerPage;
					// create new tab entry
					echo "<div id='tabsBottom-$startingVal'>";
				}
				// add row to tab
				echo "<div class='resultContainer'>";
					echo "<a href='course.php?course=$row[id]'>$row[award] $row[title] ($row[id])</a>";
	
					$finalDash = strrpos($row['href'], "-");
					if (strrpos($row['href'], "-") == FALSE)
					{
						echo "<p>$row[level], School of $row[dept]</p>";
					}
					else
					{
						$commitment = substr($row['href'], ($finalDash +1));
						$formattedCommitment = str_replace('time','-time',$commitment);
						echo "<p>$row[level], $formattedCommitment, $row[dept] Department</p>";					
					}
					echo "<p>$row[summary]</p>";
				echo "</div>";
				// ++ the indexCounter
				$indexCounter++;
				// if indexCounter equals endingVal then finish row
				if($indexCounter == $endingVal)
				{
					echo "</div>";
					$tabCounter++;
				}
			}
		echo "</div>";
		}
		else
		{
			while($row = $result->fetch_assoc())
			{
				echo "<div class='resultContainer'>";
					echo "<a href='course.php?course=$row[id]'>$row[award] $row[title] ($row[id])</a>";
	
					$finalDash = strrpos($row['href'], "-");
					if (strrpos($row['href'], "-") == FALSE)
					{
						echo "<p>$row[level], School of $row[dept]</p>";
					}
					else
					{
						$commitment = substr($row['href'], ($finalDash +1));
						$formattedCommitment = str_replace('time','-time',$commitment);
						echo "<p>$row[level], $formattedCommitment, School of $row[dept]</p>";					
					}
					echo "<p>$row[summary]</p>";
				echo "</div>";
			}
		}
		if ($resultCount == 0)
		{
			echo "<p>No results returned. Perhaps your search was too specific, consider using the autocomplete to select available courses.</p>";
		}
	?>
</body>
</html>