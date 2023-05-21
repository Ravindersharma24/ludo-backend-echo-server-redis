<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\CommissionLimitManagement;
use App\Http\Controllers\Controller;
use App\MannualTransaction;
use App\TransactionHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MannualWithdrawApiController extends Controller
{
    public function withdraw(Request $request)
    {
        try {
            //code...
            $user = auth('sanctum')->user();
            $rules = array(
                'name' => 'required',
                'amount' => 'required',
                'transfer_type' => 'required',
                'upi_id' => 'required_if:transfer_type,==,1',
                'account_number' => 'required_if:transfer_type,==,2',
                'ifsc_code' => 'required_if:transfer_type,==,2',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
            }
            $min_withdraw_amount = CommissionLimitManagement::select('wallet_withdraw_limit')->orderBy('id', 'DESC')->first();
            if ($request->amount > $user->winning_cash) {
                return response()->json(['success' => false, 'msg' => "Insuficient wallet balance!"], 400);
            }
            if ($request->amount < $min_withdraw_amount->wallet_withdraw_limit) {
                return response()->json(['success' => false, 'msg' => "Minimum withdraw amount should be " . $min_withdraw_amount->wallet_withdraw_limit . " or more"], 400);
            }
            $user = User::find($user->id);

            $mannual_withdraw_id = MannualTransaction::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone_no' => $user->phone_no,
                'amount' => $request->amount,
                'transfer_type' => $request->transfer_type,
                'upi_id' => $request->upi_id ? $request->upi_id : null,
                'account_number' => $request->account_number ? $request->account_number : null,
                'ifsc_code' => $request->ifsc_code ? $request->ifsc_code : null,
            ])->id;
            // deduct balance from user winning cash

            $user->decrement('winning_cash', $request->amount);

            // add in transaction table when user successfully withdraw winning cash;
            $transferId = Carbon::now()->timestamp;
            TransactionHistory::create([
                'user_id' => $user->id,
                'mannual_withdraw_id' => $mannual_withdraw_id,
                'username' => $user->name,
                'transaction_amount' => $request->amount,
                'dr_cr' => 0,
                'order_id' => $transferId . $user->id,
                'transaction_type' => '2',
                'status' => '0',
                'closing_balance' => $user->balance + $user->winning_cash,
                'game_image' => '',
                'opposition_player' => '',
                'battle_id' => '',
                'game_name' => '',
            ]);

            return response()->json(['success' => true, 'msg' => 'You withdrawl transaction request is in process.Amount would be ransferred to your account within 24 hours!', 'withdraw_id' => $mannual_withdraw_id], 200);
        } catch (\Throwable $th) {
            throw $th;
            // print_r($th);
        }
    }
}
