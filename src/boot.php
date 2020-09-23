<?php

include( "functions-config.php" ) ;
include( "functions-shutdown.php" ) ;
include( "functions-log.php" ) ;
include( "functions-markers.php" ) ;
include( "functions-fs.php" ) ;
include( "functions-parse.php" ) ;
include( "functions-fsm.php" ) ;
include( "functions-unsorted.php" ) ;

//////////////////////////////

_fsDeleteTarget( ) ;

//////////////////////////////

$_buff = _parseInput( ) ;

//_configDebug( ) ;

//passmadenochange


$postHook = _configIniQuery( "post" , "hooks" ) ;

if( $postHook != "" ) {

    if( file_exists( $postHook ) ) {
        
        $_BUFF_ = $_buff ;
        include( $postHook ) ;
        //_log("$postHook");
        $_buff = $_BUFF_ ;

    } else { 

        _logExit( "$postHook" ) ;

    }

}

_fsSaveTarget( $_buff ) ;

