<?php

namespace App\Http\Requests;

use App\Gamelisting;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameListingsRequest extends FormRequest
{
    public function authorize()
    {
        // return \Gate::allows('user_create');
        return true;
    }

    public function rules()
    {
        return [
            'name'     => [
                'required',
            ],
            'image'    => [
                'required',
                'mimes:jpeg,jpg,png',
                'max:10240'
            ],
            'description' => [
                'required',
            ],
        ];
    }
}
