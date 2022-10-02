<?php

 namespace Contractor\Base\Http\Resources;
class AttachmentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'display_name' => $this->display_name,
            'extension' => $this->extension,
            'url' => image_url($this->file_name)
        ];
    }
}
