<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKycUploadRequest extends FormRequest
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
            'document_number'     => [
                'required',
            ],
            'first_name'     => [
                'required',
            ],
            'last_name'     => [
                'required',
            ],
            'dob'     => [
                'required',
            ],
            'state'     => [
                'required',
            ],
            // 'front_photo'     => [
            //     'required',
            //     'mimes:jpeg,jpg,png',
            //     'max:10240'
            // ],
            // 'back_photo'     => [
            //     'required',
            //     'mimes:jpeg,jpg,png',
            //     'max:10240'
            // ],
            // 'kyc_status'     => [
            //     'required',
            // ],
        ];
    }
}
