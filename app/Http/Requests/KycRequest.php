<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KycRequest extends FormRequest
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
            'document_id'     => [
                'required',
            ],
            'document'    => [
                'required',
                'mimes:jpeg,jpg,png',
                'max:10240'
            ],
        ];
    }

    public function messages()
    {
        return [
            'document_id.required' => 'Document id is invalid',
            'document.required' => 'Please Upload Document',
            'document.max' => 'Document size should not be greater than 1 MB',
        ];
    }
}
