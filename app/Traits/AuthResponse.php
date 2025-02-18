<?php 
namespace App\Traits;
trait AuthResponse {
    public function successResponseWithAccessToken($accessToken, $message, $data){
        return response()->json([
            'message' => $message,
            'data'    => $data,
            'accessToken' => $accessToken
        ],200);
    }

    public function successResponseWithAccessTokenAndRefreshToken($accessToken, $refreshToken,$message){
        return response()->json([
            'message' => $message,
            'acessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'type' => 'bearer'
        ],200);
    }

    public function successResponse($message){
        return response()->json([
            'message' => $message
        ],200);
    }

    public function errorResponse($message,$error,$status){
        return response()->json([
            'message' => $message,
            'error' => $error
        ],$status);
    }

    public function otpSuccessResponse($otp,$message){
        return response()->json([
            'message' => $message,
            'otp_token' => $otp
        ],200);
    }
}