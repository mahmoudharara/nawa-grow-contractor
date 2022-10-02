<?php


namespace NawaGrow\Contractor\Base\Repositories\Api;


use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;

class PDFRepository extends Controller
{
  private $loadType = 'view';
  /***
   * @var
   */
  private $resource;
  /***
   * @var
   *
   */
  private $data;
  /***
   * @var
   * private model
   * @author mahmoud
   */

  private $model;


  /**
   * @param $model
   * @author mahmoud
   *
   */
  private $query;
  /***
   * set base directory
   * @var
   */

  /***
   * @var
   * @author mahmoud
   */
  private $view;

  /***
   * @var
   * @author mahmoud
   */
  private $file;


  /***
   * @var
   * @author mahmoud
   */
  private $html;

  /***
   * @param $model
   * @return $this
   * set model to export data this model
   */
  public function setModel($model)
  {
    $this->model = $model;
    return $this;
  }


  /**
   * @return mixed
   * get model
   */
  public function getModel()
  {
    return $this->model;

  }

  /****
   * @param $data
   * @return $this
   * set data to export him in pdf
   */
  public function setData($data=[])
  {

    $this->data = $data;
    return $this;
  }

  /***
   * @return mixed
   * get data you want to export him in pdf
   * @author mahmoud
   */

  public function getData()
  {

    return $this->data;

  }

  /***
   * @author mahoud
   */
  public function setLoadType($loadType)
  {
    $this->loadType = $loadType;
    return $this;
  }

  /***
   * @return mixed
   * get type you want to export data
   * @author mahmoud
   */

  public function getLoadType()
  {
    return 'load' . ucwords($this->loadType);


  }


  /***
   * @param $resource
   * @return $this
   * set $resource to data
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
    return $this;
  }

  /****
   * @return mixed
   * retrun data with resource
   */
  public function getResource()
  {
    return $this->resource;

  }

  /***
   * @param $function
   * @return $this
   * set query to model
   */
  public function setQuery($function): PDFRepository
  {
    $this->query = $this->getModel()->where($function);
    return $this;
  }


  /***
   * @return mixed
   * get query
   * @author mahmoud
   */
  public function getQuery()
  {
    return $this->query ? $this->query : new $this->model;

  }


  /****
   * @param $view
   * @return $this
   * set view want to show data
   */

  public function setView($view): PDFRepository
  {
    $this->view = $view;
    return $this;


  }

  public function getView()
  {
    return $this->view;

  }

  public function setFile($file): PDFRepository
  {
    $this->file = $file;
    return $this;


  }

  public function getFile()
  {
    return $this->file;

  }

  public function setHtml($html): PDFRepository
  {
    $this->html = $html;
    return $this;


  }

  public function getHtml()
  {
    return $this->html;

  }

  /***
   * @return mixed
   */

  public function get()
  {
    return $this->getQuery()->get();
  }

  /****
   *
   */
  public function getDataWithResource()
  {
    if ($this->getModel()) {
      $data[(new $this->model)->getTable()] = ($this->resource::collection($this->get()))->toArray(request());
      return $data;
    }

    return (new $this->resource($this->getData()))->toArray(request());
  }


  /***
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Mpdf\MpdfException
   * genre
   */

  public function loadPdf()
  {
    @ini_set('max_execution_time',300);
    @ini_set('memory_limit','512M');

    return \Meneses\LaravelMpdf\Facades\LaravelMpdf::{$this->getLoadType()}($this->{'get' . ucwords($this->loadType)}(), $this->getDataWithResource());

  }

  public function download()
  {

    $name = $this->createUniqueFilename();


    Storage::put("reports/$name", $this->loadPdf()->output());

    return $this->loadPdf()->download($name . '.pdf');
  }

  public function save()
  {

    $name = $this->createUniqueFilename();
    Storage::put("reports/$name", $this->loadPdf()->output());
   return $name;

  }

  public function createUniqueFilename($extension = "pdf")

  {
    return 'file_' . time() . mt_rand() . '.' . $extension;
  }

}
