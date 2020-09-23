<?php

$_CONFIG_ = array( ) ;

_config( ) ;

//////////////////////////

function _config( ) {

    date_default_timezone_set( "UTC" ) ;


    _configBuildUpdate( );
    _configInit( ) ;
    _configIniLoad( ) ;
    _configCacheCheck( ) ;


}

function _configBuildUpdate( ) {

    $build = array( "rel" => 0 ) ;

    if( file_exists( "cache/build.json" ) ) {

        $build = json_decode( file_get_contents( "cache/build.json" ) , true ) ;

    }

    $build[ "hostname" ] = gethostname( ) ;
    $build[ "date" ] = time( ) ;
    $build[ "rel" ]++ ;

    $build[ "ver" ] = date( "ymd" ).$build[ "rel" ] ;

    file_put_contents( "cache/build.json" , json_encode( $build ) ) ;

    _configQuery( "_build" , $build ) ;

}

function _configCacheCheck( ) {

    if( !is_dir( _configQuery( "_cache" ) ) ) {

        mkdir( _configQuery( "_cache" ) ) ;
        chmod( _configQuery( "_cache" ), 0777 ) ;

    }

}

function _configIniQuery( $key , $section = "" ) {

    $ini = _configQuery( "_ini" ) ;


    if( $section == "" ) {

        if( isset( $ini[ $key ] ) ) {

            return( $ini[ $key ] ) ;

        } else {

            //_log( __FUNCTION__ . " $key ???" ) ;

            return;

        }

    }    

    if( isset( $ini[ $section ][ $key ] ) ) {

        return( $ini[ $section ][ $key ] ) ;

    } else {

        //_log( __FUNCTION__ . " $section -> $key ???" ) ;

        return;

    }

}

function _configBlockQuery( $block , $key ) {

    $block = _configQuery( $block ) ;

    if( isset( $block[ $key ] ) ) {

        return( $block[ $key ] ) ;

    } else {

        //_log( __FUNCTION__ . " $key ???" ) ;

        return ;

    }


}


function _configIniLoad( ) {

    $p = _configQuery( "_inipath" ) ;

    if( file_exists( $p ) ) {

        $t = parse_ini_file( $p , true , INI_SCANNER_RAW) ;
        _configQuery( "_ini" , $t ) ;

        _configQuery( "_configIniLoad" , "y" ) ;

    } else {

        _configQuery( "_configIniLoad" , "n" ) ;

    }

}

function _configInit( ) {

    global $argv ;

    _configQuery( "_originalargs" , $argv ) ;
    _configQuery( "_pid" , getmypid( ) ) ;
    _configQuery( "_targetdir" , $argv[ 1 ] ) ;
    _configQuery( "_basename" , basename($argv[ 1 ] )) ;
    _configQuery( "_os" , $argv[ 2 ] ) ;
    _configQuery( "_user" , $argv[ 3 ] ) ;
    _configQuery( "_hostname" , $argv[ 4 ] ) ;
    _configQuery( "_timestamp" , $argv[ 5 ] ) ;
    _configQuery( "_spid" , $argv[ 6 ] ) ;
    _configQuery( "_uid" , hash( "tiger192,3" , uniqid( "ARCHITECT" . $argv[ 7 ] , true ) ) ) ;
    _configQuery( "_time" , time( ) ) ;

    _configQuery( "_sdir" , dirname( __DIR__ ) ) ;
    _configQuery( "_cache" , _configQuery( "_sdir" ) . "/cache" ) ;
    //_configQuery( "_log" , _configQuery( "_cache" ) . "/" . _configQuery( "_basename" ) . ".log" ) ;
    _configQuery( "_log" , "/tmp" . "/" . _configQuery( "_basename" ) . ".log" ) ;
    _configQuery( "_inipath" , _configQuery( "_targetdir" ) . "/architect.ini" ) ;
    _configQuery( "_ini" , array( ) ) ;

    $argvsPos = array_search( "_argsep_" , $argv ) ;

    $_args = array( ) ;

    if( $argvsPos < ( count( $argv ) - 1 ) ) {

        $_args = array_slice( _configQuery( "_originalargs" ) , $argvsPos + 1 ) ;

    }

    _configQuery( "_args" , $_args ) ;

    _configQuery( "_createblanks" , in_array( "-c" , $argv ) ) ;


}

function _configDebug( ) {

    global $_CONFIG_ ;

    print_r( $_CONFIG_ ) ;

}

function _configQuery(  ) {

    global $_CONFIG_ ;

    $args = func_get_args( ) ;

    if( count( $args ) == 1 ) {

        if( !isset( $_CONFIG_[ $args[ 0 ] ] ) ) {

            return( NULL ) ;

        } else {

            return( $_CONFIG_[ $args[ 0 ] ] ) ;

        }

    }

    $_CONFIG_[ $args[ 0 ]  ] = $args[ 1 ]  ;

} 

function _configIsset( $k ) {

    global $_CONFIG_ ;

    return( isset( $_CONFIG_[ $k ] ) ) ;

}


function _configPush( $block , $key , $val ) {

    global $_CONFIG_ ;


    $_CONFIG_[ $block ][ $key ] = $val ;


}


function _configAppQuery( $key ) {

    return(_configIniQuery($key,"app"));



}




