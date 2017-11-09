<!DOCTYPE html>
<html>
	<head>
		<title>Search Database</title>
		<meta charset="utf-8">

		<!-- Bootstrap stuff -->
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<!-- jQuery library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	</head>

	<body>
		<h1>Supreme Court Coverage/Analytics Application</h1>

		<!-- search bar + options -->
		<div class='searchBar'><form action='' method='GET'>
			Sort by: 
			<input type='radio' name='searchBy' value='title' checked='checked'>Title
			<input type='radio' name='searchBy' value='source'>Source
			<input type='radio' name='searchBy' value='keyword'>Keyword<br>
			<input type='search' name='search_query' placeholder='Type search query here...'>
			<button type='submit'>Search</button>
		</form></div>

		<?php
			// connect to database
			$connect = mysqli_connect("localhost", "root", "") or die(mysqli_connect_error());
    		mysqli_select_db($connect, "SupremeCourtApp") or die(mysqli_connect_error());

    		// base sql query
    		$sql = "SELECT date, title, source ";

    		// build sql query based on search criteria
    		if(isset($_GET['search_query']))
    		{
    			if(isset($_GET['searchBy']))
    			{
    				$search_query = mysqli_real_escape_string($connect, $_GET['search_query']);
    				if($_GET['searchBy'] == 'title') 
    					{ $sql .= "FROM article WHERE title LIKE '%{$search_query}%'"; }
    				else if($_GET['searchBy'] == 'source')
    					{ $sql .= "FROM article WHERE source LIKE '%{$search_query}%'"; }
    				else if($_GET['searchBy'] == 'keyword')
    					{ $sql = "SELECT DISTINCT title, source, date FROM article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances WHERE keyword LIKE '%{$search_query}%'"; }
    				else
    					{ $sql .= "FROM article"; }
    			}
    		}
    		else // default search yields all articles
    		{
    			$sql .= "FROM article";
    		}

    		$query = mysqli_query($connect, $sql) or die(mysqli_connect_error()); // execute query
		?>

		<!-- display query results as table -->
		<table>
			<thead>
				<th><strong>Title</strong></th>
				<th><strong>Source</strong></th>
				<th><strong>Date</strong></th>
			</thead>

			<?php
				while($row = mysqli_fetch_array($query))
				{
					$title = $row['title'];
					$source = $row['source'];
					$date = $row['date'];
					echo "<tr> <td>$title</td> <td>$source</td> <td>$date</td> </tr>";
				}
			?>
		</table>
	</body>
</html>