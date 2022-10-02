<?php


namespace NawaGrow\Contractor\Base\Repositories\Api;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NawaGrow\Contractor\Base\Http\Resources\BaseResource;
use NawaGrow\Contractor\Base\Interfaces\BaseInterface;
use NawaGrow\Contractor\Base\Repositories\ImageRepository;


class BaseRepository extends GeneralRepository implements BaseInterface
{


    public $imageRepository;

    /**
     * repo. model
     * @author WeSSaM
     * @var
     */
    public $model;

    /**
     * repo. model
     * @author WeSSaM
     * @var
     */
    public $resource = BaseResource::class;

    /**
     * relation returned with every fetch query
     * @author WeSSaM
     * @var array
     */
    public $with = [];

    /**
     * relation returned just with  ( find ) query
     * @author WeSSaM
     * @var array
     */
    public $findWith = [];

    /**
     * relation returned just with  ( all ) query
     * @author WeSSaM
     * @var array
     */
    public $allWith = [];


    /**
     * default pagination per page
     * @author WeSSaM
     */
    const DEFAULT_PAGINATION = 15;

    /**
     * default pagination per page
     * @author WeSSaM
     */
    public $defaultPagination = self::DEFAULT_PAGINATION;


    /**
     * table's columns used to order lists
     * @author WeSSaM
     */
    const ORDERED_COLUMN = 'created_at';

    /**
     * order's direction
     * @author WeSSaM
     */
    const ORDER = 'desc';

    /**
     * table's columns used to order lists
     * @author WeSSaM
     */
    public $orderBy = self::ORDERED_COLUMN;


    /**
     * table's columns used to order lists
     * @author WeSSaM
     */
    public $sorting = self::ORDER;


    /**
     * activation key
     *
     * @author WeSSaM
     * @var array
     */
    public $status = "is_active";

    /**
     * custom query builder
     *
     * @author WeSSaM
     * @var array
     */
    private $query;

    /**
     * default authenticated user guard
     * @author WeSSaM
     */
    public $GUARD = 'api';

    /**
     * BaseRepository constructor.
     * @param ImageRepository|null $imageRepository
     * @author WeSSaM
     */
    public function __construct(ImageRepository $imageRepository = null)
    {
        $this->imageRepository = $imageRepository;
        parent::__construct();
    }

    /**
     * get specific resources' columns
     *
     * @param array $cols
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function get($cols = ['*'])
    {
        return $this->getResource()::collection($this->getProcessedQuery()->get($cols));
    }

    /**
     * get single resource according to
     * the given id
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function find($id)
    {

        return $this->getQuery()->with($this->getFindWith())->find($id);
    }


    /**
     * get single resource according to
     * the given id
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function groupBy($attribute)
    {

        return $this->getQuery()->leftJoin('services', 'services.id', '=', 'orders.service_id')
            ->select($attribute, 'services.name', 'service_id', DB::raw('count(*) as total'))->groupBy($attribute, 'service_id');
    }


    /**
     * take $limit no from query
     * this function accepts callback function for query customizing
     * default $function is null
     *
     *
     * @param int $limit
     * @param  $function
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function take($limit = 10, $function = null)
    {
        if (isset($function))
            return call_user_func($function, $this->getQuery());
        return $this->getQuery()->take($limit)->get();
    }

    /**
     * last $limit no from query
     * this function accepts callback function for query customizing
     * default $function is null
     *
     *
     * @param int $limit
     * @param  $function
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function last($count = 10, $function = null)
    {
        if (isset($function))
            return call_user_func($function, $this->getQuery());

        return $this->getResource()::collection($this->getQuery()->latest()->get()->take($count));


    }


    /**
     * take $limit no from query
     * this function accepts callback function for query customizing
     * default $function is null
     * result will passes through repo.'s resource
     *
     *
     * @param int $limit
     * @param $function
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function takeWithResource($limit = 10, $function = null)
    {
        return $this->getResource()::collection($this->take($limit, $function));
    }

    /**
     * customized query
     *
     * @param $function
     * @return $this
     * @throws \Exception
     */
    public function customQuery($function)
    {
        $this->query = $this->getQuery()->where($function);
        return $this;
    }

    /**
     * get all resources
     *
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function all()
    {
        $query = $this->getProcessedQuery();
        if (request()->has('no_pagination'))
            return $this->getResource()::collection($query->get());

        $result = $query->paginate($this->getDefaultPagination());
        $resourceResult = $this->getResource()::collection($result->getCollection());
        $result->setCollection(collect($resourceResult));
        return $result;
    }


    /**
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function getProcessedQuery()
    {
        return $this->getQuery()->with($this->getAllWith())->orderBy($this->getOrderBy(), $this->getSorting());
    }

    /**
     * delete resource according
     * to the given id
     *
     * @param $id
     * @return mixed
     *
     * @author WeSSaM
     */
    public function delete($id)
    {
        $model = $this->model::findOrFail($id);
        $model->delete();
        return $model;
    }

    /**
     * delete resource according
     * to the given id
     *
     * @param Request $request
     * @return mixed
     *
     * @author WeSSaM
     */
    public function deleteGroup(Request $request)
    {
        $model = $this->model::whereIn('id', $request->selected)->delete();
        return $model;
    }

    /**
     * create new resource
     *
     * @param Request $request
     * @return mixed
     *
     * @throws \Modules\Core\Exceptions\UploadErrorException
     * @author WeSSaM
     */
    public function store(Request $request)
    {

        $this->beforeCreate($request);
        return $this->created($this->__getModel()->create($this->getCreateAttributes($request)), $request);
    }

    /**
     * Create Attributes
     */
    public function getCreateAttributes(Request $request)
    {

        return $this->getAttributes($request);
    }

    /**
     * Create Attributes
     */
    public function getUpdateAttributes(Request $request)
    {

        return $this->getAttributes($request);
    }


    /**
     * update resource
     *
     * @param Request $request
     * @param $id
     * @return mixed
     *
     * @throws \Exception
     * @author WeSSaM
     */
    public function update(Request $request, $id)
    {

        $model = parent::getQuery()->findOrFail($id);

        $this->beforeUpdate($request, $model);
        $model->fill($this->getUpdateAttributes($request));
        $model->update();

        return $this->updated($model->refresh(), $request);
    }

    /**
     * get find with relations
     * @return array
     * @author WeSSaM
     */
    public function getFindWith()
    {
        return array_merge($this->with, $this->findWith);
    }

    /**
     * get find with relations
     * @return array
     * @author WeSSaM
     */
    private function getAllWith()
    {
        return array_merge($this->with, $this->allWith);
    }

    /**
     * you can shape the given attributes of request
     * as you want here by overriding this method
     *
     * @param Request $request
     * @return array
     * @throws \Modules\Core\Exceptions\UploadErrorException
     * @author WeSSaM
     */
    protected function

    getAttributes(Request $request)
    {

        $data = $this->__serializeAttributes($request->all());

        if (isset($request->password) && $request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        return array_merge($data, $this->sanitizeImages($request));
    }


    /**
     * prepare translated attributes for saving
     *
     * @param Request $request
     * @return array
     * @author WeSSaM
     */
    public function parseTranslatedAttributes(Request $request)
    {
        $data = [];
        foreach ($this->getTranslatedAttributes() as $translatedAttribute) {
            $value = [];
            foreach (locales() as $locale => $language) {
//                if ($request->get($translatedAttribute . '_' . $locale))
                $value[$locale] = $request->get($translatedAttribute . '_' . $locale) ?? null;
            }

            if ($value)
                $data[$translatedAttribute] = ($value);
        }

        return $data;
    }


    /**
     * @param Request $request
     * @return array
     * @throws \Modules\Core\Exceptions\UploadErrorException
     * @author WeSSaM
     */
    public function sanitizeImages(Request $request)
    {
        $images = [];
        foreach ($this->getImageableAttributes() as $attribute) {

            if (in_array($attribute, $this->getTranslatedAttributes()))
                $images[$attribute] = $this->saveMultiLanguageImage($attribute, $request);

            else if (isset($request->$attribute) && strlen($request->$attribute) > 40&&!$request->hasFile($attribute))
                $images[$attribute] = $this->imageRepository->uploadBase64($request->$attribute)->file_name;

            else if (isset($request->$attribute) && $request->hasFile($attribute))
                $images[$attribute] = $this->imageRepository->upload($request->$attribute)->file_name;
        }
        return $images;
    }

    /**
     * get model class for this repo
     *
     * @return mixed
     * @author WeSSaM
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * set new model for this repo's instance
     *
     * @param mixed $model
     * @return BaseRepository
     * @author WeSSaM
     */
    public function setModel($model): BaseRepository
    {
        $this->model = $model;
        return $this;
    }

    /**
     * check if request has order key
     * if exist will return new key
     * else default value
     *
     * @return array|Request|string
     * @author WeSSaM
     */
    public function getOrderBy()
    {
        return request()->has('order_by') ? request('order_by') : $this->orderBy;
    }

    /**
     * @param $orderBy
     * @return BaseRepository
     * @author WeSSaM
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * sort by asc or desc based on request
     * default is desc
     *
     * @return string
     * @author WeSSaM
     */
    public function getSorting()
    {
        return request()->has('sort_by') ? request('sort_by') : $this->sorting;
    }

    /**
     * @param mixed $sorting
     * @return BaseRepository
     * @author WeSSaM
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPagination(): int
    {
        return request()->get('perPage') ?? $this->defaultPagination;
    }

    /**
     * @param int $defaultPagination
     * @return BaseRepository
     */
    public function setDefaultPagination(int $defaultPagination)
    {
        $this->defaultPagination = $defaultPagination;
        return $this;
    }


    /**
     * get data after applying resource changes
     * @param $resource
     * @param $data
     * @return mixed
     * @author WeSSaM
     */
    protected function getCollection($resource, $data)
    {
        if (!($data instanceof LengthAwarePaginator))
            return $resource::collection($data);
        $collection = $data->getCollection();
        $data->setCollection($resource::collection($collection)->collect());
        return $data;
    }

    /**
     * get model's resource
     * @return String
     * @author WeSSaM
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * returns the original source of data
     * @param $id
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    function findOrFail($id)
    {
        return $this->getQuery()->findOrFail($id);
    }

    /**
     * update the status of resource
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function updateStatus(Request $request, $id)
    {
        $statusAttribute = $this->status;
//        $request->validate(["$statusAttribute" => "required|boolean"]);
        $model = $this->model::findOrFail($id);//$this->find($id);
        $model->$statusAttribute = (int)$request->input("$statusAttribute");
        $model->update();
        return $model->refresh();
    }

    /**
     * update the default of resource
     *
     * @param Request $request
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function changeDefault(Request $request, $id)
    {
        $request->validate(['is_default' => "required|boolean"]);
        $model = $this->model::findOrFail($id);//$this->find($id);
        $model->is_default = $request->input('is_default');
        $model->update();
        return $model->refresh();
    }

    /**
     * serialize attributes to be saved safely
     * @param $data
     * @return array
     * @author WeSSaM
     */
    function __serializeAttributes($data)
    {

        $data = collect($data);

        return $data->map(function ($item) {
            if (gettype($item) == 'array' && array_key_exists('id', $item)) {

                return $item['id'];
            }

            return $item;
        })->toArray();
    }


    /**
     * upload multi language image based on system locales
     * return json encoded string for saving
     *
     * @param $attribute
     * @param Request $request
     * @return false|string
     */
    public function saveMultiLanguageImage($attribute, Request $request)
    {
        return collect(locales())->map(function ($language, $locale) use ($request, $attribute) {
            $attribute = $attribute . "_" . $locale;
            return isset($request->$attribute) && strlen($request->$attribute) > 40 ? $this->imageRepository->uploadBase64($request->$attribute)->file_name : "";
        })->toArray();
    }


    /**
     * check if country id exists in model
     * if exist returned query filtered by auth user country
     *
     * @return mixed
     * @throws \Exception
     * @author WeSSaM
     */
    public function getQuery()
    {
        return $this->query ?? parent::getQuery()->search(request());
    }


    /**
     * @param $model
     * @param Request $request
     * @return mixed
     * @author WeSSaM
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
     * @author WeSSaM
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
     * @author WeSSaM
     */
    public function saving($model, Request $request)
    {
        // TODO: Implement updated() method.
        return $model;
    }

    /**
     * @param mixed $resource
     * @return
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }


    /**
     * get translated attributes based on current model
     * if not exist will return empty array
     *
     * @return array
     * @author WeSSaM
     */
    public function getTranslatedAttributes()
    {
        return isset($this->model) && (new $this->model)->getTranslatable() ? (new $this->model)->getTranslatable() : [];
    }

    /**
     * get imageable attributes based on current model
     * if not exist will return empty array
     *
     * @return array
     * @author WeSSaM
     */
    public function getImageableAttributes()
    {
        return isset($this->model) && (new $this->model)->getImageable() ? (new $this->model)->getImageable() : [];
    }


    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws AuthenticationException
     * @author WeSSaM
     */
    public function user()
    {
        if ($this->guard()->check())
            return $this->guard()->user();

        throw new AuthenticationException(trans('core::messages.un_authenticated'), UNAUTHENTICATED_ERROR);
    }

    /**
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     * @author WeSSaM
     */
    public function guard()
    {

        return auth($this->GUARD);
    }


    /**
     * @param Request $request
     * @author WeSSaM
     */
    public function beforeCreate(Request $request)
    {
        // TODO: Implement beforeCreate() method.
        return $this->beforeSaving($request);
    }

    /**
     * @param Request $request
     * @author WeSSaM
     */
    public function beforeSaving(Request $request)
    {
        // TODO: Implement beforeSaving() method.
    }


    /**
     * @param Request $request
     * @param $model
     * @author WeSSaM
     */
    public function beforeUpdate(Request $request, $model)
    {
        // TODO: Implement beforeUpdate() method.

        return $this->beforeSaving($request);
    }

    public function collectingDataForImport(Request $request,$appendFillable = [])
    {
        $model = new $this->model();
        $translatedFillable = $model->getTranslatable();
        $fillable = array_merge($model->getFillable(),$appendFillable);
        $locale = app()->getLocale();
        $this->data = [];

        collect($request->data)->map(function ($item) use ($model, $fillable, $translatedFillable, $locale) {

            foreach ($translatedFillable as $translated) {
                if ($model->getTranslatableForImporting() && in_array($translated, $model->getTranslatableForImporting()))
                    $item[$translated] = json_encode(['ar' => ($item[$translated . '_ar'] ?? null), 'en' => ($item[$translated . '_en'] ?? null)]);
                else
                    $item[$translated] = json_encode([$locale => $item[$translated] ?? []]);
            }
            $this->data[] = collect($item)->only($fillable)->all();
        });
        return $this->data;
    }

    public function insert(Request $request)
    {
        return $this->model::insert($this->collectingDataForImport($request));
    }

    public function filterByJson(Builder $q, $attribute, $value, $operator = "LIKE", $query = 'whereRaw')
    {
        $q = isset($value) ? $q->{$query}('LOWER(JSON_EXTRACT(' . $attribute . ', "$.' . app()->getLocale() . '")) like ?', ['"%' . strtolower($value) . '%"']) : $q;
        return $q;
    }
}
