<?php


_markersInit( ) ;

function _markersInit( ) {

    $markerPatterns = array( ) ;

    $markerPatterns[ "rematchtokenpre" ] = _configIniQuery( "rematchtokenpre" , "markerpatterns" ) ;
    $markerPatterns[ "rematchtokenpost" ] = _configIniQuery( "rematchtokenpost" , "markerpatterns" ) ;
    $markerPatterns[ "markerpre" ] = _configIniQuery( "markerpre" , "markerpatterns" ) ;
    $markerPatterns[ "markerpost" ] = _configIniQuery( "markerpost" , "markerpatterns" ) ;

    $markerPatterns[ "rematchtoken" ] = "/" . preg_quote( $markerPatterns[ "rematchtokenpre" ] , "/" ) . '.*?' . preg_quote( $markerPatterns[ "rematchtokenpost" ] , "/" ) . "/" ;
    $markerPatterns[ "markerregex" ] = "/" . preg_quote( $markerPatterns[ "markerpre" ] , "/" ) . '.*?' . preg_quote( $markerPatterns[ "markerpost" ] , "/" ) . "/" ;

    _configQuery( "markerpatterns" , $markerPatterns ) ;


    _configQuery( "embedfirstpassmarkersdata" , array( ) ) ;

    _configQuery( "passmadenochange" , false ) ;

}



function _markersGetOriginal( $str ) {

    $matches = _markersGetMatches( $str , _configBlockQuery( "markerpatterns" , "markerregex" ) ) ;

    $matchesHash = array( ) ;

    foreach( $matches as $match ) {

        if( isset( $matchesHash[ $match[ 0 ] ] ) ) {

            $matchesHash[ $match[ 0 ] ][ ] = $match[ 1 ] ;

        } else {

            $matchesHash[ $match[ 0 ] ] = array( $match[ 1 ] ) ;

        }

    }

    //print_r($matchesHash);exit;

    foreach( $matchesHash as $mhk => $mhv ) {

        $m = substr( $mhk , strlen( _configBlockQuery( "markerpatterns" , "markerpre" ) ) , -strlen( _configBlockQuery( "markerpatterns" , "markerpost" ) ) ) ;

        $mp = preg_split( '/\s+/' , trim( $m ) ) ;

        $timeDataParts = explode( "-" , $mp[ 0 ] ) ;

        $data = json_decode( base64_decode( $timeDataParts[ 1 ] ,true ) ) ;

        $originalMarker = _configBlockQuery( "markerpatterns" , "rematchtokenpre" ) . implode( " " , $data ) . _configBlockQuery( "markerpatterns" , "rematchtokenpost" ) ;

        //print_r( "[$originalMarker]\n" ) ;

        $matchesHash[ $mhk ][ ] = $originalMarker ;

    }

//    print_r($matchesHash);

    $buff = "" ;

    $lastCursor = 0 ;

    foreach( $matchesHash as $mhk => $mhv ) {

        //_log( $mhv[ 2 ] ) ;

        $buff = $buff . substr( $str , $lastCursor , $mhv[ 0 ] - $lastCursor ) ;
        $buff = $buff . $mhv[ 2 ] ;
        $lastCursor = $mhv[ 1 ] + strlen( $mhk ) ;

    }

    $buff = $buff . substr( $str , $lastCursor ) ;
    //print( "[$buff]\n" ) ; exit ;

    return( $buff ) ;

}


function _markersGetMatches( $str , $match ) {

    preg_match_all(

        $match ,
        $str ,
        $matches ,
        PREG_OFFSET_CAPTURE

    ) ;

//    print_r($matches);exit;

    if( isset( $matches[ 0 ] ) ) return( $matches[ 0 ] ) ;

    return( array( ) ) ;

}

