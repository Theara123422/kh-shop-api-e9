<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\AuthResponse;
use App\Traits\GeneralResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Register the user
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @group register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'email' => 'required|string|email|max:50|unique:users,email',
            'phoneNumber' => 'required|string',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'password' => [
                'required',
                'min:6',
                'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
            ]
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                "Validation Failed",
                $validator->errors(),
                422
            );
        }

        // Image set to null by default
        // we have another endpoint to upload the image to the specific user
        $imageName = null;

        // Create user record in the database
        $user = User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone_number' => $request->phoneNumber,
            'country' => $request->country,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'profile' => $imageName,
        ]);

        // $otpToken = $this->otpService->generateOtp($user);

        return $this->successReponseWithData( "Register Successfully..",[
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'gender' => $request->gender,
            'email' => $request->email,
            'phone_number' => $request->phoneNumber,
            'country' => $request->country,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'profile' => $imageName,
        ]);
    }

    /**
     * Verify OTP and log the user
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
            'otpToken' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validate Failed', $validator->errors(), 400);
        }

        if (!$this->otpService->verifyOtp($request->otpToken, $request->otp)) {
            return $this->errorResponse(
                'Invalid OTP Code',
                'Invalid OTP Code or Expired',
                400
            );
        }

        $payload = Crypt::decrypt($request->otpToken);
        $user = User::find($payload['user']);
        $accessToken = JWTAuth::fromUser($user);

        return $this->successResponseWithAccessToken($accessToken, "User Registration Successfully", $user);
    }

    /**
     * Login implementation
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     * @group Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validation Failed',
                $validator->errors(),
                400
            );
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->errorResponse(
                'Unauthorized',
                'Wrong Credential',
                401
            );
        }
//        dump(get_class(auth()->user()));

        return $this->successResponseWithAccessTokenAndRefreshToken($token, 'Login Success');
    }

    /**
     * Logout implementation
     * @return mixed|\Illuminate\Http\JsonResponse
     * @group Logout
     */
    public function logout()
    {
        Auth::logout();
        return $this->successResponse("Logout Successfully");
    }

    /**
     *
     * Get current user
     * @return mixed|\Illuminate\Http\JsonResponse
     * @group Profile
     */
    public function me()
    {
        return $this->successReponseWithData("Get Profile Success",Auth::user());
    }

    public function uploadProfileImage(Request $request)
    {
        // dump($request->profile);
        $user = auth()->user();

        if (!$user) {
            return $this->errorResponse('Unauthorized', 'User not found', 403);
        }

        $validator = Validator::make($request->all(), [
            'profile' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Failed', $validator->errors(), 422);
        }

        if ($request->hasFile('profile')) {
            $image = $request->file('profile');
            $imageName = now()->format('YmdHis') . '-' . $image->getClientOriginalName();
            $path = $image->storeAs('profile', $imageName, 'public');

            $user->update(['profile' => $path]);
        }


        return $this->successReponseWithData('Upload Image success', [
            'image_url' => 'http://localhost:8000/'.asset('storage/' . $path)
        ]);
    }
}
