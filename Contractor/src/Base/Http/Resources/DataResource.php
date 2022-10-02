<?php

 namespace Contractor\Base\Http\Resources;


use function PHPUnit\Framework\isNull;

class DataResource
{


    private $resource;


    public function __construct($resource)
    {


        if (is_object($this->resource))
            $this->resource = (object)$resource;

        $this->resource = (object)$resource;
    }

    public function __get($name)
    {
        if ($this->isTranslateable($this->resource->{$name}))
            return $this->getLocaleValue($this->resource->{$name});

        return $this->resource->{$name};
    }


    function is_multi($a)
    {
        if (!is_array($a))
            return false;
        $rv = array_filter($a, 'is_array');
        if (count($rv) > 0) return true;
        return false;
    }

    public function toArray()
    {


        $data = [];

        foreach ($this->resource as $key => $value) {


            if (is_array($value) && !$this->isTranslateable($value)) {

                if ($this->is_multi($value))
                    $data[$key] = collect($value)->map(function ($nested) {
                        return (new DataResource($nested))->toArray();
                    })->toArray();
                else
                    $data[$key] = $value;


            } else

                $data[$key] = $this->{$key};


        }


        return $data;
    }


    public function __sanitizeRow($value)
    {
        if ($this->isTranslateable($value))
            $value = $this->getLocaleValue($value);

        return $value;
    }


    public function getLocaleValue($attribute)
    {
        $locale = app()->getLocale();

        return $attribute && array_key_exists($locale, $attribute) ? $this->validLangAttribute($attribute, $locale) : $this->defaultLocale($attribute);
    }

    public function isTranslateable($value)
    {


        return is_array($value) && array_intersect(array_keys($value ?? []), array_keys(config('languages')) ?? []);


    }

    public function defaultLocale($attribute)
    {

        $initLang = array_keys($attribute)[0];
        return $attribute && array_key_exists($initLang, $attribute) ? $this->validLangAttribute($attribute, $initLang) : "";

    }

    public function validLangAttribute($attribute, $lang)
    {
        return $attribute[$lang] ? $attribute[$lang] : null;
    }


    public function __getResource()
    {
        return $this->resource;
    }


}
