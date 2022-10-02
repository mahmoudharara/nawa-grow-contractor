<?php


 namespace Contractor\Base\Http\Resources;



class ConstantResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'color' => $this->getValueByKey('color')?$this->getValueByKey('color'):'#EBEBDC',
            'border' => $this->getValueByKey('border')? $this->getValueByKey('border'):'#7D7D78',
            'icon' =>$this->getValueByKey('icon')?constant_url($this->getValueByKey('icon')):constant_url('app-grey.svg') ,
            'key' => $this->getValueByKey('key'),
            'value' => $this->value,
        ];
    }
}
