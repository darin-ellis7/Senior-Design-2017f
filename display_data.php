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
<link href="css/bootstrap-select.min.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
<link href="http://momentjs.com/downloads/moment.js" rel="stylesheet" />
<link href="https://github.com/drvic10k/bootstrap-sortable/blob/master/Contents/bootstrap-sortable.css" rel="stylesheet" />
<link href="https://github.com/drvic10k/bootstrap-sortable/blob/master/Scripts/bootstrap-sortable.js" rel="stylesheet" />
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesnt work if you view the page via file: -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body style="background: rgb(153, 204, 255)">

<div class="img_holder" onclick="myfuunc(this);">
<img class="flagimgs first" src="https://polisci.as.uky.edu/sites/default/files/political-science.png" width=10% height=10%  <h1><font size="5"> College of Art &amp; Science <p>Political Science</font></p></h1>

<p><font size="8">Supreme Court Coverage/Analytics Application</font></p></img>


<hr>
</div>
</body>
</html>


<?php
    
        include "search.php";
    
        $sql = "SELECT date, title, source, url FROM article ";
        //keep it for keyword
        $keyword = "SELECT title,source, date FROM article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances";
    
    
    
        if (isset($_GET['search_box'])) {
        
                $search_term = mysqli_real_escape_string($connect, $_GET['search_box']);
        
                $sql .= "WHERE title, source like '%{$search_term}%'";
            }
        else {
                $sql = "SELECT title, source, date, idArticle FROM article";
        
                  }
        $query = mysqli_query($connect, $sql) or die(mysqli_connect_error());
    
       ?>
<div class='container'>
<div class='content-wrapper'>
<div class='row'>

<div class='col-xs-12 col-sm-12 col-md-5 col-lg-8 center-block'>

<div class="input-group">
<div class="input-group-btn search-panel">
<select class="selectpicker" title="Choose one of the following...">
<option>Title</option>
<option>Source</option>
<option>Keyword</option>
</select>
</div>
<form name="navbar-form" method="GET" action="display_data.php">
<div class='input-group'>
<input class='form-control' type="text" name="search_box" value="" placeholder='Search' />
<span class="input-group-btn">
<button type='submit' class='btn btn-default'>
<span class='glyphicon glyphicon-search'></span>

</button>
</span>
</div>
</div>
</div>


</form>

<tr>

<div class="row">
<div class="col-sm-12">
<table class="table table-bordered dataTable no-footer">
             <thead>
                <tr role="row">
    <th><strong>Title</strong></th>
    <th><strong>Source</strong></th>
<th class ="sorting_asc tabindex="0" aria-controls="example" rowspan="1" colspan="1" role="grid" aria-describedby="example-info";"><strong>Date</strong></th>
</tr>
                </tr>
             </thead>
<?php while ($row = mysqli_fetch_array($query)) { ?>

<tr  class='clickable-row' data-href='./display_article.php?idArticle=<?php echo $row['idArticle']; ?>'>
<td><button class="btn btn-link" style="color:black"><?php echo $row['title']; ?></button></td>
<td><?php echo $row['source']; ?></td>
<td  data-dateformat="MM-DD-YYYY"><?php echo $row['date']; ?></td>
</tr>
 <?php }?>

</table>

</div>
</div>
<div class="dataTables_paginate paging_simple-number center-block" id="example_paginate">
<ul class="pagination">
<li class="paginate_button previos disabled" id="example_previous">
<a href="#" aria-controls="example" data-dt-idx="0" tabindex="0">Previous</a>
</li>
<li class="paginate_button active">
<a href="#" aria-controls="example" data-dt-idx="1" tabindex="0">1</a>
</li>
<li class="paginate_button active">
<a href="#" aria-controls="example" data-dt-idx="2" tabindex="0">2</a>
</li>
<li class="paginate_button active">
<a href="#" aria-controls="example" data-dt-idx="3" tabindex="0">3</a>
</li>
<li class="paginate_button active">
<a href="#" aria-controls="example" data-dt-idx="4" tabindex="0">4</a>
</li>
<li class="paginate_button active">
<a href="#" aria-controls="example" data-dt-idx="5" tabindex="0">5</a>
</li>
<li class="paginate_button next" id="example_next">
<a href="#" aria-controls="example" data-dt-idx="6" tabindex="0">Next</a>
</li>
</ul>
</div>
<!-- jQuery (necessary for Bootstraps JavaScript plugins) -->
             <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
              <script src="js/jquery.js"></script>
             <script src="js/dropdown.js"></script>
             <script src="js/src/dropdown.js"></script>
             <!-- Include all compiled plugins (below), or include individual files as needed -->
             <script src="js/bootstrap.min.js"></script>
             <script src="js/bootstrap-select.min.js"></script>
            <script src="js/i18n/defaults-*.min.js"></script>
             <script src="js/jquery.dataTables.min.js"></script>
             <script src="js/dataTables.bootstrap.min.js"></script>
            <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
            <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
            <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
            <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
             <script>

             $('#mydata').DataTable();

             </script>

             </body>
</html>
