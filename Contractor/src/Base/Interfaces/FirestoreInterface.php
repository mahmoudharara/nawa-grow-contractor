<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 31/1/2021
 * Time: 6:05 م
 */

 namespace Contractor\Base\Interfaces;


interface FirestoreInterface
{

    const COLLECTION = "collection";
    const DOCUMENT = "document";
    const TYPE_DELIMITER = ":";
    const PATH_DELIMITER = "/";

    /**
     * @return mixed
     */
    public function database();
}
