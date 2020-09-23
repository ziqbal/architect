<?php


function _fsGetInputTemplate( ) {

    _log( __FUNCTION__ ) ;

    $inputTemplate = _configIniQuery( "template" , "input" ) ;

    if( is_null( $inputTemplate ) ) {

        _logExit( "No input template found in ini" ) ;
        exit ;

    }

    if( !file_exists( $inputTemplate ) ) {

        _logExit( "$inputTemplate template file not found" ) ;
        exit ;

    }

    return( file_get_contents( $inputTemplate ) ) ;


}


function _fsSaveOriginal( $buffer ) {

    $fp = _configIniQuery( "original" , "output" ) ;

    if( is_null( $fp ) ) {

        $configinputtemplateparts = pathinfo(_configIniQuery( "template" , "input" ));
        //_log($configinputtemplateparts);

        $fp = "cache/_original." . $configinputtemplateparts[ "extension" ] ;

    }

    $parts = pathinfo($fp);

    if(!is_dir($parts['dirname'])){
        _log($parts['dirname']." does not exist?");
        
        return;

    }


    file_put_contents( $fp , $buffer )  ;

    if( !file_exists( $fp ) ) {

        _logExit( "ERROR creating $fp" ) ;

    }


}


function _fsGetInput( ) {

    $_inputFile = _fsGetInputTemplate( ) ;
    $_buff = _markersGetOriginal( $_inputFile ) ;
    _fsSaveOriginal( $_buff ) ;

    return($_buff);


}


function _fsSaveTarget( $buffer ) {

    _log( __FUNCTION__ ) ;

    $fp = _configIniQuery( "target" , "output" ) ;

    if( is_null( $fp ) ) {

        $configinputtemplateparts = pathinfo(_configIniQuery( "template" , "input" ));
        $fp = _configQuery("_basename") . "." . $configinputtemplateparts[ "extension" ] ;

    }


    file_put_contents( $fp , $buffer )  ;

}

function _fsDeleteTarget( ) {

     if(_configQuery( "_configIniLoad" ) === "n") {

        return ;

     }


    $fp = _configIniQuery( "target" , "output" ) ;

    if( is_null( $fp ) ) {

        $configinputtemplateparts = pathinfo( _configIniQuery( "template" , "input" ) ) ;

        if(!isset($configinputtemplateparts[ "extension" ])) return;

        $fp = _configQuery( "_basename") . "." . $configinputtemplateparts[ "extension" ] ;



    }

    if(file_exists($fp)){

        unlink( $fp ) ;

    }

}




