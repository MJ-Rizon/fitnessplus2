<?php
// Check if 'id' is set in the GET request
if (isset($_GET["id"])) {
    // Convert the 'id' parameter to an integer to prevent SQL injection and errors
    $id = intval($_GET["id"]);
    
    // Database connection credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database  = "fitnessplus";

    // Create a new connection to the MySQL database
    $connection = mysqli_connect($servername, $username, $password, $database);

    // SQL query to delete the coach with the specified id
    $sql = "DELETE FROM coach WHERE id=$id";
    // Execute the delete query
    $connection->query($sql);
}

// Redirect to the coach list page after deletion
header("location: /FITNESS/coach.php");
exit;
?>