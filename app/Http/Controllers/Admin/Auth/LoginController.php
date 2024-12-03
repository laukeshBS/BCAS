<?php

namespace App\Http\Controllers\Admin\Auth;
use Log;
use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_DASHBOARD;

    /**
     * show login form for admin guard
     *
     * @return void
     */
    public function showLoginForm()
    {
        $randomBytes = random_bytes(8);
        $randomString = bin2hex($randomBytes);
        Session::put('bsrandom', $randomString);
        return view('admin.auth.login');
    }


    /**
     * login admin
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:50',
            'password' => 'required',
        ]);

        $user = Admin::with('roles')->where('email', $request->email)->where('status', 2)->first();

        $key = 'xWfR9K7h3gD5yTqV'; // Adjust to match the 16-byte key
        $encryptedData = $request->input('password');
        $iv = base64_decode($request->input('iv')); // Decode Base64 IV

        $decrypted = openssl_decrypt(base64_decode($encryptedData), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            Log::error('Decryption failed: ' . openssl_error_string());
        }
        if ($user && Hash::check($decrypted, $user->password)) {
            $token = $user->createToken('bcas_cms')->plainTextToken;

            $user->api_token = $token; // If using api_token column
            $user->save();

            $logs_data = array(
                'module_item_title'     =>  'Login Successfull',
                'module_item_id'        =>  $user->id,
                'action_by'             =>  $user->id,
                'old_data'              =>  $user,
                'new_data'              =>  $request,
                'action_name'           =>  'Login Attempt',
                //'page_category'         =>  '',
                'lang_id'               =>   "en",
                'action_type'        	=>  'User Login',
                'approve_status'        =>  0,
                'action_by_role'        =>  $user->username
            );
            audit_trails($logs_data);

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged in!',
                'data' => [
                    'user' => $user,
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                ],
            ], 200);
        }
        $logs_data = array(
            'module_item_title'     =>  'Login Unsuccessfull',
            'module_item_id'        =>  0,
            'action_by'             =>  0,
            'old_data'              =>  response()->json(['success' => false,'message' => 'No such username or password.'], 401),
            'new_data'              =>  $request,
            'action_name'           =>  'Login Attempt',
            //'page_category'         =>  '',
            'lang_id'               =>   "en",
            'action_type'        	=>  'User Login',
            'approve_status'        =>  0,
            'action_by_role'        =>  ''
        );
        audit_trails($logs_data);
        return response()->json([
            'success' => false,
            'message' => 'No such username or password.',
        ], 401);
    }



    /**
     * logout admin guard
     *
     * @return void
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function myCaptcha()
    {
        return view('myCaptcha');
    }
    


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }
    // Password Decrypt
    public function cryptoJsAesDecrypt($passphrase, $jsonString){
		$jsondata = json_decode($jsonString, true);
		$salt = hex2bin($jsondata["s"]);
		$ct = base64_decode($jsondata["ct"]);
		$iv  = hex2bin($jsondata["iv"]);
		$concatedPassphrase = $passphrase.$salt;
		$md5 = array();
		$md5[0] = md5($concatedPassphrase, true);
		$result = $md5[0];
		for ($i = 1; $i < 3; $i++) {
		$md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
		$result .= $md5[$i];
		}
		$key = substr($result, 0, 32);
		$data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
		return json_decode($data, true);
    }
}
