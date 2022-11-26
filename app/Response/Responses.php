<?php

namespace App\Response;

class Responses
{

    /**
     *
     * Error message and reponse code for Model Not Found exception.
     */

    public static $modelNotFound = ['Message' => 'Resource not found.'];
    public static $modelNotFoundResponseCode = 404;
}
