<?php

/*
 *  @Author: ASHISH KUMAR
 
 */

namespace App\Libraries;

use Illuminate\Support\Facades\Response;

/**
 * Description of ResponseFactory
 *
 * @author kamlesh
 */
class ResponseFactory {

    /**
     * 
     * @return type
     */
    public static function setResponse($message = '', $status = FALSE, $HttpCode = 200, $data = [], $contentType = 'application/json') { 
        return response()->json([
                    'status_code'   => $HttpCode,
                    'error'         => $status,
                    'message'       => $message,
                    'data'          => $data,
                ])->withHeaders([
                    'Content-Type' => $contentType,
                    'Access-Control-Allow-Origin' => 'http://localhost:3000',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS,PUT,DELETE',
                    'Access-Control-Allow-Headers' => '*',
                    'Access-Control-Expose-Headers' => '*',
                    'Access-Control-Max-Age' => '86400'
        ]);
        
    }

}
