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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/jquery.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<script src="js/jspdf.js"></script>
<script src="js/pdfFromHTML.js"></script>

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
                    $("#results-table").DataTable({"searching":false, "order": [[2,"desc"]]});
                  } );
</script>
</head>
<!--background -->
<div style="background: white">
<img class="flagimgs first" src="CAS.png" width=100% height=10%>
<img class="flagimgs first" src="PS.png" width=500 height=150 style="background: white">
<p><font size="8">&nbsp;Supreme Court Coverage/Analytics Application</font></p></img></img>

<hr>
</div>
<!-- search bar + options -->
<div class='container'>
<div class='content-wrapper'>
<div class='row'>
<div class='col-xs-12 col-sm-12 col-md-5 col-lg-8 center-block'>
</div>
<div class='navbar-form' align="center"><form action='' method='GET'>

Search by:
<?php
    // sets the checked criteria on the results of a search based on what the user selected before searching
    $values = ['title','source','keyword'];
    foreach($values as $v)
    {
        echo "<input type='radio' name='searchBy' value=$v";
       
            if((isset($_GET['searchBy']) && $_GET['searchBy'] == $v) || (!isset($_GET['searchBy']) && $v == 'title'))
            {
                echo " checked = 'checked'";
            }
        
        
        echo "> " . ucfirst($v) . " ";   
    }

?>

<br>
<br>

<!-- php code within these input tags are to remember user input after search is done -->
<span class="input-group-btn">
<input class='form-control' type="text" name="search_query" style="width: 430px !important;" placeholder='Type search query here... (leave empty to see all)' <?php if(isset($_GET['search_query'])) echo " value='{$_GET['search_query']}'"; ?>          />

<button type='submit' class='btn btn-default'>
<span class='glyphicon glyphicon-search'></span>

</button>
</span>
<br>
From: <input type="date" name="dateFrom" <?php if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo'])) { echo " value = '{$_GET['dateFrom']}'"; } ?> > 
To: <input type="date" name="dateTo" <?php if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo'])) { echo " value = '{$_GET['dateTo']}'";} ?> > 
</div>
</div>
</div>
</div>
<!--download the table as PDF -->
<div align="right">
<?php 
if (isset($_GET['searchBy'])) {
    $csvURL = "./CSV.php?searchBy=".$_GET['searchBy']."&search_query=".$_GET['search_query']."&dateFrom=".$_GET['dateFrom']."&dateTo=".$_GET['dateTo'];
}
else{
    $csvURL = "./CSV.php?searchBy=title&search_query=";
}    
echo "<button class=\"btn btn-default\"><a style=\"color:black; text-decoration:none\" href=\""; echo $csvURL; echo "\">Download CSV</a></button> &nbsp;"; 
?>
<button class="btn btn-default" href="#" onclick="HTMLtoPDF()">Download PDF</button>
</form></div></div>

<hr>

<body style=" height:100%; background: linear-gradient(0deg, rgb(153, 204, 255), rgb(208, 230, 255)) no-repeat;">

<?php
    // connect to database but need limit $page1,10
    $connect = mysqli_connect("localhost", "root", "") or die(mysqli_connect_error());
    mysqli_set_charset($connect, "utf8");
    mysqli_select_db($connect, "SupremeCourtApp") or die(mysqli_connect_error());
    $page=(isset($_GET["page"]));
    if($page=="" || $page=="1")
    {
        $page1=0;
    }
    else
    {
        $page1=($page*10)-10;
    }
    // base sql query
    $sql = "SELECT date, title, source, idArticle ";
    //paging the table
   
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
            { $sql = "SELECT DISTINCT title, source, idArticle, date FROM article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances WHERE keyword LIKE '%{$search_query}%' "; }
            else
            { $sql .= "FROM article "; }
        }
    }
    else // default search yields all articles
    {
        $sql .= "FROM article ";
    }

    // date range search
    if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo']))
    {
        if(isset($_GET['search_query']))
        {
            $sql .= "AND date BETWEEN '{$_GET['dateFrom']}' AND '{$_GET['dateTo']}'";
        }
        else
        {
            $sql .= "WHERE date BETWEEN '{$_GET['dateFrom']}' AND '{$_GET['dateTo']}'";
        }
    }

    $sql .= " ORDER BY date DESC";
    $query = mysqli_query($connect, $sql) or die(mysqli_connect_error()); // execute query
    ?>

<!-- display query results as table -->
<div id="HTMLtoPDF" class="col-sm-12">
<table   id="results-table" style="background-color: white" width="90%" class="stripe hover"  align="center">
<thead>
<tr align="center">
<td><strong>Title</strong></td>
<td><strong>Source</strong></td>
<td><strong>Date</strong></td>
</tr>
</thead>
<style>
a {
color: black;
}
a:visited {
color: black;
    background-color: transparent;
    text-decoration: none;
}
a:hover {
color: black;
    background-color: transparent;
    text-decoration: underline;
}
a:active {
color: blue;
}
</style>


<?php 
$color = "#B2E4FF";
while ($row = mysqli_fetch_array($query)) { 
if ($color == "#B2E4FF"){
echo "<tr bgcolor = \"#B2E4FF\" class='clickable-row' href='./display_article.php?idArticle="; echo $row['idArticle']; echo"'>";
echo "<td><button class=\"btn btn-link\" style=\"color:black\"><a href=\"./display_article.php?idArticle="; echo $row['idArticle']; echo "\" style=\"color:black\">"; echo $row['title']; echo "</a></button></td>";
echo "<td>&nbsp"; echo $row['source']; echo"</td>";
echo "<td>"; echo $row['date']; echo "</td>";
echo "</tr>"; 
$color = "#75CEFF"; }
else {
echo "<tr bgcolor = \"#FFFFFF\" class='clickable-row' data-href='./display_article.php?idArticle="; echo $row['idArticle']; echo"'>";
echo "<td><button class=\"btn btn-link\" style=\"color:black\"><a href=\"./display_article.php?idArticle="; echo $row['idArticle']; echo "\" style=\"color:black\">"; echo $row['title']; echo "</a></button></td>";
echo "<td>&nbsp"; echo $row['source']; echo"</td>";
echo "<td>"; echo $row['date']; echo "</td>";
echo "</tr>"; 
$color = "#B2E4FF"; }
 }
?>

<?php
  
    while($row = mysqli_fetch_array($query))
    {
        $title = $row['title'];
        $source = $row['source'];
        $date = $row['date'];
        echo "<tr> <td><a>$title</a></td> <td>$source</td> <td>$date</td> </tr>";
    }
    
    ?>
</table>


</body>

<div style="height:200px"></div>
</div>

</html>
