<?php

namespace App\Http\Requests\Admin\Regions;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

/**
 * @property int $parent
 */
class CreateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:regions,name,NULL,id,parent_id,' . ($this->parent ?: 'NULL'),
            'slug' => 'required|string|max:255|unique:regions,slug,NULL,id,parent_id,' . ($this->parent ?: 'NULL'),
            'parent' => 'exists:regions,id'
        ];
    }
}
