<?php


function _shutdownCallback( ) {

    _log( "SHUTDOWN" ) ;

}

register_shutdown_function( "_shutdownCallback" ) ;

