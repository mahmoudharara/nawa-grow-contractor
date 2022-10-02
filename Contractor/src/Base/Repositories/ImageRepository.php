<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 27/3/2021
 * Time: 2:23 م
 */

 namespace Contractor\Base\Repositories;



use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image as ImageIntervention;
useContractor\Base\Exceptions\UploadErrorException;
useContractor\Base\Interfaces\AttachmentInterface;

class ImageRepository implements AttachmentInterface
{

    protected $rules = 'required|mimes:png,gif,jpeg,jpg,bmp,svg,ico';

    private $saving_path = 'uploads/images';

    public $resizeWidth = 512;
    public $resizeHeight = 512;

    /**
     * @param $image
     * @return mixed
     * @throws UploadErrorException
     */
    public function upload($image)
    {
        $extension = $image->getClientOriginalExtension();

        $filename = $this->createUniqueFilename($extension);
        $response = $this->resizeImage($image, $extension, $filename);

//    $uploadSuccess1 = $this->original($image, $filename);

        $originalName = str_replace('.' . $extension, '', $filename);
        if (!isset($response))
            throw  new UploadErrorException(__('lang.uploading_error_exception'), UPLOADING_ERROR);
//
        return $this->saveToModel([
            'file_name' => $filename,
            'display_name' => $originalName,
            'size' => 0,
            'extension' => $extension
        ]);
    }

    /**
     * Optimize Original Image
     * @param $image
     * @param $filename
     * @return
     */
    public function original($image, $filename)
    {
        return $image->storeAs("public/$this->saving_path", $filename);
    }


    /**
     * @param $attachment
     * @return array
     * @throws UploadErrorException
     */
    public function uploadBase64($attachment)
    {
        $base64_str = substr($attachment, strpos($attachment, ",") + 1);

        $filename = $this->createUniqueFilename('png');
        $response = $this->resizeImage(base64_decode($base64_str), 'png', $filename);
//    $response = Storage::disk('public')->put($this->saving_path . '\\' . $filename, base64_decode($base64_str));
        if (!$response)
            throw  new UploadErrorException(__('lang.uploading_error_exception'), UPLOADING_ERROR);

        return $this->saveToModel([
            'file_name' => $filename,
            'display_name' => $filename,
            'size' => 0,
            'extension' => 'png'
        ]);
    }

    /**
     * @param $extension
     * @return string
     */
    public function createUniqueFilename($extension)
    {
        return 'image_' . time() . mt_rand() . '.' . $extension;
    }


    /**
     * @param $data
     * @return mixed
     */
    public function saveToModel($data)
    {
        return \Modules\Core\Models\Image::create($data);
    }

    /**
     * @return string
     * @author WeSSaM
     */
    public function getFullPath()
    {
        return storage_path($this->saving_path);
    }

    public function setImageResizeWidth($resizeWidth)
    {
        $this->resizeWidth = $resizeWidth;
        return $this;
    }

    public function setImageResizeHeight($resizeHeight)
    {
        $this->resizeHeight = $resizeHeight;
        return $this;
    }

    public function getImageResizeWidth()
    {
        return $this->resizeWidth;
    }

    public function getImageResizeHeight()
    {
        return $this->resizeHeight;
    }

    public function resizeImage($image, $format, $filename)
    {
        if (!isset($image) || !isset($filename))
            throw  new UploadErrorException(__('lang.invalid_image'), UPLOADING_ERROR);

        $img = ImageIntervention::make($image)->resize($this->getImageResizeWidth(), $this->getImageResizeHeight(), function ($constraint) {
            $constraint->aspectRatio();
        })->encode($format);
        return Storage::disk('public')->put($this->saving_path . '\\' . $filename, $img);
    }
}
