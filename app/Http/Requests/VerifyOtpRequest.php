<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
            'phone_no'    => [
                'required',
                'digits:10',
                'numeric'
            ],
            'otp_no'    => [
                'required',
                'min:6',
                'numeric'
            ],
        ];
    }
}
