<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\patient_token;
use App\Models\patient;

class MyTokenManager
{
    
    public static function createToken($id)
    {
        $tokenStr = Str::random(40);
        $enctoken = Hash::make($tokenStr);
        $result =patient_token::create(array(
            
                'user_id' => $id,
                'token' => $tokenStr
            
        ));
        $tokenId = DB::getPdo()->lastInsertId();
        return "$tokenId|$tokenStr";
    }
    public static function currentUser(Request $request)
    {
        $token = $request->bearerToken();
        if(!str_contains($token, '|'))
        {
            return NULL;
        }
        [$tokenId, $tokenStr] = explode('|', $token, 2);
        $result = patient_token::where(['id' => $tokenId])->first();
        $tokenData = $result[0];
        if($tokenStr == $result->token)
        {
            $result1 = patient::where(['id' => $result->user_id])->first();
            $result2 = $result1[0];
            return $result1;
        }
        else
        {
            return NULL;
        }
    }
    public static function removeToken(Request $request)
    {
        
        $token = $request->bearerToken();
        //return ['token' => $token];
        if(!$token)
        {
            return NULL;
        }
        if(!str_contains($token, '|'))
        {
            return NULL;
        }
        [$tokenId, $tokenStr] = explode('|', $token, 2);
        patient_token::where('id', $tokenId)->firstorfail()->delete();
        //DB::delete("delete from patient_tokens where id = ?",[$tokenId]);
    }
}
