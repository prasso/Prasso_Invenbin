<?php

namespace Faxt\Invenbin\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller; // Base Controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthManager; // Use AuthManager instead of facade
use App\Models\User;

class ErpBaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $auth;  // Store injected Auth service
    protected $user;  // The user property

    /**
     * Constructor with dependency injection
     * 
     * @param AuthManager $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth; // Assign injected AuthManager to property
    }

    /**
     * setUser is an optional function that will authorize the user from token.
     * It is able to be called from any controller method, but is not a requirement.
     * It will throw an authorization exception if the token of the user is not valid.
     */
    protected function setUser(Request $request)
    {
        // Get the authenticated user via injected $auth
        $this->user = $this->auth->user();

        // Optionally set up a user if none is found
        if ($this->user === null) {
            $this->setUpUser($request);
        }

        // Throw an exception if no user is found
        if ($this->user === null) {
            Log::info('setUser: no user found');
            throw new AuthorizationException('Unauthorized access: no valid user found.');
        }

        return $this->user;
    }

    /**
     * setUser is an optional function that will authorize the user from token
     * it is able to be called from any controller method, but is not a requirement
     * it will throw an authorization exception if the token of the user is not valid
   
    protected function setUser(Request $request)
    {
        // Get the authenticated user
        $this->user = Auth::user();

        // Optionally set up a user if none is found
        if ($this->user === null) {
            $this->user = $this->setUpUser($request, $this->user); // Ensure setUpUser is implemented elsewhere
        }

        // Throw an exception if no user is found
        if ($this->user === null) {
            Log::info('setUser: no user found');
            throw new AuthorizationException('Unauthorized access: no valid user found.');
        }

        return $this->user; // Optionally return the user
    }
  */
    protected function setUpUser($request)
    {
        $accessToken = $request->header(config('constants.AUTHORIZATION_'));
        //if no accesstoken, check if we have an X-Authorization header present
        if($accessToken == '' && $auth = $request->header(config('constants.XAUTHORIZATION_'))) {
            info('setting authorization header from xauthorization header');
            $request->headers->set('Authorization', $auth);
        }

        $accessToken = str_replace("Bearer ","",$accessToken);
    
        if (!isset($accessToken) && isset($_COOKIE[config('constants.AUTHORIZATION_')]))
        {
            $accessToken = $_COOKIE[config('constants.AUTHORIZATION_')];
        }
        else {

            if ((!isset($accessToken) || $accessToken == 'Bearer') && $this->user != null) 
            {

                $accessToken = $request->user()->createToken(config('app.name'))->accessToken->token;

            }
        }
        if (isset($accessToken))
        {
            $this->setAccessTokenCookie($accessToken);
            if ($this->user == null)
            {
                $this->user = User::getUserByAccessToken($accessToken);
            }

            if ($this->user != null) 
            {
                \Auth::login($this->user); 
            }
        } else{
            Log::info('no access token found for request');
        }
       
    }

     /**
     * function is used to set accessToken cookie to browser
     */
    protected function setAccessTokenCookie($accessToken)
    {
        setcookie(config('constants.ACCESSTOKEN_'), $accessToken, time() + (86400 * 30), "/");
        
        setcookie(config('constants.COMMUNITYTOKEN'), $accessToken, time() + (86400 * 30), "/");
        setcookie(config('constants.COMMUNTIYREMEMBER'), $accessToken, time() + (86400 * 30), "/");
    }
}
