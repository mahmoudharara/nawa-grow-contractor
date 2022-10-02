<?php

namespace NawaGrow\Contractor\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use NawaGrow\Contractor\Base\Http\Resources\AttachmentResource;
use NawaGrow\Contractor\Base\Repositories\ImageRepository;


class AttachmentController extends Controller
{
    /**
     * authentication guard
     * @author WeSSaM
     */
    protected $guard;

    /**
     * attachment resource class
     * @author WeSSaM
     */
    protected $attachmentResource = AttachmentResource::class;

    /**
     * check if controller has middleware
     * then apply middleware on controller's functions
     *
     * AttachmentController constructor.
     * @author WeSSaM
     */
    public function __construct()
    {
        if (isset($this->guard))
            $this->middleware("auth:$this->guard");
    }


    /**
     * upload image
     *
     * @param Request $request
     * @param ImageRepository $imageRepository
     * @return mixed
     * @throws \Modules\Core\Exceptions\UploadErrorException
     * @author WeSSaM
     */
    public function imageUpload(Request $request, ImageRepository $imageRepository)
    {
        if (!$request->hasFile("file"))
            return response()->api(false, trans('core::messages.file_not_found'), [], [], UPLOADING_ERROR);

        $image = $imageRepository->upload($request->file);
        return response()->api(true, trans('core::messages.uploaded_successfully'), (new AttachmentResource($image)));
    }
  public function imageUploadBase64(Request $request, ImageRepository $imageRepository )
  {

//    $this->callValidationRequest();
//    if (!$request->hasFile("file"))
//      return response()->api(false, trans('core::messages.file_not_found'), [], [], UPLOADING_ERROR);


    $image = $imageRepository->uploadBase64($request->file, $request->resize );
    return response()->api(true, trans('core::messages.uploaded_successfully'), (new AttachmentResource($image)));
  }


}
