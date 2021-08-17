<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|image|mimes:jpeg|max:20480',
        ];
    }

    public function attributes()
    {
        return [
            'file' => 'Image file'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Request does not contain file',
        ];
    }
}
