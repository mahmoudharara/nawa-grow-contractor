<?php

namespace NawaGrow\Contractor;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {


        Response::macro('api', function ($status, $message, $data = [], $extra=[], $error_code = 0, $statusCode = 200) {
            /*
             * This condition is set to unified the response of pagination according
             * to this format :
             * data => [],
             * paginator => links
             *
             * @author WeSSaM
             *
             */
            if ($data instanceof LengthAwarePaginator) {
                $data = $this->load_resource_pagination(null, $data);
                $payload = array_merge(['status' => $status, 'message' => $message, 'error_code' => $error_code], $data);
            } else {
                $payload = ['status' => $status, 'message' => $message, 'error_code' => $error_code, 'data' => $data];
            }
            if (isset($extra))
                $payload = array_merge($payload, ['extra' => count($extra)>0 ?$extra:new \stdClass()]);

            return Response::make($payload, $statusCode,
                ['Content-Type' => 'application/json']);

        });
    }
   public function load_resource_pagination($resourceClass , \Illuminate\Pagination\LengthAwarePaginator $paginator)
    {
        $data = $paginator->getCollection();
        if ($resourceClass != null) {
            $data = $resourceClass::collection($paginator->getCollection());
        }
        $result['data'] = $data;
        $temp = $paginator->toArray();
        unset($temp['data']);
        $result['paginator'] = $temp;

        return $result;
    }
}
