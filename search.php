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

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function() {
                    $("#results-table").DataTable({"searching":false, "order": [[2,"desc"]]});
                    $('.datebox').datepicker({clearBtn: true });
                  });
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

From: <input data-provide="datepicker" class="datebox" type="text" name="dateFrom" <?php if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo'])) { echo " value = '{$_GET['dateFrom']}'"; } ?> > 


To: <input data-provide="datepicker" class="datebox" type="text" name="dateTo" <?php if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo'])) { echo " value = '{$_GET['dateTo']}'";} ?> > 
</div>
</div>
</div>
</div>

<!--download button -->
<div align="right">
<?php 
if (isset($_GET['searchBy'])) 
{
    $downloadURL = "./download.php?searchBy=".$_GET['searchBy']."&search_query=".$_GET['search_query']."&dateFrom=".$_GET['dateFrom']."&dateTo=".$_GET['dateTo'];
}
else{
    $downloadURL = "./download.php?searchBy=title&search_query=";
}    
echo "<button class=\"btn btn-default\"><a style=\"color:black; text-decoration:none\" href=\""; echo $downloadURL; echo "\">Download Results</a></button> &nbsp;"; 
?>
</form></div></div>

<hr>

<body style=" height:100%; background: linear-gradient(0deg, rgb(153, 204, 255), rgb(208, 230, 255)) no-repeat;">

<?php
    // connect to database (or not)
    $connect = mysqli_connect("localhost", "root", "") or die(mysqli_connect_error());
    mysqli_set_charset($connect, "utf8");
    mysqli_select_db($connect, "SupremeCourtApp") or die(mysqli_connect_error());
    

    // base sql query
    // default search includes entire database
    $sql = "SELECT date, title, source, idArticle FROM article ";
   
    // build sql query based on search criteria
    if(isset($_GET['search_query']))
    {
        if(isset($_GET['searchBy']))
        {
            $search_query = mysqli_real_escape_string($connect, $_GET['search_query']);
            if($_GET['searchBy'] == 'title') // title search
            { 
                $sql .= "WHERE title LIKE '%$search_query%' "; 
            }
            else if($_GET['searchBy'] == 'source') // source search
            { 
                $sql .= "WHERE source LIKE '%$search_query%' "; 
            }
            else if($_GET['searchBy'] == 'keyword') // keyword search
            { 
                // keywords require special query
                $sql = "SELECT DISTINCT title, source, idArticle, date FROM article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances WHERE keyword LIKE '%$search_query%' "; 
            }
        }
    }

    // date range search - if no dates provided, ignore
    if(!empty($_GET['dateFrom']) && !empty($_GET['dateTo']))
    {
        // convert date input to Y-m-d format - this is because the bootstrap datepicker sends dates in Y/m/d while SQL only accepts as Y-m-d
    	$dateFrom = date("Y-m-d",strtotime($_GET['dateFrom']));
    	$dateTo = date("Y-m-d",strtotime($_GET['dateTo']));
        if(isset($_GET['search_query']))
        {
            $sql .= "AND date BETWEEN '$dateFrom' AND '$dateTo' ";
        }
        else
        {
            $sql .= "WHERE date BETWEEN '$dateFrom' AND '$dateTo' ";
        }
    }

    $sql .= "ORDER BY date DESC";
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

    <?php 
        // build search results table
        while ($row = mysqli_fetch_array($query)) 
        { 
            echo "<tr class='clickable-row' href='./display_article.php?idArticle="; echo $row['idArticle']; echo"'>";
                echo "<td><button class=\"btn btn-link\" style=\"color:black\"><a href=\"./display_article.php?idArticle="; echo $row['idArticle']; echo "\" style=\"color:black\">"; echo $row['title']; echo "</a></button></td>";
                echo "<td>&nbsp"; echo $row['source']; echo"</td>";
                echo "<td>"; echo $row['date']; echo "</td>";
            echo "</tr>"; 
        }
    ?>
</table>


</body>

<div style="height:200px"></div>
</div>

</html>
