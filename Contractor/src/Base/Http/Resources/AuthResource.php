<?php


 namespace Contractor\Base\Http\Resources;

class AuthResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function serializeForEdit($request)
    {
//        dd($this,$this->sanitizePermissions());
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->name,
            'email' => $this->email,
            'role_name' => $this->role_name,
            'current_country' => $this->current_country,
            'current_country_id' => $this->current_country,
//            'current_country_telly' => 'QA',

//            {"id":1,"name":"Admin","email":"admin@admin.com","email_verified_at":null,"locations":1,"langs":"ar,tr","role_id":4
//                ,"current_country":11,"countries":"[\"1\",\"2\"]","created_at":null,"updated_at":"2020-12-29 18:14:43"}
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function serializeForFind($request)
    {
        return $this->toArray($request);
    }

}
