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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/jquery.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<script src="js/jspdf.js"></script>
<script src="js/pdfFromHTML.js"></script>
<script src="js/jquery-latest.js"></script>
<script src="js/jquery.tablesorter.js"></script>
</head>
<!--background -->
<div style="background: white">
<img class="flagimgs first" src="CAS.png" width=100% height=10%>
<img class="flagimgs first" src="PS.png" width=500 height=150 style="background: white">
<p><font size="8">Supreme Court Coverage/Analytics Application</font></p></img></img>

<hr>
</div>
<body style=" height:100%; background: linear-gradient(0deg, rgb(153, 204, 255), rgb(255, 255, 255)) no-repeat;">
<?php
   
    include "search.php";
    mysqli_set_charset($connect, "utf8");
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
	   $sql = "SELECT date, source, author, title, article_text, url FROM article WHERE idArticle='{$search_term}'";
    	   //echo $sql;
          
          }
    $query = mysqli_query($connect, $sql) or die(mysqli_connect_error());
    
    ?>
<div class='container'>
<div class='content-wrapper'>
<div class='row'>
<div class='col-xs-3 col-md-3'>
<div id="rectangle" style="width:number px; height:number px; background-color:white; border-radius: 25px; padding: 20px; border: 2px solid #000000;">
<b><big><big><big>Details</big></big></big></b></br></br>
<b><big>Author</big></b></br>
<?php ($row = mysqli_fetch_array($query)); echo $row['author']; ?></br></br>
<b><big>Source</big></b></br>
<?php echo $row['source']; ?></br></br>
<b><big>Publication Date</big></b></br>
<?php echo $row['date']; ?></br></br>
<b><big><div id="dont-break-out" style="word-break: break-word; word-break: break-all; -ms-word-break: break-all; word-wrap: break-word; overflow-wrap: break-word;">URL</div></big></b>
<a href="<?php echo $row['url']; ?>"><?php echo substr($row['url'], 0, 30); echo"...";?></a></br></br>


</div>
</div>
<div class='col-xs-9 col-md-9 center-block'>


<div id="rectangle" style="width:number px; height:number px; background-color:white; border-radius: 25px; padding: 20px; border: 2px solid #000000;">
  

<b><big><?php echo $row['title']; ?></b></big></br>
<?php echo $row['date']; ?></br>
<?php echo nl2br($row['article_text']); ?></br>

</table>

</div>
</div>
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
</br></br></br></br></br></br></br></br></br></br>
</body>
         
             
</html>
