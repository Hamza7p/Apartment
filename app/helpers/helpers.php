<?php

if(!function_exists('generateOtpCode')) {
    function generateOtpCode() {
        if(isLocalEnvironment()) {
            return '12345';
        }
        return rand(10000, 99999);
    }
}

if(! function_exists('isLocalEnvironment')) {
    function isLocalEnvironment() {
        return app()->environment('local');
    }
}