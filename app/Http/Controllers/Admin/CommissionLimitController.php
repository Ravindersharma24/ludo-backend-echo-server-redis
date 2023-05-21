<?php

namespace App\Http\Controllers\Admin;

use App\CommissionLimitManagement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateCommissionLimitRequest;

class CommissionLimitController extends Controller
{
    public function index()
    {
        try{
            $data = CommissionLimitManagement::all();
            return view('admin.commission_limit_management.index', compact('data'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.commission_limit_managements.index')->with('error', 'Something went wrong');
            }
        }

    }

    public function edit(CommissionLimitManagement $commission_limit_management)
    {
        try{
            return view('admin.commission_limit_management.edit', compact('commission_limit_management'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.commission_limit_managements.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function update(UpdateCommissionLimitRequest $request, CommissionLimitManagement $commission_limit_management)
    {
        try{
            $refer_commission_percentage = $request->refer_commission_percentage;
            $wallet_withdraw_limit = $request->wallet_withdraw_limit;
            $refer_reedem_limit = $request->refer_reedem_limit;
            $max_refer_commission = $request->max_refer_commission;
            $pending_game_penalty_amt = $request->pending_game_penalty_amt;
            $wrong_result_penalty_amt = $request->wrong_result_penalty_amt;
            // $commission_limit_management->update([
            //     'refer_commission_percentage' => $refer_commission_percentage,
            //     'wallet_withdraw_limit' => $wallet_withdraw_limit,
            //     'refer_reedem_limit' => $refer_reedem_limit,
            //     'max_refer_commission' => $max_refer_commission,
            //     'pending_game_penalty_amt' => $pending_game_penalty_amt,
            //     'wrong_result_penalty_amt' => $wrong_result_penalty_amt,
            //     'updated_at' => Carbon::now()->timestamp,
            // ]);
            $commission_limit_management->update($request->all());
            return redirect()->route('admin.commission_limit_managements.index')->with('success', 'Data Updated Successfully !');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.commission_limit_managements.index')->with('error', 'Something went wrong');
            }
        }
    }
}
