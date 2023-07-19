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
        $email = $request->input('email');
        $otp = rand(1000,9999);
        $count = User::where('email', '=', $email)->count();

        if($count==1){
            Mail::to($email)->send(new OTPMail($otp));

        }else{
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized user and email not found',
            ]);
        }
    }

}
