<?php

 namespace Contractor\Base\Interfaces;

use Illuminate\Http\Request;

interface BaseInterface
{
    /**
     * get specific resources' columns
     *
     * @param array $cols
     * @return mixed
     * @author WeSSaM
     */
    public function get($cols = ['*']);

    /**
     * get single resource according to
     * the given id
     *
     * @param $id
     * @return mixed
     * @author WeSSaM
     */
    public function find($id);

    /**
     * get all resources
     *
     * @return mixed
     * @author WeSSaM
     */
    public function all();

    /**
     * take $limit no from query
     * this function accepts passing custom query
     * default $query is null
     *
     * @param int $limit
     * @param  $q
     * @return mixed
     * @author WeSSaM
     */
    public function take($limit = 10, $q = null);

    /**
     * take $limit no from query
     * this function accepts passing custom query
     * default $query is null
     *
     * @param int $limit
     * @param  $q
     * @return mixed
     * @author WeSSaM
     */
    public function takeWithResource($limit = 10, $q = null);

    /**
     * delete resource according
     * to the given id
     *
     * @param $id
     * @return mixed
     *
     * @author WeSSaM
     */
    public function delete($id);

    /**
     * create new resource
     *
     * @param Request $request
     * @return mixed
     *
     * @author WeSSaM
     */
    public function store(Request $request);

    /**
     * update resource
     *
     * @param Request $request
     * @param $id
     * @return mixed
     *
     * @author WeSSaM
     */
    public function update(Request $request, $id);
}
