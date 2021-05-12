<?php

function curlGet($url) // Function to make GET request using cURL
{
    $ch = curl_init(); // Initialising cURL session
    // Setting cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //curl_setopt($ch, CURLOPT_SSL_OPTIONS, CURLSSLOPT_NO_REVOKE); //these three commented lines were a temporary fix for certificate verification problem on Windows 10
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    $results = curl_exec($ch); // Executing cURL session
    curl_close($ch); // Closing cURL session
    return $results; // Return the results
}

function create_new_database()
{
    global $dbc;

    $q1 = "CREATE DATABASE upwork CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    mysqli_query($dbc, $q1); // Run the query.

    if( mysqli_affected_rows($dbc) == 1 )
    {
        echo "Database created successfully. <br />\n";
    }
    else
    {
        echo "Database creation failed. <br />\n";
        echo "Error description: " . mysqli_error($dbc) . "<br />\n";
    }
}

function create_database_table( $table_name )
{
	global $dbc;
	$q = "CREATE TABLE " . $table_name . " ( id INT UNSIGNED NOT NULL AUTO_INCREMENT, title VARCHAR(500) NOT NULL, link VARCHAR(1000) NOT NULL, description TEXT, pubdate CHAR(31), date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP, guid_complete VARCHAR(1000), guid CHAR(20) NOT NULL, status CHAR(1), PRIMARY KEY (id) ) ENGINE=INNODB";
	if( mysqli_query($dbc, $q) ) //execute the SQL query
	{
		echo "Table " . $table_name . " created successfully. <br />\n";
	}
	else
	{
		echo "Table " . $table_name . " creation failed. <br />\n";
		echo "Error description: " . mysqli_error($dbc) . "<br />\n";
	}
}

function check_if_database_already_exists()
{
    global $dbc;
    $q = "SHOW DATABASES";
    $r = mysqli_query( $dbc, $q );
    $num = mysqli_num_rows( $r ); // Count the number of returned rows
    if ($num > 0) // If it ran OK
    {
        while( $row = mysqli_fetch_array($r, MYSQLI_ASSOC) )
        {
            if( $row["Database"] === DB_NAME )
            {
                return true;
            }
        }
    }
    return false;
}

function truncate_database_table( $table_name )
{
	global $dbc;
	$q = "TRUNCATE TABLE " . $table_name;
	if( mysqli_query($dbc, $q) )
	{
		echo "Table " . $table_name . " truncated successfully. <br />\n";
	}
	else
	{
		echo "Table " . $table_name . " truncation failed. <br />\n";
		echo "Error description: " . mysqli_error($dbc) . "<br />\n";
	}
}

function delete_files( $table_name )
{
	global $dbc;
	$q = "DROP TABLE " . $table_name;
	if( mysqli_query($dbc, $q) )
	{
		echo "<br />Table " . $table_name . " deleted successfully. <br />\n";
	}
	else
	{
		echo "Table " . $table_name . " deletion failed. <br />";
		echo "Error description: " . mysqli_error($dbc) . "<br />";
    }
    if( unlink( "readfeed_" . $table_name . ".php" ) )
        echo "File readfeed_" . $table_name . ".php deleted successfully<br />";
    else
        echo "File readfeed_" . $table_name . ".php couldn't be deleted<br />";
    if( unlink( "readshowjobs_" . $table_name . ".php" ) )
        echo "File readshowjobs_" . $table_name . ".php deleted successfully<br />";
    else
        echo "File readshowjobs_" . $table_name . ".php couldn't be deleted<br />";  
}

function get_table_names()
{
    global $dbc;
    $databaseTablesNames = array(); //initialize array
    $q = "SHOW TABLES";
    $r = mysqli_query( $dbc, $q ); //run the query, store the returned rows in $r
    $num = mysqli_num_rows($r); // Count the number of returned rows
    if ($num > 0) // If it ran OK, store the table names in $databaseTablesNames array.
    {
        while( $row = mysqli_fetch_array($r, MYSQLI_NUM) )
        {
            $databaseTablesNames[] =  $row[0];
        }
    }
    mysqli_free_result($r);
    return $databaseTablesNames;
}

function get_relative_url($file = 'index.php')
{
	// URL is http:// plus the host name plus the current directory:
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	$url = rtrim($url, '/\\'); // Remove any trailing slashes:
	$url .= '/' . $file; // Add the page:
	return $url;
}