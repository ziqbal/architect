<?php


function _parseInput( ) {

    $_buff = _fsGetInput( ) ;

    while( !_configQuery( "passmadenochange" ) ) {


        $_buff = _getOutputFromSinglePass( $_buff ) ;

        
    }


    if( _configIniQuery( "addreembedmarker" , "markeroptions")=="true" ) {

        foreach( _configQuery( "embedfirstpassmarkersdata" ) as $reembedk => $reembedv ) {

            $_buff = str_replace( $reembedk , "\n{$reembedv}\n" , $_buff ) ;

        }

    }


    return($_buff);

}