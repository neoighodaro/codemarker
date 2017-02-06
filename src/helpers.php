<?php

use Neo\CodeMarker\CodeMarker;

if ( ! function_exists('start_marker')) {
    /**
     * Start a marker.
     *
     * @param  string $name
     * @return void
     */
    function start_marker($name)
    {
        CodeMarker::instance()->addMarker($name);
    }
}

if ( ! function_exists('stop_marker')) {
    /**
     * Stop marker.
     *
     * @param  string $name
     * @return void
     */
    function stop_marker($name)
    {
        CodeMarker::instance()->stopMarker($name);
    }
}