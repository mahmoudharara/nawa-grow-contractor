<?php

namespace NawaGrow\Contractor;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{


    public function register()
    {

        /**
         * call authApiRoutes Macro
         *
         * @author WeSSaM
         */
        $this->authApiRoutes();

        /**
         * call resourceRoutes Macro
         *
         * @author WeSSaM
         */
        $this->resourceRoutes();



        $this->callAppendsRoutes();


        $this->uploadingRoutes();
    }

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * call resourceRoutes Macro
     *
     * @author WeSSaM
     */
    public function resourceRoutes()
    {
        Route::macro('resourceRoutes', function ($resource, $controller, $function = null) {
            /**
             * Generate resource default rest-full routes
             *
             * @param $resource
             * @param string $controller
             * @return string
             * @author WeSSaM
             */
            Route::resource($resource, $controller);
            Route::patch($resource . '/delete/group', $controller . '@deleteGroup');
            Route::match(["put", "patch"], "$resource/{id}/status", "$controller@updateStatus");
            Route::match(["put", "patch"], "$resource/order/list", "$controller@order");
            Route::match(["post", "patch"], (str_contains($resource, '-') ? str_replace('-', '_', $resource) : $resource) . "/import", "$controller@import");
            if (is_callable($function))
                Route::group(['prefix' => $resource], function () use ($function, $controller, $resource) {
                    call_user_func($function, $controller, $resource);
                });
            return $this;
        });
    }

    /**
     * call authApiRoutes Macro
     *
     * @author WeSSaM
     */
    public function authApiRoutes()
    {
        Route::macro('authApiRoutes', function ($controller = "AuthController") {
            /**
             * Generate module authentication routes
             * Default controller AuthController
             *
             * @param string $controller
             * @return string
             * @author WeSSaM
             */
            Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () use ($controller) {
                Route::post('login', ['as' => 'login', 'uses' => "$controller@login"]);
                Route::get('logout', ['as' => 'logout', 'uses' => "$controller@logout"]);
                Route::get('refresh', ['as' => 'refresh', 'uses' => "$controller@refresh"]);
            });
            return $this;
        });
    }


    /**
     * call uploadingRoutes Macro
     *
     * @author WeSSaM
     */
    public function uploadingRoutes()
    {
        Route::macro('uploadingRoutes', function ($middleware = "", $controller = "AttachmentController") {
            /**
             * Generate module uploading routes
             * Default controller AttachmentController
             *
             * @param string $controller
             * @return string
             * @author WeSSaM
             */
            Route::group(['prefix' => 'upload', 'as' => 'upload.', 'middleware' => $middleware], function () use ($controller) {
                Route::post('image', ['as' => 'image_', 'uses' => "$controller@imageUpload"]);
                Route::post('imageUploadBase64', ['as' => 'image', 'uses' => "$controller@imageUploadBase64"]);
                Route::post('file', ['as' => 'file', 'uses' => "$controller@fileUpload"]);
            });
        });
    }


    /**
     * call appends Method
     *
     * @author WeSSaM
     */
    public function callAppendsRoutes()
    {
        Route::macro('appends', function ($resource, $controller, $function) {

            if (!is_callable($function))
                return;

            Route::group(['prefix' => $resource], function () use ($function, $controller, $resource) {
                call_user_func($function, $controller, $resource);
            });

        });
    }




}
