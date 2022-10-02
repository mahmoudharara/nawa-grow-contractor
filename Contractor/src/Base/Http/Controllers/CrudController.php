<?php

namespace NawaGrow\Contractor\Base\Http\Controllers;



use App\Http\Controllers\Controller;
use App\Repositories\DatatableRepository;
use App\Repositories\ModelRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CrudController extends Controller
{

    protected $model = Model::class;
    protected $authMiddleware = 'auth';
    protected $listFileName = 'all';
    protected $createFileName = 'create';
    protected $showFileName = 'show';
    protected $module;
    protected $resource;
    protected $repository;
    protected $repositoryClass = ModelRepository::class;
    protected $request = Request::class;
    protected $datatableRepository;

    /**
     * CrudController constructor.
     * @author Emad
     */
    public function __construct()
    {
        if ($this->authMiddleware)
            $this->middleware($this->authMiddleware);
        $this->repository = App::make($this->repositoryClass);
        if ($this->repositoryClass == ModelRepository::class)
            $this->repository->setModel($this->model);
        $this->datatableRepository = new DatatableRepository($this->model);

        return parent::__construct();
    }

    /**
     * @author Emad
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(implode([$this->module, $this->resource, $this->listFileName], '.'), $this->indexPayload());
    }


    /**
     * @author Emad
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {

        return view(implode([$this->module, $this->resource, $this->createFileName], '.'), $this->createPayload($request));
    }



    /**
     * @author Emad
     * @param Request $request
     * @param array $args
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, ...$args)
    {

        $data['item'] = $this->repository->find(@$args[0]);

        return view(implode([$this->module, $this->resource, $this->createFileName], '.'), $this->editPayload($request, $data));
    }


    /**
     * @author Emad
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store()
    {

        $request = $this->callValidationRequest();

        $this->created($this->repository->store($this->getRequest($request)), $this->getRequest($request));
        return $this->response_api(true, trans('messages.added_successfully', ['attribute' => $this->resource]));
    }

    /**
     * @author Emad
     * @param array $args
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(...$args)
    {
        $request = $this->callValidationRequest();
        $this->updated($this->repository->update($this->getRequest($request), ...$args), $this->getRequest($request));
        return $this->response_api(true, trans('messages.edited_successfully', ['attribute' => $this->resource]));
    }

    /**
     * @author Samer
     * @param array $args
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete(...$args)
    {
        $this->repository->find(@$args[0])->delete();
        return $this->response_api(true, trans('messages.done_successfully', ['attribute' => $this->resource]));
    }


    /**
     * @author Emad
     * @param Request $request
     * @return Request
     */
    public function getRequest(Request $request)
    {
        return $request;
    }


    /**
     * @author Emad
     * @param mixed ...$args
     * @return array
     */
    public function indexPayload(...$args)
    {
        return array_merge($args, $this->appendsToPayload());
    }

    /**
     * @author Emad
     * @param Request $request
     * @param mixed ...$args
     * @return array
     */
    public function createPayload(Request $request, ...$args)
    {
//        dd( $this->appendsToPayload());
        return array_merge($request->all(), $args, ['key' => $request->lang ?? 'ar', 'lang' => App::getLocale() ?? 'ar'], $this->appendsToPayload());
    }

    /**
     * @author Emad
     * @param Request $request
     * @param mixed ...$args
     * @return array
     */
    public function editPayload(Request $request, ...$args)
    {
        return array_merge($request->all(), array_merge(...$args), ['key' => ' ', 'lang' => 'ar'], $this->appendsToPayload());
    }

    /**
     * @author Emad
     * @param array $array
     * @return array
     */
    public function appendsToPayload($array = [])
    {
        $array['lang'] = App::getLocale();
        return $array;
    }

    /**
     * @author Emad
     * @param Request $request
     * @return mixed
     */
    public function getDataTable(Request $request)
    {
        $datatable = $this->datatableRepository->datatableParams();
        $items = $this->datatableQuery($request)->orderByDesc('created_at')->take($datatable['perpage'])->skip($datatable['skip'])->get();
        $datatable['total'] = $this->datatableQuery($request)->count();
        $datatable['pages'] = ceil($datatable['total'] / $datatable['perpage']);
        $data['meta'] = $datatable;
        $data['data'] = $items;
        return $data;
    }

    /**
     * @author Emad
     * @param Request $request
     * @return mixed
     */
    public function datatableQuery(Request $request)
    {
        return $this->repository->query()->search($request);
    }

    /**
     * @param $model
     * @param Request $request
     * @return mixed
     */
    public function created($model, Request $request)
    {
        // TODO: Implement created() method.
        return $this->saving($model, $request);
    }

    /**
     * @param $model
     * @param Request $request
     * @return mixed
     */
    public function updated($model, Request $request)
    {
        // TODO: Implement updated() method.
        return $this->saving($model, $request);
    }

    /**
     * @param $model
     * @param Request $request
     * @return mixed
     */
    public function saving($model, Request $request)
    {
        // TODO: Implement updated() method.
        return $model;
    }


    /**
     * @author Emad
     * @return mixed
     */
    public function callValidationRequest()
    {
        return App::make($this->request);
    }

    public function active(...$args)
    {

        $item = $this->repository->find(@$args[0]);
        if ($item->is_active == 1) {
            $is_active = '0';

        } else {
            $is_active = '1';
        }

        return (isset($item) && $item->update(['is_active' => $is_active])) ? $this->response_api(true, trans('messages.added_successfully')) : $this->response_api(false, 'حدث خطأ أثناء المعالجة');
    }


    public function SendNotification($tokens = [])
    {
// prepare the message
        $url = 'https://fcm.googleapis.com/fcm/send';
        $message = array(
            'title' => 'This is a title.',
            'body' => 'Here is a message.',
        );

        $fields = array(
            'registration_ids' => $tokens,
            'data' => $message
        );

        $headers = array(
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
//    public function find(Request $request, ...$args)
//    {
//
//        $item = $this->repository->find(@$args[0]);
//        if ($item)
//            return $this->response_api(true, trans('messages.done_successfully'), $item);
//
//        return  $this->response_api(false, trans('messages.done_successfully'), [ ]);
//    }
}
