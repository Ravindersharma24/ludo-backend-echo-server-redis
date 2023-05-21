<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\CommissionLimitManagement;
use App\Http\Controllers\Controller;
use App\Referral;
use App\TransactionHistory;
use App\User;
use Illuminate\Http\Request;

class TransactionApiController extends Controller
{
    public function walletTransactionHistory()
    {
        $user = auth('sanctum')->user();
        $wallet_transaction_history = TransactionHistory::where('user_id', $user->id)->where('order_id', '!=', '')->where('battle_id', '=', '')->where('transaction_type', '!=', '5')->select(['transaction_amount', 'dr_cr', 'order_id', 'transaction_type', 'status', 'closing_balance', 'created_at as date'])->orderBy('id', 'DESC')->paginate(10);
        // $wallet_transaction_history = TransactionHistory::where('user_id',$user->id)->where('transaction_type','=','1')->orWhere('transaction_type','=','2')->select(['transaction_amount','dr_cr','order_id','transaction_type','closing_balance','created_at as date'])->orderBy('id','DESC')->paginate(10);

        return response()->json(['wallet_transaction_history' => $wallet_transaction_history]);
    }

    public function battleTransactionHistory()
    {
        $user = auth('sanctum')->user();
        $battle_transaction_history = TransactionHistory::where('user_id', $user->id)->where('order_id', '=', '')->where('battle_id', '!=', '')->select(['transaction_amount', 'dr_cr', 'opposition_player', 'order_id', 'battle_id', 'transaction_type', 'closing_balance', 'game_image', 'game_name', 'created_at as date'])->orderBy('created_at', 'DESC')->paginate(10);
        // $battle_transaction_history = TransactionHistory::where('user_id',$user->id)->where('transaction_type','=','3')->orWhere('transaction_type','=','4')->select(['transaction_amount','dr_cr','opposition_player','order_id','battle_id','transaction_type','closing_balance','game_image','game_name','created_at as date'])->orderBy('id','DESC')->paginate(10);
        return response()->json(['battle_transaction_history' => $battle_transaction_history]);
    }
    public function referTransactionHistory()
    {
        $user = auth('sanctum')->user();
        $refer_transaction_history = Referral::where('user_id', $user->id)->select(['referral_amount', 'dr_cr', 'transaction_type', 'referral_closing_balance', 'order_id', 'created_at as date'])->orderBy('id', 'DESC')->paginate(10);
        return response()->json(['refer_transaction_history' => $refer_transaction_history]);
    }

    public function withdraw_referral_amount(Request $request)
    {
        $user = auth('sanctum')->user();
        $min_withdraw_refer_amount = CommissionLimitManagement::select('refer_reedem_limit')->orderBy('id', 'DESC')->first();
        if ($request->refer_amount > $user->refer_cash) {
            return response()->json(['success' => false, 'msg' => "Insuficient wallet balance!"], 400);
        }
        if ($request->refer_amount < $min_withdraw_refer_amount->refer_reedem_limit) {
            return response()->json(['success' => false, 'msg' => "Minimum refer withdraw amount should be " . $min_withdraw_refer_amount->refer_reedem_limit . " or more"], 400);
        }
        $user = User::find($user->id);
        $user->decrement('refer_cash', $request->refer_amount);
        $user->increment('balance', $request->refer_amount);
        $user->increment('deposit_cash', $request->refer_amount);

        // add in transaction table when user successfully withdraw winning cash;
        $bytes = random_bytes(4);
        $commission_order_id = bin2hex($bytes);

        // add referral amount data into the referral table.
        $refer = Referral::create([
            'user_id' => $user->id,
            'username' => $user->name,
            'referral_amount' => $request->refer_amount,
            'dr_cr' => 0,
            'transaction_type' => 2,
            'referral_closing_balance' => $user->refer_cash,
            'order_id' => $commission_order_id . $user->id,
        ]);

        // get last insert referral record
        $ref_order_id = $refer->select('order_id')->orderBy('id', 'DESC')->first();
        TransactionHistory::create([
            'user_id' => $user->id,
            'username' => $user->name,
            'transaction_amount' => $request->refer_amount,
            'dr_cr' => 0,
            'order_id' => $ref_order_id->order_id,
            'transaction_type' => '5',
            'closing_balance' => $user->balance + $user->winning_cash,
            'game_image' => '',
            'opposition_player' => '',
            'battle_id' => '',
            'game_name' => '',
        ]);
        // creating transaction for add referral balance to wallet ballance
        TransactionHistory::create([
            'user_id' => $user->id,
            'username' => $user->name,
            'transaction_amount' => $request->refer_amount,
            'dr_cr' => 1,
            'order_id' => $ref_order_id->order_id,
            'transaction_type' => '1',
            'closing_balance' => $user->balance + $user->winning_cash,
            'game_image' => '',
            'opposition_player' => '',
            'battle_id' => '',
            'game_name' => '',
        ]);

        return response()->json(['success' => true, 'msg' => "Refer amount successfully transffered to your deposit cash !"], 200);
    }
}
