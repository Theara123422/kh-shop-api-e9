<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\AuthResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use AuthResponse;

    protected $otpService;

    /**
     * Inject OTP Service via constructor
     * @param OtpService $otpService
     */
    public function __construct(
        OtpService $otpService
    ) {
        $this->otpService = $otpService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'email' => 'required|string|email|max:50|unique:users,email',
            'phoneNumber' => 'required|numeric|phone_number',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'password' => [
                'required',
                'min:6',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'confirmed'
            ],
            'profile' => [
                'nullable'
            ]
        ]);

        if($validator->fails()){
            return $this->errorResponse(
                "Validation Failed",
                $validator->errors(),
                422
            );
        }
        $profile = null;
        if($request->hasFile('profile')){
            $profile = $request->file('profile')->store('./image','public');
        }

          // Create user record in the database
        $user = User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone_number' => $request->phoneNumber,
            'country' => $request->country,
            'city' => $request->city,
            'password' => Hash::make($request->password), // Ensure the password is hashed
            'profile' => $profile,
        ]);

        $otpToken = $this->otpService->generateOtp($user);

        return $this->otpSuccessResponse($otpToken,"Otp sent to your email successfully, please verify.");
    }

    /**
     * Verify OTP and log the user
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all(),[
            'otp' => 'required|string',
            'otpToken' => 'required|string'
        ]);

        if($validator->fails()){
            return $this->errorResponse('Validate Failed',400,$validator->errors());
        }

        if(!$this->otpService->verifyOtp($request->otp_token,$request->otp)){
            return $this->errorResponse(
                'Invalid OTP Code',
                'Invalid OTP Code or Expired',
                400
            );
        }

        $payload = Crypt::decrypt($request->otp_token);
        $user = User::find($payload['user_id']);
        $accessToken = JWTAuth::fromUser($user);
        $refreshToken = JWTAuth::factory()->setTTL(config('jwt.refresh_ttl'))->token();

        return $this->successResponseWithAccessTokenAndRefreshToken($accessToken,$refreshToken,'Register Successfully');
    }
}
