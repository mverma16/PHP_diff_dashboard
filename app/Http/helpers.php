<?php

/**
 * Format success message/data into json success response
 *
 * @param string $message Success message
 * @param array|string $data Data of the response
 * @param int $statusCode
 * @return \Illuminate\Http\JsonResponse json response
 */
function successResponse($message = '', $data = '', $statusCode = 200) {
    $response = ['success' => true];

    // if message given
    if (!empty($message)) {
        $response['message'] = $message;
    }

    // If data given
    if (!empty($data)) {
        $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
}



// For API response
/**
 * Format the error message into json error response
 *
 * @param string|array $message Error message
 * @param int $statusCode
 * @return \Illuminate\Http\JsonResponse json response
 */
function errorResponse($message, $statusCode = 500) {
    /**
     * When developers simply want to send info of exceptions they are handling
     * using errorResponse() and explicitly pass $e->getCode() as second argument
     * then if thrown exception has code as 0 then response() will throw an error
     * and response will be modified to new exception. We are handling this here
     * so that developers can easily use this method and simply pass $e->getCode()
     * to use for response HTTP code.
     */
    $statusCode = ($statusCode)?:500;
    return response()->json(['success' => false, 'message' => $message], $statusCode);
}

/**
 * Format exception response with exception details
 *
 * @param \Exception $exception Exception instance
 * @return \Illuminate\Http\JsonResponse json response
 */
function exceptionResponse(Throwable $exception){
    return response()->json(
        [
            'success' => false,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(). " (".$exception->getLine().")",
            'trace' => $exception->getTraceAsString()
        ], 500);
}
