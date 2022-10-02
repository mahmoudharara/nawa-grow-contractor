<?php

namespace NawaGrow\Contractor\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use Spatie\Translatable\HasTranslations;

trait HasModel
{
    use HasTranslations, HasFilters;

    public $translatable = [];

    public $imageable = [];

    public $columnsForSheets = [];

    public $translatableForImporting = [];

    /**
     * @return array
     * @author WeSSaM
     */
    public function getTranslatable(): array
    {
        return $this->translatable;
    }

    /**
     * @param array $translatable
     * @author WeSSaM
     */
    public function setTranslatable(array $translatable): void
    {
        $this->translatable = $translatable;
    }

    /**
     * @return array
     */
    public function getImageable(): array
    {
        return $this->imageable;
    }

    /**
     * @param array $imageable
     */
    public function setImageable(array $imageable): void
    {
        $this->imageable = $imageable;
    }

    public function scopeSearch($q, Request $request)
    {
        return $q;
    }

    public function combinePivot($entities, $pivots = [])
    {
        // Set array
        $pivotArray = [];
        // Loop through all pivot attributes
        foreach ($pivots as $pivot => $value) {
            // Combine them to pivot array
            $pivotArray += [$pivot => $value];
        }
        // Get the total of arrays we need to fill
        $total = count($entities);
        // Make filler array
        $filler = array_fill(0, $total, $pivotArray);
        // Combine and return filler pivot array with data
        return array_combine($entities, $filler);
    }

    public function scopeDateBetween($q, $from, $to,$attribute='created_at')
    {
        return isset($from) && $to ? $q->whereDate($attribute, '>=', $from)->whereDate($attribute, '<=', $to) : $q;
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('ordered', 'asc');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }

    /***
     * @param $key
     * @return mixed
     * find status model by key status
     */



    public function setColumnsForSheets($columns)
    {
        $this->columnsForSheets = $columns;
    }

    public function getColumnsForSheets()
    {
        return $this->columnsForSheets ?: $this->getFillable();
    }

    public function setTranslatableForImporting($columns)
    {
        $this->translatableForImporting = $columns;
    }

    public function getTranslatableForImporting()
    {
        return $this->translatableForImporting;
    }
}
