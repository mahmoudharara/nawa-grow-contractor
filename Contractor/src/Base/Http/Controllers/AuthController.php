<?php

namespace NawaGrow\Contractor\Base\Http\Controllers;





use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NawaGrow\Contractor\Base\Events\UserAuthentication;
use NawaGrow\Contractor\Base\Http\Requests\BaseRequest;
use NawaGrow\Contractor\Base\Http\Resources\AuthResource;
use NawaGrow\Contractor\Repositories\Api\AuthRepository;

class AuthController extends BaseController
{

    public $authRepository;
    public $guard = 'api';
    public $model = User::class;
    public $resource = AuthResource::class;
    public $request = BaseRequest::class;

    /**
     * @param AuthRepository $authRepository
     * @author WeSSaM
     * AuthController constructor.
     */
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
        $this->authRepository->setGuard($this->guard);
        $this->authRepository->setModelPath($this->model);
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \Modules\Core\Exceptions\AuthenticationException
     * @author WeSSaM
     */
    public function login(Request $request)
    {
        $token = $this->authRepository->login($this->credentials($request));
        !$token ?: $this->authenticated($token);
        $this->afterLogin();
        return response()->api(SUCCESS_RESPONSE, trans('core::messages.successfully_logged_in'), [
            'access_token' => $token,
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'token_type' => 'Bearer',
            'auth' => (new $this->resource(auth($this->guard)->user()))->serializeForEdit($request),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     * @author WeSSaM
     */


    public function credentials(Request $request)
    {
        return array($this->username() => $request->get($this->username()), 'password' => $request->get('password') ??'');
    }
    /**
     * @return string
     * @author WeSSaM
     */
    public function username()
    {
        return 'username';
    }

    /**
     * @param string $auth
     * @return mixed
     * @throws \Modules\Core\Exceptions\AuthenticationException
     */
    public function logout($auth = 'api')
    {
        if (auth($this->guard)->check()) {
            $user = auth($this->guard)->user();
            auth($this->guard)->logout();
        } else if (auth(ADMIN_GUARD)->check()) {
            $user = auth(ADMIN_GUARD)->user();
            auth(ADMIN_GUARD)->logout();
        } else if (auth('api')->check()) {
            $user = auth('api')->user();
            auth('api')->logout();
        }
        event(new UserAuthentication(LOGOUT_TRANSACTION, $user));                       // register user's logout transaction @author WeSSaM
        return response()->api(SUCCESS_RESPONSE, trans('Auth::lang.logged_out_successfully'));
    }


    /**
     * @param string $auth
     * @throws \Modules\Core\Exceptions\AuthenticationException
     */
    public function refresh($auth = 'api')
    {
        $data = null;
        $token = null;
        if (auth($auth)->check()) {
            $token = auth($auth)->refresh();
        } elseif (auth(ADMIN_GUARD)->check()) {
            $token = auth($auth)->refresh();
        } elseif (auth('api')->check()) {
            $token = auth($auth)->refresh();
        }
        if (is_null($data))
            return response()->api(ERROR_RESPONSE, trans('Auth::lang.token_refreshed_error'));
        event(new UserAuthentication(3));                       // register user's logout transaction @author WeSSaM
        return $this->authenticated($token);
    }

    /**
     * @param $token
     * @author WeSSaM
     */
    protected function authenticated($token)
    {


    }

    /**
     * @return mixed
     * @author WeSSaM
     */
    public function guard()
    {
        return Auth::guard($this->authRepository->getGuard());
    }


    public function getAttributesForRegister(Request $request)
    {
        return [] ;
    }
    public function afterLogin(){
        // TO DO What do u want after login
    }
}
