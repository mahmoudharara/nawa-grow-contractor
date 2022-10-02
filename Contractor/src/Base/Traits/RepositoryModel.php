<?php


 namespace Contractor\Base\Traits;



use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;

trait RepositoryModel
{

    function __getModel()
    {
        if (!$this->model)
            throw new \Nette\NotSupportedException('Model not supported');
        $model = $this->__getClassPath($this->model);

        request()['resource_path'] = $model;
        return new $this->model;
    }


    function __getClassPath($model)
    {
        $classPath = $model;

        if (is_object($model)) {
            $classPath = get_class($model);
        }
        $isExisted = class_exists($classPath);
        if (!$isExisted)
            throw new ClassNotFoundError("Repository's model is supported", $model);
        return $classPath;
    }
}
