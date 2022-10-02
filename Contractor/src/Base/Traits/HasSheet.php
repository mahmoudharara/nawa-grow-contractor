<?php

namespace NawaGrow\Contractor\Base\Traits;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Export\BaseExport;

trait HasSheet
{
    public $callableCollectionMethod = 'serializeForEdit';
    public $excelResource ;
    public $sheetTitle ;
    public function __construct()
    {
        parent::__construct();

       $this->excelResource=$this->getExcelResource();
    }

    /***
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        $request->validate($this->__getRequest()->rules(), $this->__getRequest()->messages());
        $model = $this->__getRepository()->insert($request);
        return response()->api(SUCCESS_STATUS, trans('core::messages.import_successfully', ['attribute' => $this->alertMessage()]), $model, []);
    }

    public function exportExcel()
    {
        @ini_set('max_execution_time', 300);
        @ini_set('memory_limit', '512M');
        $filename=  createUniqueFilename();


        Excel::store(new BaseExport($this->collectedDataForSheets(), $this->columnsForSheets(), $this->sheetTitle?:strtolower(class_basename($this->model))), "/reports/$filename");
        return response()->api(SUCCESS_STATUS, trans('core::messages.excel_exported_successfully'), ['filename' => $filename]);
    }

    public function exportPdf()
    {
        ini_set("pcre.backtrack_limit", "5000000");
        $path_file = genratePdf($this->viewPdf,$this->columnsForSheets(),$this->collectedDataForSheets(),$this->model);
        return response()->api(SUCCESS_STATUS, trans('core::messages.excel_exported_successfully'), ['filename' => $path_file]);
    }

    public function export($fileName)
    {
        return response()->download(storage_path('app/reports/' . $fileName));
    }

    public function collectedDataForSheets()
    {
        $resource = $this->getExcelResource()?:$this-> $this->__getRepository()->getResource();
        if (request()->get('id')) {
            $model = $this->__getRepository()->find(request()->get('id'));
            $resource = $this->__getRepository()->getResource();
            return (new $resource($model))->{$this->callableCollectionMethod}(request());
        }
        return $resource::Collection($this->__getRepository()->getProcessedQuery()->get())->toArray(request(),$this->callableCollectionMethod);
    }

    public function columnsForSheets(): array
    {
        return ((new $this->model)->getColumnsForSheets()) ?? ((new $this->model)->getFillable());
    }

    public function getExcelResource()
    {
        return $this->excelResource;
    }

    public function getSheetTitle()
    {
        return $this->sheetTitle;
    }
}
