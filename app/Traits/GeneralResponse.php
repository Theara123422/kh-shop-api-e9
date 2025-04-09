<?php 
namespace App\Traits;
trait GeneralResponse{
    public function successReponseWithData($message, $data){
        return response()->json([
            'status' => 200,
            'message' => $message,
            'data'  => $data
        ]);
    }

    public function successResponse($message){
        return response()->json([
            'message' => $message
        ],200);
    }

    public function errorResponse($error, $status){
        return response()->json([
            'message' => $error 
        ],$status);
    }
}