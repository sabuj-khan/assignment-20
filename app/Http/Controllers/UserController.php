<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
       
    function userRegistration(Request $request){

       try{
        User::create([
            "firstName"=>$request->input('firstName'),
            "lastName"=>$request->input('lastName'),
            "email"=>$request->input('email'),
            "phone"=>$request->input('phone'),
            "password"=>$request->input('password')
           ]);
    
           return response()->json([
            'status'=>'Success',
            'message'=>'User registration done successfully',
           ]);
       }catch(Exception $e){
        return response()->json([
            'status'=>'Failed',
            'message'=>'User registration failed',
            'exception'=>$e->getMessage()
           ]);
       }



      
    }

    function userLogin(Request $request){
        $count = User::where('email', '=', $request->input('email'))
        ->where('password', '=', $request->input('password'))
        ->count();

        if($count == 1){
            $token = JWTToken::createToken($request->input('email'));

            return response()->json([
                'status'=>'Success',
                'message'=>'User Login Successful',
                'token'=>$token
            ]);
        }else{
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized user',
            ]);
        }


    }

    function sendOTPToEmail(Request $request){
        $email=$request->input('email');
        $opt=rand(1000,9999);
        $count=User::where('email','=',$email)->count();

        if($count==1){
            // OTP Email Address
            Mail::to($email)->send(new OTPMail($opt));
            // OTO Code Table Update
            User::where('email','=',$email)->update(['otp'=>$opt]);

            return response()->json([
                'status'=>'Success',
                'message'=>'OTP has been sent to Email',
            ]);

        }else{
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized user and email not found',
            ]);
        }
    }

    function optvarification(Request $request){
        $email = $request->input('email');
        $opt = $request->input('opt');
        $count = User::where('email', '=', $email)
        ->where('opt', '=', $opt)->count();

        if($count == 1){
            //OPT reset
            User::where('opt', '=', $opt)->update(['opt'=>'0']);
            // Token create for password setting
            $token = JWTToken::createTokenForSetPassword($request->input('email'));

            return response()->json([
                'status'=>'Success',
                'message'=>'OPT varification done successfully',
                'token'=>$token
            ]);
        }else{
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized user and wrong opt code',
            ]);
        }

    }

    function resetPassword(Request $request){
        try{
            $email = $request->header('email');
            $password = $request->input('password');
            User::where('email', '=', $email)->update(['password'=>$password]);

            return response()->json([
                'status'=>'Success',
                'message'=>'Password has been reset successfully',
            ]);

        }catch(Exception $e){
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized',
            ]);
        }
    }

}
