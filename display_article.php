<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Bootstrap 101 Template</title>

<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body style="background: rgb(153, 204, 255)">

<div class="img_holder" onclick="myfuunc(this);">
<img class="flagimgs first" src="https://polisci.as.uky.edu/sites/default/files/political-science.png" width=10% height=10%  <h1><font size="5"> College of Arts &amp; Sciences <p>Political Science</font></p></h1>

<p><font size="8">Supreme Court Coverage/Analytics Application</font></p></img>


<hr>
</div>
</body>
</html>


<?php
   
    include "search.php";
    $search_term = $_GET['idArticle'];
    $sql = "SELECT date, title, source, url FROM article WHERE idArticle='%{$search_term}%'";
    //keep it for keyword
    //$keyword = "SELECT title,source, date FROM article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances";
    
 
    
    if (isset($_POST['search'])) {
        
        $search_term = $_GET['idArticle'];
        
        $sql .= "WHERE idArticle='%{$search_term}%'";
	   //echo $sql;
    }
    else {
	   $search_term = $_GET['idArticle'];
	   $sql = "SELECT date, title, article_text, url FROM article WHERE idArticle='{$search_term}'";
    	   //echo $sql;
          
          }
    $query = mysqli_query($connect, $sql) or die(mysqli_connect_error());
    
    ?>
<div class='container'>
<div class='content-wrapper'>
<div class='row'>
<div class='col-xs-12 col-sm-12 col-md-8 col-lg-8'>
</div>
<div class='col-xs-12 col-sm-12 col-md-5 col-lg-5 center-block'>

<div class="input-group">
<div class="input-group-btn search-panel">
<button type="button" class="btn btn-default data-toggle="dropdown">
<span id="search_concept">Search By</span> <span class="caret"></span>
<ul class="dropdown-menu">
<li><a href="localhost">Title</a></li>
<li><a href="localhost">Source</a></li>
<li><a href="">Keyword</a></li>
</ul>

</button>
</div>
<form name="navbar-form" method="POST" action="display_data.php">
<div class='input-group'>
<input class='form-control' type="text" name="search_box" value="" placeholder='Search' />
<span class="input-group-btn">
<button type='submit' name="search" class='btn btn-default'>
<span class='glyphicon glyphicon-search'></span>
</button>
</span>
</div>
  </div>
</div>
</form>
<tr>
            
             </thead>
<?php while ($row = mysqli_fetch_array($query)) { ?>
  

</br></br><b><big><?php echo $row['title']; ?></b></big></br>
<?php echo $row['date']; ?></br>
<?php echo $row['article_text']; ?></br>

 <?php }?>

</table>

</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
             <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
              <script src="js/jquery.js"></script>
             <!-- Include all compiled plugins (below), or include individual files as needed -->
             <script src="js/bootstrap.min.js"></script>
             <script src="js/jquery.dataTables.min.js"></script>
             <script src="js/dataTables.bootstrap.min.js"></script>
             <script>
            
             $('#mydata').DataTable();
             
             </script>
         
             </body>
</html>