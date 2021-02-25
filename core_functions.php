<?php

function create_read_feed_file( $database_table_name, $h1_heading, $rss_link )
{
    $readFeedFileName = "readfeed_" . $database_table_name . ".php";
    $readFeedFP = fopen( $readFeedFileName, "w" ) OR die("Unable to open file for writing");
    $output = "";
    $output = "<?php\nrequire(\"utility_functions.php\");\n";
    $output = $output . "\$res = curlGet(\"" . $rss_link . "\");\n";
    $output = $output . <<<'MARK'
    require( "mysqli_connect.php");
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = FALSE;
    $doc->loadXML($res);
    $allItems = $doc->getElementsByTagName("item");
    $records_received = $allItems->count(); //returns int
    $records_added = 0;
    for( $i = (($records_received) - 1); $i >= 0; $i-- )
    {
        $item = $allItems->item($i);
        $children = $item->childNodes;
        $title = mysqli_real_escape_string( $dbc, $children->item(0)->nodeValue );
        $link = mysqli_real_escape_string( $dbc, $children->item(1)->nodeValue );
        $description = mysqli_real_escape_string( $dbc, $children->item(2)->nodeValue );
        $pubDate = mysqli_real_escape_string( $dbc, $children->item(4)->nodeValue );
        $guid_complete = mysqli_real_escape_string( $dbc, $children->item(5)->nodeValue );
        $guid = substr( $guid_complete ,-31, 20);
        
        $q = "SELECT title, link, description FROM upwork.
    MARK;
    $output = $output . $database_table_name . " ";
    $output = $output . <<<'MARK'
    WHERE guid='$guid'";
        $r = mysqli_query($dbc, $q); // Run the query.
        $num = mysqli_num_rows($r); // Count the number of returned rows
        mysqli_free_result($r);
        if( $num > 0 ) //if the job already exists in DB table, then do nothing.
        {
            // do nothing
        }
        else //it's a new job. Insert it into the DB table.
        {
            $q2 = "INSERT INTO upwork.
    MARK;
    $output = $output . $database_table_name . " ";
    $output = $output . <<<'MARK'
    (title,link,description,pubdate,guid_complete,guid,status) VALUES ('$title', '$link', '$description', '$pubDate', '$guid_complete', '$guid', 'n')";
            mysqli_query($dbc, $q2); // Run the query.
            if (mysqli_affected_rows($dbc) == 1) // if it ran OK
            {
                $records_added++;
            }
            else //job couldn't be inserted. Notify the end user.
            {
                echo '<p>System Error</p>
                <p>The job could not be entered into the database. We apologize for any inconvenience.</p>';
                echo '<p>' . mysqli_error($dbc) . '<p>';
            }
        }
    }
    mysqli_close($dbc);
    echo "
    MARK;
    $output = $output . $h1_heading;
    $output = $output . <<<'MARK'
     Records Added: " . $records_added;
    MARK;

    if( fwrite( $readFeedFP, $output ) )
        echo "<br />File: " . $readFeedFileName . " updated successfully";
}

function create_read_show_jobs_file( $database_table_name, $h1_heading, $rss_link )
{
    $readShowJobsFileName = "";
    $readShowJobsFileName = "readshowjobs_" . $database_table_name . ".php";
    $readShowJobsFP = fopen( $readShowJobsFileName, "w" ) OR die("Unable to open file for writing");
    $outputShowJobs = "";
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title>
    MARK;
    $outputShowJobs = $outputShowJobs . $h1_heading;
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    </title>
        <link rel="stylesheet" href="styles.css" />
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    </head>
    <body>
    <h1 data-category="
    MARK;
    $outputShowJobs = $outputShowJobs . $database_table_name . "\">{$h1_heading}</h1>\n";
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    <?php
    require( "mysqli_connect.php");
    $display = 300; //number of records to show per page
    // Determine how many pages there are...
    if (isset($_GET['p']) && is_numeric($_GET['p']))
    { // Already been determined.
        $pages = $_GET['p'];
    }
    else // Need to determine.
    {
        // Count the number of records:
        $q = "SELECT COUNT(id) FROM upwork.
    MARK;
    $outputShowJobs = $outputShowJobs . $database_table_name;
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    ";
        $r = @mysqli_query($dbc, $q);
        $row = @mysqli_fetch_array($r, MYSQLI_NUM);
        $records = $row[0];
        // Calculate the number of pages...
        if ($records > $display) // More than 1 page.
        {
            $pages = ceil ($records/$display);
        }
        else
        {
            $pages = 1;
        }
    }
    // Determine where in the database to start returning results...
    if (isset($_GET['s']) && is_numeric($_GET['s']))
    {
        $start = $_GET['s'];
    }
    else
    {
        $start = 0;
    }
    $q = "SELECT id,title,description,pubdate,date_added,guid,status FROM upwork.
    MARK;
    $outputShowJobs = $outputShowJobs . $database_table_name . " ";
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    ORDER BY id DESC LIMIT $start, $display";
    $r = mysqli_query($dbc, $q);
    $num = mysqli_num_rows($r); // Count the number of returned rows
    if ($num > 0) // If it ran OK, display the records.
    {
        echo "<div>";
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC))
        {
            echo "<div class=\"title\" data-read=\"{$row["status"]}\">
                <span id=\"jobtitle\">" . $row["title"] . "</span><span id=\"jobpubdate\">" . $row["pubdate"] . "</span></div>\n";
            echo "<div class=\"description\">\n";
            echo "<p><strong>ID: </strong>{$row['id']}</p>\n";
            echo "<p><strong>Description: </strong>{$row['description']}</p>\n";
            echo "<p><strong>Pub Date: </strong>{$row['pubdate']}</p>\n";
            echo "<p class=\"guid\">". $row['guid'] . "</p>\n";
            echo "</div><!-- div.description ends -->\n";
        }
        echo "</div>";
    }
    mysqli_free_result($r);
    mysqli_close($dbc);
    if ($pages > 1)
    {
        echo "<br><p>";
        // Determine what page the script is on:
        $current_page = ($start/$display) + 1;
        // If it's not the first page, make a Previous link:
        if ($current_page != 1) {
            echo '<a href="
    MARK;
    $outputShowJobs = $outputShowJobs . $readShowJobsFileName;
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    ?s=' . ($start - $display) . '&p=' . $pages . '">Previous</a> ';
        }
        // Make all the numbered pages:
        for ($i = 1; $i <= $pages; $i++) {
            if ($i != $current_page) {
                echo '<a href="
    MARK;
    $outputShowJobs = $outputShowJobs . $readShowJobsFileName;
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    ?s=' . (($display * ($i - 1))) . '&p=' . $pages . '">' . $i . '</a> ';
            } else {
                echo $i . ' ';
            }
        }
        // If it's not the last page, make a Next button:
        if ($current_page != $pages) {
            echo '<a href="
    MARK;
    $outputShowJobs = $outputShowJobs . $readShowJobsFileName;
    $outputShowJobs = $outputShowJobs . <<<'MARK'
    ?s=' . ($start + $display) . '&p=' . $pages . '">Next</a>';
        }
        echo '</p>';
    } // End of links section.
    ?>
    <script src="code.js" type="text/javascript"></script>
    </body>
    </html>
    MARK;
    if( fwrite( $readShowJobsFP, $outputShowJobs ) )
        echo "<br />File: " . $readShowJobsFileName . " updated successfully.";
}

function create_load_jobs_file()
{
    global $dbc;
    $loadJobsFP = fopen( "loadjobs.php", "w" ) OR die("Unable to open file for writing");
    $output = "";
    $output = $output . <<<'MARK'
    <?php
    require( "utility_functions.php" );

    MARK;
    $databaseTablesNames = "";
    $databaseTablesNames = get_table_names();
    foreach( $databaseTablesNames as $value )
    {
        $url = get_relative_url( "readfeed_" . $value . ".php" );
        $output = $output . "\$res = curlGet( \"" . $url . "\");\n";
        $output = $output . "echo \"\$res <br />\\n\";\n";
    }
    if( fwrite( $loadJobsFP, $output ) )
        echo "<br />File: loadjobs.php updated successfully.";
}

function create_truncate_tables_file()
{
    global $dbc;
    $truncateTablesFP = fopen( "truncate_tables.php", "w" ) OR die("Unable to open file for writing");
    $databaseTablesNames = get_table_names();
    $htmlOP = "";
    $codeOP = "";
    foreach( $databaseTablesNames as $value )
    {
        $htmlOP = $htmlOP . "\n\t<p><label>" . $value . ": <input type=\"checkbox\" name=\"" . $value . "\" value=\"" . $value . "\" /></label></p>";
        $codeOP = $codeOP . "\n\tif( isset( \$_POST[\"" . $value . "\"] ) )
        {
            truncate_database_table( \"" . $value . "\" );
            \$flag = true;
        }";
    }
    $htmlOP = $htmlOP . "\n\t<p><input type=\"submit\" name=\"submit\" value=\"Submit\"></p>";
    $codeOP = $codeOP . "\n\tif( \$flag == false )
	{
		echo( \"nothing to do\" );
	}";

    $output = "";
    $output = $output . <<<'MARK'
    <?php
    require( "utility_functions.php" );
    require( "mysqli_connect.php" );
    if( $_SERVER["REQUEST_METHOD"] == "POST" )
    {
        $flag = false;
    MARK;
    $output = $output . $codeOP;
    $output = $output . <<<'MARK'
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title>Truncate Database Tables</title>
    </head>
    <body>
        <h1>Truncate Database Tables</h1>
        <p>Please note that this doesn't deletes the database table. It only delete all the rows in the database table.</p>
        <p><a href="index.php">Homepage</a></p>
        <form action="truncate_tables.php" method="POST">
    MARK;
    $output = $output . $htmlOP;
    $output = $output . <<<'MARK'
        </form>
    </body>
    </html>
    MARK;

    
    if( fwrite( $truncateTablesFP, $output ) )
        echo "<br />File: truncate_tables.php updated successfully.";
}

function create_delete_files_list_file()
{
    global $dbc;
    $deleteFilesListFP = fopen( "delete_files_list.php", "w" ) OR die("Unable to open file for writing");
    $databaseTablesNames = get_table_names();
    $htmlOP = "";
    foreach( $databaseTablesNames as $value )
    {
        $htmlOP = $htmlOP . "\n\t<p><label>" . $value . ": <input type=\"checkbox\" name=\"" . $value . "\" value=\"" . $value . "\" /></label></p>";
    }
    $htmlOP = $htmlOP . "\n\t<p><input type=\"submit\" name=\"submit\" value=\"Submit\"></p>";

    $output = "";
    $output = $output . <<<'MARK'
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title>Delete Files</title>
    </head>
    <body>
        <h1>Delete Files</h1>
        <p><a href="index.php">Homepage</a></p>
        <form action="delete_files.php" method="POST">
        <p>Please note that this deletes the files readfeed_example.php and readshowjobs_example.php.</p>
        <p>It also drops (deletes) the table from database.</p>
    MARK;
    $output = $output . $htmlOP;
    $output = $output . <<<'MARK'
        </form>
    </body>
    </html>
    MARK;

    if( fwrite( $deleteFilesListFP, $output ) )
        echo "<br />File: delete_files_list.php updated successfully.";
}

function create_delete_files_file()
{
    global $dbc;
    $deleteFilesFP = fopen( "delete_files.php", "w" ) OR die("Unable to open file for writing");
    $databaseTablesNames = get_table_names();
    $codeOP = "";
    foreach( $databaseTablesNames as $value )
    {
        $codeOP = $codeOP . "\n\tif( isset( \$_POST[\"" . $value . "\"] ) )\n\t{\n\t\tdelete_files( \"" . $value . "\" );\n\t\t\$flag = true;\n\t}\n";
    }

    $output = "";
    $output = $output . <<<'MARK'
    <?php
    require( "utility_functions.php" );
    require( "mysqli_connect.php" );
    require( "core_functions.php" );
    echo "<a href=\"index.php\">Homepage</a><br />";
    if( $_SERVER["REQUEST_METHOD"] == "POST" )
    {
        $flag = false;
    MARK;
    $output = $output . $codeOP;
    $output = $output . <<<'MARK'
        if( $flag == false )
        {
            echo( "Nothing to do!" );
        }
        if( $flag == true )
        {
            create_delete_files_file();
            create_delete_files_list_file();
            create_truncate_tables_file();
            create_load_jobs_file();
        }
    }
    MARK;

    if( fwrite( $deleteFilesFP, $output ) )
        echo "<br />File: delete_files.php updated successfully.";
}