<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 31/1/2021
 * Time: 6:05 م
 */

 namespace Contractor\Base\Repositories;


use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\FirestoreClient;
useContractor\Base\Interfaces\FirestoreInterface;


class FirestoreRepository implements FirestoreInterface
{
    public $firestore;


    /**
     * FirestoreRepository constructor.
     * @param FirestoreClient $firestoreClient
     */
    public function __construct(FirestoreClient $firestoreClient)
    {
        $this->firestore = $firestoreClient;
    }


    /**
     * return the root of firestore database reference
     *
     * @return mixed
     * @author WeSSaM
     */
    public function database()
    {
        return $this->firestore;
    }


    /**
     * @param $path
     * @return mixed
     * @throws NotFoundException
     */
    public function getRef($path)
    {
        $databaseRef = $this->database();
        foreach ($this->sanitizeRefs($path) as $sanitizeRef) {
            if ($this->checkReferenceMethod($databaseRef, self::COLLECTION))
                $databaseRef = $databaseRef->collection($sanitizeRef);
            else if ($this->checkReferenceMethod($databaseRef, self::DOCUMENT))
                $databaseRef = $databaseRef->document($sanitizeRef);
            else
                throw new NotFoundE(trans('path_not_found'), RESOURCE_NOT_FOUND);
        }
        return $databaseRef;
    }


    /**
     * @param $path
     * @param array $data
     * @return mixed
     * @throws NotFoundException
     * @author WeSSaM
     */
    public function create($path, $data = array())
    {
//        if ($this->getRef($ref))
//        try {
            return $this->getRef($path)->create($data);
//        } catch (ConflictException $conflictException) {
//            return $this->getRef($path)->update(
//                [
//                    'path' => 'messages',
//                    'key' => '2222',
//                    'value' => $data
//                ]);
//        }

    }


    /**
     * @param $path
     * @param array $data
     * @return mixed
     * @throws NotFoundException
     * @author WeSSaM
     */
    public function update($path, $data = array())
    {
        return $this->getRef($path)->update(
            $data
        );
    }


    /**
     * @param $path
     * @param array $data
     * @return mixed
     * @throws NotFoundException
     * @author WeSSaM
     */
    public function set($path, $data = array())
    {
        return $this->getRef($path)->set(
            $data
        );
    }



    /**
     * @param $path
     * @return \Google\Cloud\Firestore\QuerySnapshot|mixed
     * @throws NotFoundException
     * @author WeSSaM
     */
    public function get($path)
    {
        $ref = $this->getRef($path);
        return method_exists($ref, 'snapshot') ? $this->document($ref) : $this->collection($ref);
    }

    /**
     * @param DocumentReference $ref
     * @return mixed
     * @author WeSSaM
     */
    public function document(DocumentReference $ref)
    {
        return $ref->snapshot()->data();
    }


    /**
     * @param $ref
     * @param $method
     * @return bool
     * @author WeSSaM
     */
    public function checkReferenceMethod($ref, $method)
    {
        return method_exists($ref, $method);
    }


    /**
     * @param CollectionReference $ref
     * @return \Illuminate\Support\Collection
     * @author WeSSaM
     */
    public function collection(CollectionReference $ref)
    {
        return collect($ref->documents())->map(function ($document) {
            return $document->data();
        });
    }


    /**
     * @param $path
     * @return \Illuminate\Support\Collection
     * @author WeSSaM
     */
    public function sanitizeRefs($path)
    {
        return collect(explode(self::PATH_DELIMITER, $path));
    }
}
