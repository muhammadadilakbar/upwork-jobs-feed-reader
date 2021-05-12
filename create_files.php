<?php
require( "mysqli_connect.php" );
require( "utility_functions.php" );
require( "core_functions.php" );

if( $_SERVER["REQUEST_METHOD"] == "POST" )
{
    $database_table_name = "";
    $h1_heading = "";
    $rss_link = "";
    $errors = "";
    //if( !check_if_database_already_exists() ) //if the database named "upwork" doesn't exists, then create the database with the same name
    //    create_new_database();
    if( !empty( $_POST["database_table_name"] ) )
        $database_table_name = $_POST["database_table_name"];
    else
    {
        $database_table_name = NULL;
        $errors = $errors . "<p>You forgot to enter database table name.</p>";
    }
    if( !empty( $_POST["h1_heading"] ) )
        $h1_heading = $_POST["h1_heading"];
    else
    {
        $h1_heading = NULL;
        $errors = $errors . "<p>You forgot to enter h1 heading</p>";
    }
    if( !empty( $_POST["rss_link"] ) )
        $rss_link = $_POST["rss_link"];
    else
    {
        $rss_link = NULL;
        $errors = $errors . "<p>You forgot to enter RSS link.</p>";
    }
    if( $database_table_name && $h1_heading && $rss_link ) //if everything is ok
    {
        create_database_table( $database_table_name );
        create_read_feed_file( $database_table_name, $h1_heading, $rss_link );
        create_read_show_jobs_file( $database_table_name, $h1_heading, $rss_link );
        create_load_jobs_file();
        create_truncate_tables_file();
        create_delete_files_list_file();
        create_delete_files_file();
    }
}
?><!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf8" />
    <title>Create Files</title>
</head>
<body>
    <h1>Create Files</h1>
    <p><a href="index.php">Homepage</a></p>
    <form action="create_files.php" method="POST">
		<p><label>Database table name (e.g. uw_figma): <input type="text" name="database_table_name" /></label></p>
        <p><label>H1 heading (e.g. WordPress OR WooCommerce): <input type="text" name="h1_heading" /></label></p>
		<p><label>RSS Link: <input type="text" name="rss_link" /></label></p>
        <p><input type="submit" name="submit" value="Submit"></p>
        <?php
        if( !empty( $errors ) )
            echo $errors;
        mysqli_close($dbc);
        ?>
    </form>
</body>
</html>