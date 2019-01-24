<?php

namespace App\Http\Requests\Admin\Regions;

use App\Entity\Region;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Region $region
*/
class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => "required|string|max:255|unique:regions,name,{$this->region->id},id,parent_id,{$this->region->parent_id}",
            'slug' => "required|string|max:255|unique:regions,slug,{$this->region->id},id,parent_id,{$this->region->parent_id}"
        ];
    }
}
