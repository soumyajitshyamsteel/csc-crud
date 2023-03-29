<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validateData= Validator::make($request->all(),[
            'email' => 'email|required',
            'password' => 'required'
        ]);
        if ($validateData->fails()) {
            return $this->responseJson(false,422,$validateData->errors()->all(),"");
        }
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        // return $request->all();
        if (!auth()->attempt($data)) {
            return response()->json(['error_message' => 'Incorrect Details.
            Please try again']);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;
        return $this->responseJson(true,200,$validateData->errors()->all(),['user' => auth()->user(), 'token' => $token]);

    }
}
