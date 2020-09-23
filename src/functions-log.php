<?php

function _logExit( $msg , $key = "DEBUG" ) {


    _log( $msg , $key ) ;

    print( time( ) . ",ERROR, $msg\n" ) ;

    exit( 1 ) ;

}


function _log( $msg , $key = "DEBUG" ) {

    static $firsttime = true ;

    if( is_array( $msg ) ) {

        $msg = print_r( $msg , true ) ;
        
    }


    if( !file_exists( _configQuery( "_log" )  ) ) {

        file_put_contents( _configQuery( "_log" ) , "" , FILE_APPEND | LOCK_EX ) ;

        chmod( _configQuery( "_log" ) , 0777 ) ;

    }


    $prefix = time( ) . "," . _configQuery( "_spid" ) . "-" . _configQuery( "_pid" ) . ",$key" ;

    if( $firsttime ) {

        file_put_contents( _configQuery( "_log" ) , "\n$prefix," . date( 'l jS \of F Y h:i:s A' ) . "\n" , FILE_APPEND | LOCK_EX ) ;
        $firsttime = false ;

    }


    if( $msg == "" ) $msg = "[!!!BLANK!!!]" ;


    file_put_contents( _configQuery( "_log" ) , "$prefix,$msg\n" , FILE_APPEND | LOCK_EX ) ;

}
