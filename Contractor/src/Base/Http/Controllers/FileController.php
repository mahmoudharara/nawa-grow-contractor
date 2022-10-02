<?php

 namespace Contractor\Base\Http\Controllers;


use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Modules\Core\Exceptions\UploadErrorException;
use Modules\Core\Repositories\FileRepository;
use Modules\Core\Repositories\ImageRepository;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{

    public function show($id)
    {
        $path = storage_path('app/public/files/' . $id);
        $type = File::mimeType($path);

        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => $type,
        ]);

    }

    public function fileUpload(Request $request)
    {
        if (!$request->hasFile("file"))
            return response()->api(false, trans('core::messages.file_not_found'), [], [], UPLOADING_ERROR);

        $file=  (new FileRepository())->upload($request->file);

        return response()->api(true, trans('core::messages.uploaded_successfully'),$file);

    }

}

