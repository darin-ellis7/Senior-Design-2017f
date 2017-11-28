<?php
    // does the same SQL search query as the one in search.php for any given search, but outputs the rows to a CSV

    // connect to database (or not)
    $connect = mysqli_connect("localhost", "root", "") or die(mysqli_connect_error());
    mysqli_set_charset($connect, "utf8");
    mysqli_select_db($connect, "SupremeCourtApp") or die(mysqli_connect_error());

    // base sql query - concatenate to as needed
    // the default CSV will include the entire database
    $sql = "SELECT date, title, source, article.idArticle, article.score, magnitude, entity, MAX(entity_instances.score) FROM article LEFT JOIN (image NATURAL JOIN image_entities NATURAL JOIN entity_instances) ON article.idArticle = image.idArticle ";
   
    // build sql query based on search criteria
    if(isset($_GET['search_query']))
    {
        if(isset($_GET['searchBy']))
        {
            $search_query = mysqli_real_escape_string($connect, $_GET['search_query']);
            if($_GET['searchBy'] == 'title')
            { 
                $sql .= "WHERE title LIKE '%$search_query%' "; 
            }
            else if($_GET['searchBy'] == 'source')
            { 
                $sql .= "WHERE source LIKE '%$search_query%' "; 
            }
            else if($_GET['searchBy'] == 'keyword')
            {
                // keywords require special query
                $sql = "SELECT DISTINCT title, date, source, article.idArticle, article.score, magnitude, entity, MAX(entity_instances.score) FROM (article NATURAL JOIN article_keywords NATURAL JOIN keyword_instances) LEFT JOIN (image NATURAL JOIN image_entities NATURAL JOIN entity_instances) ON article.idArticle = image.idArticle WHERE keyword LIKE '%$search_query%' ";
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

    $sql .= "GROUP BY article.idArticle"; // finish query string
    $query = mysqli_query($connect, $sql) or die(mysqli_connect_error()); // execute query

    // build CSV
    $fp = fopen('php://output', 'w');
    if ($fp && $query) 
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // column headers
        $arrName = array("Article ID", "Date", "Source", "Title","Sentiment Score","Sentiment Magnitude","Top Image Entity","Entity Score");
        fputcsv($fp, $arrName);

        // insert rows
        while ($row = mysqli_fetch_array($query)) 
        { 
    	   $arr = array($row['idArticle'],$row['date'], $row['source'], $row['title'], $row['score'],$row['magnitude'],$row['entity'],$row['MAX(entity_instances.score)']);

           fputcsv($fp, $arr);        
        }
    }

?>