<?php
    
    $connect = mysqli_connect("localhost", "root", "") or die(mysqli_connect_error());
    
    mysqli_select_db( $connect, "SupremeCourtApp") or die(mysqli_connect_error());
    
    ?>
