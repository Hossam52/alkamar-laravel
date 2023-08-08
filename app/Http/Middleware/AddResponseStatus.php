<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class AddResponseStatus
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Get the original response data
        $responseData = $response->getOriginalContent();

        // Determine the status based on the response status code
        $status = $response->getStatusCode();
        $isSuccessful = $status >= 200 && $status < 300;

        // Create the formatted response array
        $formattedResponse = [
            'status' => $isSuccessful,
            'message' => $isSuccessful ? 'تم استرجاع البيانات بنجاح' : 'خطأ في البيانات',
        ];

        // Merge the original response data with the formatted response
        if (is_array($responseData)) {
            $formattedResponse = array_merge($formattedResponse, $responseData);
        }

        // Set the modified response data
        $jsonResponse = new JsonResponse($formattedResponse, $status);

        // Set the modified response content
        $response->setContent($jsonResponse->getContent());

        return $response;
    }
}
