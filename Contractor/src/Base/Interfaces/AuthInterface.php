<?php

 namespace Contractor\Base\Interfaces;

/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 05/04/2020
 * Time: 9:46 PM
 */
interface AuthInterface
{

    /**
     * @author WeSSaM
     * @param $credentials
     * @return mixed
     */
    public function login($credentials);


    /**
     * @author WeSSaM
     * @param string $auth
     * @return mixed
     */
    public function logout($auth = 'api');


    /**
     * @author WeSSaM
     * @param string $auth
     * @return mixed
     */
    public function refresh($auth = 'api');


    /**
     * @author WeSSaM
     * @return mixed
     */
    public function guard();
}
