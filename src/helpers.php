<?php

use Optimizely\Optimizely;

if (! function_exists('optimizely')) {
    function optimizely() : Optimizely
    {
        return app(Optimizely::class);
    }
}
