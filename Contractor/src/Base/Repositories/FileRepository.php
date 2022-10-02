<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 27/3/2021
 * Time: 2:23 م
 */

 namespace Contractor\Base\Repositories;


use App\Models\Image;
use Illuminate\Support\Facades\Storage;
useContractor\Base\Exceptions\UploadErrorException;
useContractor\Base\Interfaces\AttachmentInterface;


class FileRepository implements AttachmentInterface
{



    private $saving_path = '/files';

    /**
     * @param $image
     * @return mixed
     * @throws UploadErrorException
     */
    public function upload($file)
    {


        $extension = $file->getClientOriginalExtension();

        $filename = $this->createUniqueFilename($extension);

        $uploadSuccess1 = $this->original($file, $filename);
        $originalName = str_replace('.' . $extension, '', $file->getClientOriginalName());
        if (!$uploadSuccess1)
            throw  new UploadErrorException(__('lang.uploading_error_exception'), UPLOADING_ERROR);

        return collect([
            'file_name' => $filename,
            'display_name' => $originalName,
            'size' => 0,
            'extension' => $extension,
            'url'=>fileUrl($filename)
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
     * @param $extension
     * @return string
     */
    public function createUniqueFilename($extension)
    {
        return 'file_' . time() . mt_rand() . '.' . $extension;
    }


    /**

    /**
     * @return string
     * @author WeSSaM
     */
    public function getFullPath()
    {
        return storage_path($this->saving_path);
    }
}
