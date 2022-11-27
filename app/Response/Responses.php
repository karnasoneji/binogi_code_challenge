<?php

namespace App\Response;

class Responses
{

    /**
     *
     * Array of Exception classes and their respective response.
     *
     */

    private const EXCEPTION_JSON_RESPONSE_MAP = [
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' =>  [
                                                                    "error" => [
                                                                                    "message" => "Resource not found."
                                                                                ]
                                                                  ],
    ];

    /**
     *
     * Array of Exception classes and their respective response statuses.
     *
     */

    private const EXCEPTION_RESPONSE_STATUS_MAP = [
        'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 404,
    ];


    /**
     *
     * Get method to retrive and prepare the response for Exception
     * @param $exception_class String
     * @return Array
     */

    public static function getCustomExceptionResponse($exception_class){

        $result = Self::EXCEPTION_JSON_RESPONSE_MAP[$exception_class];

        return $result;

    }

    /**
     *
     * Get method to retrive status code for Exception
     * @param $exception_class String
     * @return Integer
     */

    public static function getCustomExceptionResponseStatus($exception_class){

        $result = Self::EXCEPTION_RESPONSE_STATUS_MAP[$exception_class];

        return $result;

    }
}
