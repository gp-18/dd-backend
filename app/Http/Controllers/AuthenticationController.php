<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    /**
     * THIS IS THE FUNCTION USED TO CREATE USER
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/register (PUBLIC ROUTE)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserRegister(Request $request)
    {
        try {

            $validate = $request->validate([
                'name'     => 'required|string',
                'email'    => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            if (User::where('email', $validate['email'])->first()) {
                return response()->json([
                    'message' => 'User Already Exist',
                    'status'  => 'Failed Registration'
                ], 409);
            }
            
            $user = User::create($request->only('name', 'email') + ['role' => 'U']  + ['password' => Hash::make($validate['password'])]);

            $token = $user->createToken($request->email)->plainTextToken;

            return response()->json([
                'message'   => 'User Created Successfully',
                'token'     => $token,
                'status'    => 'Success Registration',
                'data'      => $user
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'status'  => 'Failed Registration'
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO LOGIN USER
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/login (PUBLIC ROUTE)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserLogin(Request $request)
    {

        try {

            $validate = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|min:8',
            ]);

            $user = User::where('email', $validate['email'])->first();

            if ($user && Hash::check($request->password, $user->password)) {

                $token = $user->createToken($request->email)->plainTextToken;

                return response()->json([
                    'message'   => 'User Login Successfully',
                    'token'     => $token,
                    'status'    => 'Success Login',
                    'data'      => $user
                ], 200);

            }

            return response()->json([
                'message'   => "Provided Credentials are invalid",
                'status'    => 'Failed Login'
            ], 401);

        } catch (\Exception $e) {

            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => 'Failed'
            ], 500);
        }
    }

    /**
     * THIS IS THE FUNCTION USED TO LGOUT USER
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/logout (PROTECTED)
     * @Middleware (AUTH:SANCTUM)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserLogout(Request $request)
    {
        try {

            $user = Auth::user();

            if ($user) {

                $request->user()->tokens()->delete();

                return response()->json([
                    'message' => 'User logged out successfully',
                    'status'  => 'Success Logout'
                ], 200);

            } else {

                return response()->json([
                    'message' => 'User not found',
                    'status'  => 'Failed Logout'
                ], 404);

            }
        } catch (\Exception $e) {

            return response()->json([
                'error'   => $e->getMessage(),
                'status'  => 'Failed ',
            ], 500);

        }
    }

    /**
     * THIS IS THE FUNCTION USED TO GET LOGIN USER DETAILS
     * @method GET
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/details (PROTECTED )
     * @Middleware (Auth:Sanctum)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UserDetails()
    {

        try {

            $user = Auth::user();

            return response()->json([
                'message' => 'User Login Details',
                'status'  => 'User Details',
                'data'    => $user
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'error'   => $e->getMessage(),
                'status'  => 'Failed ',
            ], 500);

        }
    }

    /**
     * THIS IS THE FUNCTION USED TO CHANGE USER PASSWORD
     * THE USERS KNOWS HIS CURRENT PASSWORD AND NEEDS TO UPDATE THE PASSWORD
     * @method POST
     * @author PARTH GUPTA (Zignuts Technolab)
     * @route user/changePassword (PROTECTED ROUTE)
     * @Middleware(AUTH:SANCTUM)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        try {

            $validated = $request->validate([
                'current_password' => 'required|min:8',
                'new_password' => 'required|confirmed|min:8',
            ]);

            $user = Auth::user(); 

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => 'Failed',
                ], 404);
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'status' => 'Failed',
                ], 401);
            }

            if (Hash::check($validated['new_password'], $user->password)) {
                return response()->json([
                    'message' => 'New password cannot be the same as the current password',
                    'status' => 'Failed',
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save(); 

            return response()->json([
                'message' => 'Password changed successfully',
                'status' => 'Success',
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'message'  => 'An error occurred while changing the password',
                'error'    => $e->getMessage(),
                'status'   => 'Failed',
            ], 500);

        }
    }

}
