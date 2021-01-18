jQuery( document ).ready( function() {
    $("div.description").hide();
    $("div.title").each( function() {
        let fetchedReadStatus = $( this ).attr( "data-read" );
        if( fetchedReadStatus === 'y' )
        {
            $( this ).children("span#jobtitle").css( { "color": "#888888", "font-weight": "normal" } );
        }
    }).on( "click", function() {
        $( this ).next().toggle();
        $( this ).children("span#jobtitle").css( { "color": "#888888", "font-weight": "normal" } );
        if( $(this).attr( "data-read" ) === 'n' )
        {
            let category = $( "h1" ).attr( "data-category" );
            let guidtext = $( this ).next().children("p.guid").text();
            let options = {
                url: 'markasread.php',
                type: 'post',
                data: { guid: guidtext, category: category },
                dataType: 'json',
                success: function( response ) {
                    updateDataRead( response );
                }
            }
            $.ajax(options);   
        }
    });
});

function updateDataRead( server_response ) {
    if ( server_response.success === 'yes')
    {
        $( "p.guid" ).each( function() {
            if( $(this).text() === server_response.guid )
            {
                $(this).parent().prev().attr( 'data-read', 'y');
                console.log('job successfully set as read');
            }
        })
        
    }
    else if( server_response.success === 'no' )
    {
        console.log( "An error occurred: " );
        console.log( server_response.error );
        console.log( "Query result: " + server_response.query_result );
        console.log( "Affected Rows: " + server_response.affected_rows );
    }
    else
    {
        console.log( "An unknown error occurred." );
    }
}