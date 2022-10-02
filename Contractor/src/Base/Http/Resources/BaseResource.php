<?php


namespace NawaGrow\Contractor\Base\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function serializeForEdit($request)
    {

        return array_merge($this->toArray($request), $this->sanitizeTranslations($request));
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function serializeForFind($request)
    {
        return $this->toArray($request);
    }


    /**
     * returns concatenation of translations attributes and locales with translated value
     * if attribute is image or file appends url
     *
     *
     * @param Request $request
     * @return array
     * @author WeSSaM
     */
//  public function sanitizeTranslations(Request $request)
//  {
//    return $this->translations;
//    $translations = [];
//
//
//    foreach ($this->translations as $attribute => $translation) {
//      foreach ($translation as $locale => $value) {
//        $translations[$attribute . "_" . $locale] = $value;
//        if (in_array($attribute, $this->getImageable()))
//          $translations[$attribute . "_" . $locale . "_url"] = $value;
//      }
//    }
//    return count($translations)>0?$translations:null;
//  }
    public function sanitizeTranslations(Request $request)
    {
        $translations = [];
        if (isset($this->translations)) {
            foreach ($this->translations as $attribute => $translation) {
                if (isset($attribute, $translation))
                    $translations[$attribute] = $this->isTranslatableAttribute($attribute) && count($this->getTranslations($attribute)) > 0 ? translationObjectHandle($this->getTranslations($attribute)) : null;
            }
        }
        return $translations;
    }


    public function getLocale($attribute)
    {
        if (!is_array($attribute))
            return $attribute; // this condition added to fix array_key_exists exception

        return $attribute && array_key_exists(app()->getLocale(), $attribute) ? $attribute[app()->getLocale()] : "";
    }

    public function getOtherLocale($attribute)
    {
        if (!is_array($attribute))
            return $attribute; // this condition added to fix array_key_exists exception

        $opposite_get_locale = app()->getLocale() === 'en' ? 'ar' : 'en';

        return $attribute && array_key_exists($opposite_get_locale, $attribute) ? $attribute[$opposite_get_locale] : "";
    }

    public function getTranslationsByModel($model, $attribute)
    {
        return isset($model) && $model->$attribute ? $model->getTranslations($attribute) : null;
    }

    public function getFromJson($attribute, $column)
    {
        return isset($attribute, $column) && is_array($this->$column) && array_key_exists($attribute, $this->$column) ? $this->getLocale($this->$column[$attribute]) : null;
    }

    public function handleStringApi($attribute)
    {
        return (isset($attribute) && strlen($attribute) > 0) ? $attribute : null;
    }
}
