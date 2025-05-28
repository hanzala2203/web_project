<?php
// src/utils/sidebar_helpers.php

if (!function_exists('isActive')) {
    function isActive($uri, $path) {
        return strpos($uri, $path) !== false ? 'active' : '';
    }
}
