<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommissionLimitRequest extends FormRequest
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
            'refer_commission_percentage' => 'required',
            'wallet_withdraw_limit' => 'required',
            'refer_reedem_limit' => 'required',
            'max_refer_commission' => 'required',
            'pending_game_penalty_amt' => 'required',
            'wrong_result_penalty_amt' => 'required',
        ];
    }
}
