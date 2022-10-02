<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 24/11/2020
 * Time: 4:34 م
 */




/**
 * return current system languages
 *
 * @return array
 * @author @WeSSaM
 */
function locales()
{
    return config('languages');
}

/**
 * @return string
 * @author @WeSSaM
 */
function current_locale()
{
    return \Illuminate\Support\Facades\App::getLocale();
}

function load_resource_pagination($resourceClass , \Illuminate\Pagination\LengthAwarePaginator $paginator)
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

//if (!function_exists('get_resource_name')) {
/**
 * this function returns the name of resource which
 * is the name of object's class
 * all resource name will be returned in lower case¬
 *
 * @param $classPath
 * @return string
 * @author WeSSaM
 */
function get_resource_name($classPath)
{
    $pathPartials = explode('\\', $classPath);
    return strtolower(end($pathPartials));
}

/**
 * @param $classPath
 * @return mixed
 * @author WeSSaM
 */
function get_class_name($classPath)
{
    if (is_object($classPath))
        $classPath = get_class($classPath);
    $pathPartials = explode('\\', $classPath);
    return end($pathPartials);
}


/**
 * @param $model
 * @return string
 */
function models_path($model)
{
    return "App\\Models\\$model";
}

function prepare_error_attrs_keys($errors)
{
    $new = [];
    foreach ($errors->getMessages() as $key => $error) {
        $new[(strpos($key, '.') !== false) ? explode('.', $key)[0] : $key] = $error;
    }
    return $new;
}

function flattern($array, $delimiter)
{
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value))
            $result = array_merge($result, flattern($value, $delimiter));
        else
            $result["$key$delimiter$value"] = $value;
    }
    return $result;
}


/**
 *
 * @param $img
 * @param string $size
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 * @author WeSSaM
 */

function image_url($img, $size = '')
{
    return (!empty($size)) ? url('image/' . $size . '/' . $img) : url('image/' . $img);
}


function fileUrl($file_name)
{
    return url('file/' . $file_name);
}
function reportUrl($file_name)
{
    return url('reports/' . $file_name);
}

/**
 *
 * @param $img
 * @param string $size
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 * @author WeSSaM
 */

function constant_url($img)
{
    return url('constants/' . $img);
}

if (!function_exists('send_notification_for_models')) {
    /**
     * send notification for models or a single one
     * @param $data
     * @param null $models
     * @author Amr
     */
    function send_notification_for_models($data, $model, $topic, $models = null)
    {

        $_data = collect($data);
        $savingFlag = $_data->get('save', true);
        $data = $_data->except('save')->toArray();
        if (!$models) {

            send_notification($data, 'topic', $topic, function ($response, $_self) use ($model, $savingFlag, $topic) {
                \Firebase\FCM\Models\Notification::create(array_merge($_self->getData(), ['type' => collect($_self->getData())->get('type'), 'type_id' => (int)collect($_self->getData())->get('type_id'), 'payload' => $_self->getData(), 'topic' => $topic]));
            });

        } else if ($models instanceof \Illuminate\Database\Eloquent\Collection) {

            $models->chunk(5)->each(function ($chunk) use ($data, $topic, $savingFlag) {
                $transformedChunks = $chunk->map(function ($model) use ($topic) {
                    return "'$topic-$model->id' in Topics";
                });
                $condition = $transformedChunks->implode(' || ');
                send_notification($data, 'condition', $condition, function ($response, $_self) use ($chunk, $savingFlag) {
                    $chunk->each(function ($model) use ($response, $_self, $savingFlag) {
                        if ($savingFlag === true)
                            $model->notifications()->create(array_merge($_self->getData(), ['type' => collect($_self->getData())->get('type'), 'type_id' => (int)collect($_self->getData())->get('type_id'), 'payload' => $_self->getData()]));
                    });
                });
            });
        } else {

            send_notification($data, 'condition', "'$topic-$models->id' in Topics", function ($response, $_self) use ($models, $savingFlag) {
                if ($savingFlag === true)
                    $models->notifications()->create(array_merge($_self->getData(), ['type' => collect($_self->getData())->get('type'), 'type_id' => (int)collect($_self->getData())->get('type_id'), 'payload' => $_self->getData()]));
            });
        }
    }
}
if (!function_exists('get_formatted_number')) {

    function get_formatted_number($number, $precision = 2)
    {
        return (double)number_format($number, $precision, '.', '');

    }
}

if (!function_exists('countryImageByCode')) {

    function countryImageByCode($code = "ps")
    {
        return 'https://country-tools.com/flags/cercle/512x512/' . strtolower($code) . '.png';
    }
}
if (!function_exists('userAuth')) {


    function userAuth()
    {

        foreach (config('auth.guards') as $guard => $array) {
            if ($array['driver'] == "jwt" && auth($guard)->check())
                return auth($guard)->user();
            continue;
        }
        throw  new \Modules\Core\Exceptions\AuthenticationException;
    }
}

if (!function_exists('heckAuth')) {


    function checkAuth()
    {

        foreach (config('auth.guards') as $guard => $array) {
            if ($array['driver'] == "jwt" && auth($guard)->check())
                return \Illuminate\Support\Facades\Auth::guard($guard)->check();


        }
        return false;

    }
}
if (!function_exists('calculateDistanceMatrix')) {

    function calculateDistanceMatrix($origins, $destinations)
    {

        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('GET', 'https://maps.googleapis.com/maps/api/distancematrix/json', [
                'query' => [
                    'origins' => $origins,
                    'destinations' => $destinations,
                    'key' => env('GOOGLE_MAP_KEY', 'AIzaSyCQSHRc94B4f_7vZUIAtZKa9_UtgYMA4ok')
                ]
            ]);


            $response = json_decode($res->getBody()->getContents());


            return $response->rows[0]->elements[0]->distance->value;

        } catch (\Exception $exception) {
            return null;
        }


    }
}
if (!function_exists('handleArrayApi')) {

    function handleArrayApi($obj)
    {
        return is_string($obj) ? json_decode($obj, true) : $obj;
    }
}




