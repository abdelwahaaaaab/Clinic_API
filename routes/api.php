<?php
use App\Helpers\MyTokenManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\patient;
use App\Models\patient_token;
use App\Models\appointment;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::post('/register', function(Request $request){
    $request->validate(
        [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:patients',
            'password' =>'required|confirmed|min:6|max:255',
            'password_confirmation' => 'required'
        ]
    );
    $pass = $request->password;
    $reg = patient::create(array(
        'name' => $request->name,
        'email' => $request->email,
        'password' =>encrypt($pass),
    ));
    $token = MyTokenManager::createToken($reg->id);
    return [
        'message' => 'Register Successfully',
        'token' => $token,
    ];
});

Route::post('/login', function(Request $request){
    $request->validate(
        [
            'email' => 'required',
            'password' => 'required'
        ]
    );
    $email = $request->email;
    $password = $request->password;
    $login = patient::where(['email' => $email])->first();
    if($login !== NULL && $password == decrypt($login->password))
    {
        $token = MyTokenManager::createToken($login->id);
        return [
            'message' => 'Logged In Successfully',
            'token' => $token,
        ];
    }
    else
    {
        return ['error' => 'Email or Password Incorrect'];
    }
});


Route::group(['middleware' => 'MyAuthAPI'], function(){

    Route::post('/appointment', function(Request $request){
        $request->validate(
            [
                    'Name' => 'required|max:50',
                    'Email' => 'required|email',
                    'Phone' => 'required|min:11',
                    'Dname' => 'required|max:50',
                    'Date' => 'required|after_or_equal:now'
            ]
        );
        $result = appointment::create($request->all());
        return [
            'result' => $result,
            'message' => 'Appointment Booked Successfully',
        ];
    
    });

    Route::get('/logout', function(Request $request){
                MyTokenManager::removeToken($request);
                return ['message' => 'You Are Log Out'];
        
        
    });
    
});


