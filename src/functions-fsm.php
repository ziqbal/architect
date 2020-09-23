<?php

function _fsmInit( ) {

    $allfiles = _getFiles( "src/_fsm" , "php" , 1 ) ;

    //_log($allfiles);

    $modes = array( ) ;

    $modes_functions  = array( ) ;

    foreach( $allfiles as $file ) {

        $mode = $file[ "parent" ] ;

        $t1 = explode("-",$file["filename"]);
        if( ! ( ( count($t1) == 2 ) && ctype_digit($t1[0]) ) ) {
            $modes_functions[ $mode ][ ] = $file ;
            continue;
        }


        if( isset( $modes[ $mode ] ) ) {

            $modes[ $mode ][ ] = $file ;

        } else {

            // Fudge start index to be 1
            $modes[ $mode ][ 1 ] = $file ;

        }

    }

    _configQuery( "_fsm" , $modes ) ;
    _configQuery( "_fsm_functions" , $modes_functions ) ;

    // Fudge index to start at 1
    $fsmKeys = array_keys( $modes ) ;
    array_unshift( $fsmKeys , "" ) ;
    unset( $fsmKeys[ 0 ] ) ;

    _configQuery( "_fsmKeys" , $fsmKeys ) ;

    //_log( _configQuery( "_fsmKeys" ) ) ;

}

function _fsmGet( $mode ) {

    $res = array_search( $mode , _configQuery( "_fsmKeys" ) );

    return( $res ) ;

}




function _fsmGetState( $state ) {

    $db1 = debug_backtrace( ) ;
    $db2 = $db1[ 1 ] ;
    $db3 = pathinfo( $db2[ "args" ][ 0 ] ) ; 
    $db4 = $db3[ "filename" ] ;

    $dirParts = explode( "/" , $db3[ "dirname" ] ) ;

    $mode = array_pop( $dirParts ) ;

    $modes = _configQuery( "_fsm" ) ;

    $thisModeStates = $modes[ $mode ] ;

    foreach( $thisModeStates as $stateID => $stateBlob ) {

        if( substr( $stateBlob[ "filename" ] , -strlen( $state ) ) === $state ) {

            return( $stateID ) ;

            break ;

        }

    }

    return( 0 ) ;

}



