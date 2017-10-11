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
	
		$search = $_REQUEST['search'];
		db_connect();
		// first return the course whose name directly matches the search, then add on any courses that share any modules with those courses. Each of those results are seperately ordered by popularity
		$query = "
			SELECT c.id, c.datetime, c.name, course, v.name AS 'venue', c.duration, c.price
				  FROM COURSES c LEFT JOIN VENUES v ON c.venue=v.id
				 WHERE c.name LIKE '%$search%'
				 GROUP BY id
		";
		$result = db_query($query);
		if($result === false) {
			$error = db_error();
			exit("database connection failed");
		}

		$searchesPerPage = 10;
		if (isset($_POST['searchesPerPageSelector']))
		{
			$searchesPerPage = $_POST['searchesPerPageSelector'];
		}
		$resultCount = count($result);
		
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
			while($row = mysqli_fetch_assoc($result))
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
					echo "<a href='course.php?course=$row[id]'>$row[name], $row[venue] ($row[duration] Days, £$row[price])</a>";
	
					$finalDash = strrpos($row['href'], "-");
					if (strrpos($row['href'], "-") == FALSE)
					{
						echo "<p>$row[name], Hosted at $row[venue]</p>";
					}
					else
					{
						$commitment = substr($row['href'], ($finalDash +1));
						$formattedCommitment = str_replace('time','-time',$commitment);
						echo "<p>$row[id], $formattedCommitment, $row[id] Department</p>";					
					}
					echo "<p>$row[course]</p>";
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
			while($row = mysqli_fetch_assoc($result))
			{
				echo "<div class='resultContainer'>";
					echo "<a href='course.php?course=$row[id]'>$row[name], $row[venue] ($row[duration] Days, £$row[price])</a>";
	
					$finalDash = strrpos($row['href'], "-");
					if (strrpos($row['href'], "-") == FALSE)
					{
						echo "<p>$row[name], Hosted at $row[venue]</p>";
					}
					else
					{
						$commitment = substr($row['href'], ($finalDash +1));
						$formattedCommitment = str_replace('time','-time',$commitment);
						echo "<p>$row[id], $formattedCommitment, $row[id] Department</p>";					
					}
					echo "<p>$row[course]</p>";
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