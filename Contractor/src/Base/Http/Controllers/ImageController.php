<?php

 namespace Contractor\Base\Http\Controllers;


use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

use Illuminate\Support\Facades\Response;
useContractor\Base\Exceptions\UploadErrorException;
useContractor\Base\Repositories\ImageRepository;

class ImageController extends Controller
{
  public $imageRepository;

  /**
   * ImageController constructor.
   * @param ImageRepository $imageRepository
   * @author WeSSaM
   */
  public function __construct(ImageRepository $imageRepository)
  {
    $this->imageRepository = $imageRepository;
  }

  /**
   * @param Request $request
   * @return mixed
   * @throws UploadErrorException
   */
  public function upload(Request $request)
  {

    if (!$request->hasFile('file'))
      throw  new UploadErrorException(trans('lang.file_not_found'));

    $file = $this->imageRepository->upload($request->all()['file']);
    return response()->api(SUCCESS_RESPONSE, trans('lang.uploaded_successfully'), $file);
  }

  public function getPublicImage($size, $id)
  {
//        $path = storage_path('app/image/'.$id);

    $path = storage_path('app/uploads/images/' . $id);
    if (!File::exists($path))
      $path = storage_path('app/images/default_image.png');

    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $sizes = explode("x", $size);

    if (is_numeric($sizes[0]) && is_numeric($sizes[1])) {

      $manager = new ImageManager();
      $image = $manager->make($file)->fit($sizes[0], $sizes[1], function ($constraint) {
        $constraint->upsize();
      });

      $response = Response::make($image->encode($image->mime), 200);

      $response->header("CF-Cache-Status", 'HIF');
      $response->header("Cache-Control", 'max-age=604800, public');
//            $response->header("Content-Encoding", 'gzip');
      $response->header("Content-Type", $type);
//            $response->header("Vary", 'Accept-Encoding');
//            $img = Image::cache(function($image)use ($id){
//                $src=storage_path('app/uploads/images/'.$id);
//
//                $image->make($src);
//            },10,true);
//             $response->make($img,200,array('Content-Type'=>'image/jpeg'));

      return $response;

    } else {
      abort(404);
    }
  }

  public function getItemImage($size, $id)
  {
//        $path = storage_path('app/image/'.$id);

    $path = storage_path('app/uploads/images/' . $id);

    if (!File::exists($path))
      $path = storage_path('app/images/default_image.png');

    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $sizes = explode("x", $size);

    if (is_numeric($sizes[0]) && is_numeric($sizes[1])) {

      $manager = new ImageManager();
      $image = $manager->make($file)->fit($sizes[0], $sizes[1], function ($constraint) {
        $constraint->upsize();
      });

      $response = Response::make($image->encode($image->mime), 200);

      $response->header("CF-Cache-Status", 'HIF');
      $response->header("Cache-Control", 'max-age=604800, public');
//            $response->header("Content-Encoding", 'gzip');
      $response->header("Content-Type", $type);
//            $response->header("Vary", 'Accept-Encoding');
//            $img = Image::cache(function($image)use ($id){
//                $src=storage_path('app/uploads/images/'.$id);
//
//                $image->make($src);
//            },10,true);
//             $response->make($img,200,array('Content-Type'=>'image/jpeg'));

      return $response;

    } else {
      abort(404);
    }
  }

  public function getImageResize($size, $id)
  {
    $path = storage_path('app/uploads/images/' . $id);

    if (!File::exists($path))
      $path = storage_path('app/image/default_image.jpg');

    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    if (is_numeric($size)) {

      $manager = new ImageManager();
      $image = $manager->make($file);
      $height = $image->height();
      $width = $image->width();
      if ($width > $height) {
        $new_height = (($height * $size) / $width);
        $image = $image->resize($size, $new_height, function ($constraint) {
          $constraint->upsize();
        });
      } else {
        $new_width = (($width * $size) / $height);
        $image = $image->resize($new_width, $size, function ($constraint) {
          $constraint->upsize();
        });
      }

      $response = Response::make($image->encode($image->mime), 200);

      $response->header("CF-Cache-Status", 'HIF');
      $response->header("Cache-Control", 'max-age=604800, public');
      $response->header("Content-Type", $type);

      return $response;

    } else {
      abort(404);
    }
  }

  public function getImageResize_WaterMark($size, $id)
  {
    $path = storage_path('app/uploads/images/' . $id);

    if (!File::exists($path))
      $path = storage_path('app/image/default_image.jpg');

    if (!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    if (is_numeric($size)) {

      $manager = new ImageManager();
      $image = $manager->make($file);
      $height = $image->height();
      $width = $image->width();
      if ($width > $height) {
        $new_height = (($height * $size) / $width);
        $image = $image->resize($size, $new_height, function ($constraint) {
          $constraint->upsize();
        });
        $wmpath = storage_path('app/uploads/watermark.png');
        if ($image->width() < 200) {
          $wmsize = $image->width() / 2;
        } else {
          $wmsize = $image->width() / 4;
        }

        $watermark = Image::make($wmpath)->resize($wmsize, null, function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
        });
        $image->insert($watermark, 'bottom-right', 10, 10);
      } else {
        $new_width = (($width * $size) / $height);
        $image = $image->resize($new_width, $size, function ($constraint) {
          $constraint->upsize();
        });
        $wmpath = storage_path('app/uploads/watermark.png');
        if ($image->width() < 200) {
          $wmsize = $image->width() / 2;
        } else {
          $wmsize = $image->width() / 4;
        }

        $watermark = Image::make($wmpath)->resize($wmsize, null, function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
        });
        $image->insert($watermark, 'bottom-right', 10, 10);
      }

      $response = Response::make($image->encode($image->mime), 200);

      $response->header("CF-Cache-Status", 'HIF');
      $response->header("Cache-Control", 'max-age=604800, public');
      $response->header("Content-Type", $type);

      return $response;

    } else {
      abort(404);
    }
  }

  public function getDefaultImage($id)
  {
//        dd($id);

//        $path = storage_path('app/image/'.$id);
    $path = storage_path('app/public/uploads/images/' . $id);


    if (!File::exists($path)) abort(404);
//        dd($path);

    $file = File::get($path);
    $type = File::mimeType($path);

    $manager = new ImageManager();
    $image = $manager->make($file);
    $response = Response::make($image->encode($image->mime), 200);
    $response->header("CF-Cache-Status", 'HIF');
    $response->header("Cache-Control", 'max-age=604800, public');
//            $response->header("Content-Encoding", 'gzip');
    $response->header("Content-Type", $type);
//            $response->header("Vary", 'Accept-Encoding');

    return $response;

  }


}

