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
    $sql = "SELECT date, title, source, idArticle, score, magnitude ";
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
            { $sql = "SELECT DISTINCT title, source, idArticle, date, score, magnitude FROM article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances WHERE keyword LIKE '%{$search_query}%' "; }
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
        $dateFrom = date("Y-m-d",strtotime($_GET['dateFrom']));
        $dateTo = date("Y-m-d",strtotime($_GET['dateTo']));
        if(isset($_GET['search_query']))
        {
            $sql .= "AND date BETWEEN '$dateFrom' AND '$dateTo'";
        }
        else
        {
            $sql .= "WHERE date BETWEEN '$dateFrom' AND '$dateTo'";
        }
    }
    
    $query = mysqli_query($connect, $sql) or die(mysqli_connect_error()); // execute query
    ?>

<?php
    
$fp = fopen('php://output', 'w');
    if ($fp && $query) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        $arrName = array("ID", "Date", "Source", "Title","Sentiment Score","Sentiment Magnitude");
        fputcsv($fp, $arrName);
        while ($row = mysqli_fetch_array($query)) { 
		 $arr = array($row['idArticle'],$row['date'], $row['source'], $row['title'], $row['score'],$row['magnitude']);
            fputcsv($fp, $arr);        }
    }
?>
