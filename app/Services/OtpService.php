<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class OtpService{

    /**
     * Generate OTP and encrypt it send to user's email
     * @param User $user
     * @return string
     */
    public function generateOtp(User $user){
        $otp = rand(100000,999999);

        $encryptdOtp = Crypt::encrypt([
            'otp' => $otp,
            'user' => $user->id,
            'expired_at' => now()->addMinutes(1)
        ]);

        $this->sendOtpMail($user,$otp);

        return $encryptdOtp;
    }

    /**
     * Send the otp to user email address
     * @param User $user
     * @param mixed $otp
     * @return void
     */
    protected function sendOtpMail(User $user,$otp){
        Mail::raw("Your OTP code is", function ($message) use ($user){
            $message->to($user->email)->subject('Your OTP code');
        });
    }

    /**
     * Decrypt OTP and verify the expired time
     * @param mixed $encryptedOtp
     * @param mixed $otp
     * @return bool
     */
    public function verifyOtp($encryptedOtp,$otp){
        try{
            $payload = Crypt::decrypt($encryptedOtp);
        }catch( \Exception $exception){
            return false;
        }

        if(now()->greaterThan($payload['expired_at'])){
            return false;
        }

        return $payload['otp'] == $otp;
    }
}