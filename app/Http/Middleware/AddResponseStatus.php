<?php

namespace App\Http\Middleware;

use App\Models\Student;
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
        $getStage = array_slice(func_get_args(), 2);
        $stageId = $request->input('stage_id');
        if($getStage && $getStage[0] == 'with_total_students' && isset($stageId)){
            $studentsCount = Student::byStage($stageId)->count();
            $formattedResponse['total_students'] = $studentsCount;
        }
    
        // Merge the original response data with the formatted response
        if (is_array($responseData)) {
            $formattedResponse = array_merge($formattedResponse, $responseData);
        }
    
        // Set the modified response data
        $jsonResponse = new JsonResponse($formattedResponse, $status);
    
        // Set the modified response content
        $response->setContent($jsonResponse->getContent());
    
        return $response; // Continue passing the modified response down the middleware stack
    }
}
