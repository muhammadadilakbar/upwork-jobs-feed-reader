<?php

require( "mysqli_connect.php");
$guid = $_POST['guid'];
$category = $_POST["category"];
$q = "UPDATE $category SET status='y' WHERE guid='$guid'";
$r = mysqli_query($dbc, $q); // Run the query.
$outputArray = array();
$affected_rows = mysqli_affected_rows($dbc);
if ( $affected_rows === 1) // if it ran OK
{
    $outputArray['success'] = 'yes';
    $outputArray['guid'] = $guid;
    echo json_encode( $outputArray );
}
else //Update action couldn't be performed. Notify the end user.
{
    $outputArray['success'] = 'no';
    $outputArray['guid'] = $guid;
    $error = sprintf( "System Error. The job could not be marked as read. We apologize for any inconveniene. %s", mysqli_error($dbc) );
    $outputArray['error'] = $error;
    $outputArray['query_result'] = $r;
    $outputArray['affected_rows'] = $affected_rows;
    echo json_encode( $outputArray );
}

?>