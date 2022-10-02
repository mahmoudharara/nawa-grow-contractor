<?php

 namespace Contractor\Base\Repositories\Api;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
useContractor\Base\Events\UserAuthentication;
useContractor\Base\Exceptions\AuthenticationException;
useContractor\Base\Interfaces\AuthInterface;


/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 05/04/2020
 * Time: 9:44 PM
 */
class AuthRepository implements AuthInterface
{


    public $modelPath = null;
    public $guard = 'api';

    /**
     * @author WeSSaM
     * @param $credentials
     * @return mixed
     * @throws AuthenticationException
     */
    public function login($credentials)
    {

        Config::set('jwt.user', $this->modelPath);                        // change jwt user @author WeSSaM
        Config::set('auth.providers.users.model', $this->modelPath);      // load the user model @author WeSSaM

        if (!$token = $this->guard()->attempt($credentials))
            throw new AuthenticationException(trans('core::messages.user_not_found'));



        event(new UserAuthentication(LOGIN_TRANSACTION));    // register user's login transaction      @author WeSSaM

        return $token;
    }

    /**
     * @author WeSSaM
     * @param string $auth
     * @return mixed
     */
    public function logout($auth = 'api')
    {
        // TODO: Implement logout() method.
    }

    /**
     * @author WeSSaM
     * @param string $auth
     * @return mixed
     */
    public function refresh($auth = 'api')
    {
        // TODO: Implement refresh() method.
    }


    /**
     * @author WeSSaM
     * @return mixed
     */
    public function guard()
    {
        return Auth::guard($this->getGuard());
    }

    /**
     * @return string
     */
    public function getModelPath(): string
    {
        return $this->modelPath;
    }

    /**
     * @param string $modelPath
     */
    public function setModelPath(string $modelPath): void
    {
        $this->modelPath = $modelPath;
    }

    /**
     * @return string
     */
    public function getGuard(): string
    {
        return $this->guard;
    }

    /**
     * @param string $guard
     */
    public function setGuard(string $guard): void
    {
        $this->guard = $guard;
    }

}
