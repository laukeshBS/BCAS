<?php

namespace App\Http\Controllers\Admin\Auth;
use App\Models\Admin\Admin;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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
        // Validate Login Data
        $request->validate([
            'email' => 'required|max:50',
            'password' => 'required',
            'captcha' => 'required|captcha',
             ['captcha.captcha'=>'Invalid captcha code.']
            
        ]);
       // dd("You are here :) .");
        // Attempt to login
        $password= $request->password;
        //dd($password);
        $key=$request->session()->get('bsrandom');
        $haskey = $key;
        $plainText = $this->cryptoJsAesDecrypt($haskey, $password);
       // dd($plainText);
        //echo $hashedValue = hash('sha256', $plainText);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' =>  $plainText], $request->remember)) {
            // Redirect to dashboard
            session()->flash('success', 'Successully Logged in !');
            return redirect()->route('admin.dashboard');
        } else {
            // Search using username
            if (Auth::guard('admin')->attempt(['username' => $request->email, 'password' =>  $plainText], $request->remember)) {
                session()->flash('success', 'Successully Logged in !');
                return redirect()->route('admin.dashboard');
            }
            // error
            session()->flash('error', 'Invalid email and password');
            return back();
        }
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
