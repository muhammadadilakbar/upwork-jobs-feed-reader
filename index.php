<?php

/**
 * Copyright 2021-2099 Muhammad-Adil Akbar MIT License
 */

header( "Cache-Control: no-store" );
require( "mysqli_connect.php");
require( "utility_functions.php" );
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Upwork Feed Reader Index</title>
</head>
<body>
    <h1><a href="index.php">Homepage</a></h1>
    <p><a href="create_files.php">Create Files</a></p>
    <p><a href="loadjobs.php" target="_blank">Load Jobs</a></p>
    <p><a href="truncate_tables.php">Truncate Tables</a></p>
    <p><a href="delete_files_list.php">Delete Files</a></p>
    <h2>Feeds</h2>
<?php
$databaseTablesNames = get_table_names();
$flag = true;
foreach( $databaseTablesNames as $value )
{
    echo "<p><a href=\"readshowjobs_" . $value . ".php\" target=\"_blank\">" . $value . "</a></p>";
    $flag = false;
}
if( $flag )
{
    echo "No feed exists. Please click on Create Files link given above to create a feed.";
}
mysqli_close($dbc);
?>
</body>
</html>