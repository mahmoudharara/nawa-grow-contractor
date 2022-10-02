<?php

namespace NawaGrow\Contractor\Base\Traits;


use Illuminate\Database\Eloquent\Builder;


trait HasFilters
{
    /**
     * get Query
     * if user passed filter
     * otherwise
     * return the model
     * @return mixed
     * @throws \Exception
     */
    public function getQuery()
    {
//        if ($this->__filterResourceName() != null && request()->get($this->__filterResourceName()) != null) {
//            $filters = (new Filter(request()->all()))->reformStructure();
//            return $this->buildFilterQuery($filters);
//        }
        return $this->originalQuery();
    }

    function get_resource_name($classPath)
    {
        $pathPartials = explode('\\', $classPath);
        return strtolower(end($pathPartials));
    }


    /**
     * return new Query from model
     *
     * @return mixed
     * @author WeSSaM
     */
    public function originalQuery()
    {
        return $this->__getModel();
    }

    /**
     * get filter resource name attribute from request
     *
     * @return string|mixed
     * @author WeSSaM
     */
    public function __filterResourceName()
    {
        if (request()->has($this->get_resource_name($this->model) . '_filter'))
            return get_resource_name($this->model) . '_filter';
        return request()->has('filter') ? 'filter' : null;
    }

    /**
     * build query according
     * to the given data
     *
     * @param $filters
     * @return mixed
     * @author WeSSaM
     */
    private function buildFilterQuery($filters)
    {
        $query = $this->__getModel()->newQuery();
        if ($filters['query'] != null && trim($filters['query']) != '')
            $query = $query->whereRaw($filters['query'], $filters['values']);

        if (isset($filters['relations']) && sizeof($filters['relations']) > 0) {
            foreach ($filters['relations'] as $key => $relation) {
                $query = $query->whereHas($key, function ($query) use ($relation) {
                    $query->whereRaw($relation['query'], $relation['values']);
                });
            }
        }
        return $query;
    }


    /**
     * get the serialized filters
     * @return bool
     * @author WeSSaM
     */
    protected function getFilters()
    {
        return request()->has('filter');
    }


    /**
     * @param Builder $q
     * @param $attribute
     * @param $value
     * @param string $operator
     * @return Builder
     */
    public function filterByJson(Builder $q, $attribute, $value, $operator = "LIKE", $query = 'whereRaw')
    {
        $q = isset($value) ? $q->{$query}('LOWER(JSON_EXTRACT(' . $attribute . ', "$.' . app()->getLocale() . '")) like ?', ['"%' . strtolower($value) . '%"']) : $q;
        return $q;
    }

    public function filterByKey(Builder $q, $attribute, $value, $operator = "LIKE", $query = 'where')
    {
        return isset($value) ? $q->{$query}($attribute, $operator, $value) : $q;
    }

    public function filterByRelation($q, $relation, $attribute, $value, $query_method = 'filterByJson')
    {
        return $q->whereHas($relation,function ($inner) use($attribute,$value,$query_method) {
            $this->{$query_method}($inner,$attribute,$value);
        });
    }
}
