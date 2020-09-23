<?php


function _getOutputFromSinglePass( $str ) {

    if( _configIsset( "_passcounter" ) ) {

        _configQuery( "_passcounter" , _configQuery( "_passcounter" ) + 1 ) ;

    } else {

        _configQuery( "_passcounter" , 1 ) ;

    }

    _log( __FUNCTION__ . _configQuery( "_passcounter" ) ) ;

    $matches = _markersGetMatches( $str , _configBlockQuery( "markerpatterns" , "rematchtoken" ) ) ;

    _configQuery( "passmadenochange" , true ) ; 

    foreach( $matches as $match ) {

        _configQuery( "passmadenochange" , false ) ; 
        _log( $match[ 0 ] ) ;

    }

    $splices = _getSplices( $matches ) ;

    $buff = _splicesProcess( $str , $splices ) ;

    $buff = implode( "" , $buff ) ;

    return( $buff ) ;

}


function _getSplices( $matches ) {

    $splices = array( ) ;

    foreach( $matches as $matchKey => $match ) {

        $m = substr( $match[ 0 ] , strlen( _configBlockQuery( "markerpatterns" , "rematchtokenpre" ) ) , -strlen( _configBlockQuery( "markerpatterns" , "rematchtokenpost" ) ) ) ;
        $mp = preg_split( '/\s+/' , trim( $m ) ) ;

        $mpData = base64_encode( json_encode( $mp ) ) ;
        $matchName = strtolower( array_shift( $mp ) ) ;

        _configQuery( "_matchparameters" , $mp ) ;
        _configQuery( "_matchname" , $matchName ) ;

        /////////

        $incFile = "src/{$matchName}.php" ;

        if( file_exists( $incFile ) ) {

            $content = _getIncFile( $incFile ) ;

        } else {

            $content = "\n//? $incFile ?//\n" ;

            print( "[$incFile] does not exist...\n" ) ;

            if( _configQuery( "_createblanks" ) ) {

                system( "touch $incFile" ) ;

            } else {

                print( "Use -c to autocreate\n" ) ;

            }
     
        }

        $splices[ ] = array( $match[ 1 ] , strlen( $match[ 0 ] ) , $content , $mpData ) ;

    }   

    return( $splices ) ;

}

function _getIncFile( $f ) {




    ob_start( ) ;
    ob_flush( ) ;

    include( $f ) ;

    $content = ob_get_contents( ) ;

    ob_end_clean( ) ;
/*
  $prefix = _configIniQuery( "prefix" , "output" ) ;

  if($prefix===null){
    $prefix="";
  }else{
    $prefix="${prefix}_";
  }
*/

    $prefix = _configQuery( "_basename" ) . "_" ;


    $content = str_replace( "___" ,  $prefix . _configQuery( "_matchname" ) . "_" , $content ) ;

    return( $content ) ;

}

function _splicesProcess( $str , $splices ) {


    $buff = array( ) ;

    $splicePreviousEnd = 0 ;

    foreach( $splices as $splice ) {

        //_log(base64_decode($splice[3]));

        $buff[ ] = substr( $str , $splicePreviousEnd , $splice[ 0 ] - $splicePreviousEnd ) ;

        if( _configQuery( "_passcounter" ) == 1 ) {

            $firstpassMarkerID = uniqid( ) . "-" . $splice[ 3 ] ;

             if( _configIniQuery( "addreembedmarker" , "markeroptions")=="true" ) {
            $buff[ ] = _configBlockQuery( "markerpatterns" , "markerpre" ) . " " . $firstpassMarkerID . " " .  _configBlockQuery( "markerpatterns" , "markerpost" ) ;
            }

            if( _configIniQuery( "embedfirstpassmarkers" , "markeroptions")=="true" ) {


                $data = json_decode(base64_decode($splice[3]),true);
                //_log($data);
                //$buff[ ] = $_CONFIG[ "rematchtokenpre" ] . implode(" ",$data).$_CONFIG[ "rematchtokenpost" ];
                $t1 = _configBlockQuery( "markerpatterns" , "rematchtokenpre" ) . implode(" ",$data)._configBlockQuery( "markerpatterns" , "rematchtokenpost" );
                //_log($t1);

                $reembedid = "REEMBED" . uniqid( ) ;

                _configPush( "embedfirstpassmarkersdata" , $reembedid , $t1 ) ;

                $buff[ ] = $reembedid ;

            } 

            $buff[ ] = $splice[ 2 ] ;

             if( _configIniQuery( "addreembedmarker" , "markeroptions")=="true" ) {

            $buff[ ] = "\n" . _configBlockQuery( "markerpatterns" , "markerpre" ) . " " . $firstpassMarkerID . " " . _configBlockQuery( "markerpatterns" , "markerpost" ) ;
            }

        } else{

            $buff[ ] = $splice[ 2 ] ;

        }

        $splicePreviousEnd = $splice[ 0 ] + $splice[ 1 ] ;

    }

    $buff[ ] = substr( $str , $splicePreviousEnd ) ;

    return( $buff ) ;

}


function _getMarker( $name , $params = "" ) {


    $t  = _configBlockQuery( "markerpatterns" , "rematchtokenpre" ) ;
    $t .= strtoupper( $name )." {$params}" ;
    $t .= _configBlockQuery( "markerpatterns" , "rematchtokenpost" ) ;


    return( $t ) ;

}


function _outputMarkerIfConfigParameterNotSet( $p ) {


    if( !_configIsset( $p ) ) {

        print( _getMarker( _configQuery( "_matchname" ) ) ) ;

        return( true ) ;
    }    

    return( false ) ;

}

function _getFiles( $inpath , $ext = "" , $depth = 0 ) {

    $path=$inpath;
    if( substr( $path , -1 ) == "/" ) {

        $path = substr( $path , 0 , -1 ) ;

    }

    $dir = new RecursiveDirectoryIterator( $path , FilesystemIterator::SKIP_DOTS ) ;

    $it  = new RecursiveIteratorIterator( $dir , RecursiveIteratorIterator::SELF_FIRST ) ;

    $it->setMaxDepth( $depth ) ;

    $files = array( ) ;

    $fileCounter = 1 ;

    foreach( $it as $fileinfo ) {

        if( $fileinfo->isDir( ) ) {

            //_log( "DIR " . $fileinfo->getFilename( ) ) ;

        } elseif( $fileinfo->isFile( ) ) {

            //_log("FILE ".$it->getSubPath().$fileinfo->getFilename());

            $pi = pathinfo( $path . "/" . $it->getSubPath( ) . "/" . $fileinfo->getFilename( ) ) ;

            $pi[ "path" ] = $pi[ "dirname" ]."/".$pi[ "basename" ];

            $dirParts = explode( "/" , $pi[ "dirname" ] ) ;
            $pi[ "parent" ] = array_pop( $dirParts ) ;
            $pi[ "counter" ] = $fileCounter ;
            $fileCounter++;

            if( $pi[ "extension" ] == $ext  && ( $pi[ "filename" ] != "blank" ) ) {

                $files[ $pi[ "filename" ] . "_" . $fileCounter ] = $pi ;

            }

        }

    }

    ksort( $files ) ;

    return( $files ) ;


}

function _getStartPHPTag( ) {

    return( "<?php" ) ;

}

function _getEndPHPTag( ) {

    return( "?>" ) ;
    
}

function _macro( $p , $indent = 0 ) {

    if( !file_exists( $p ) ) {

        _logExit( "ERROR macro $p" ) ;

    }

    $_MACRO_ARGS = func_get_args( ) ;

    include( $p ) ;

}

function _publish( $message ) {

    $db1 = debug_backtrace( ) ;
    $db2 = $db1[ 1 ] ;
    $db3 = pathinfo( $db2[ "args" ][ 0 ] ) ; 
    $db4 = $db3[ "filename" ] ;

    $sender = $db4 ; 

    ///////////////////////////////////////////////////////// 

    $pubmessage = $message ;

    if( substr( $pubmessage , 0 , 3 ) == "___" ) {

        //_log( "special! $sender" ) ;
        //$pubmessage= str_replace( "___" , $sender."_" , $pubmessage ) ;


    }

    //_log("PUBLISH $sender->$pubmessage ");

    $pubs = _configBlockQuery( "publish" , $pubmessage ) ;

    if( $pubs == "" ) {

        $pubs = array( $sender ) ;

    } else {

        array_push($pubs, $sender);

    }


    _configPush( "publish" ,  $pubmessage , $pubs ) ;

    //_log($pubs);

}

function _publishMarkerSet( $message ) {


    $db1 = debug_backtrace( ) ;
    $db2 = $db1[ 1 ] ;
    $db3 = pathinfo( $db2[ "args" ][ 0 ] ) ; 
    $db4 = $db3[ "filename" ] ;

    $sender = $db4 ; 

//    _log( "_publishMarkerSet $sender" ) ;

    print( "{$message}_PUBLISH_" ) ;

}

function _publishMarkerGet( $message ) {


    $db1 = debug_backtrace( ) ;
    $db2 = $db1[ 1 ] ;
    $db3 = pathinfo( $db2[ "args" ][ 0 ] ) ; 
    $db4 = $db3[ "filename" ] ;

    $sender = $db4 ; 

//    _log("_publishMarkerGet $sender");

    return( "{$message}_PUBLISH_" ) ;

}



