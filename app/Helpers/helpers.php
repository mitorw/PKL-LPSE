<?php

if (! function_exists('safeFileName')) {
    function safeFileName($string) {
        return preg_replace('/[^A-Za-z0-9_\-]/', '-', $string);
    }
}
