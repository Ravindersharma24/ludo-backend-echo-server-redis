<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MannualTransaction;
use App\TransactionHistory;
use App\User;
use Illuminate\Http\Request;

class MannualWithdrawlController extends Controller
{
    public function index()
    {
        try {
            return view('admin.mannual_withdrawl.index');
        } catch (\Throwable $th) {
            if ($th) {
                return redirect()->route('admin.mannual_withdrawls.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function show(Request $request)
    {
        try {
            $withdraw_request = MannualTransaction::where('id', $request->user)->first();
            return view('admin.mannual_withdrawl.edit', compact('withdraw_request'));
        } catch (\Throwable $th) {
            if ($th) {
                return redirect()->route('admin.mannual_withdrawls.index')->with('error', 'Something went wrong');
            }
        }
    }
    public function update(Request $request)
    {
        try {
            $mannual_transaction_id = $request->id;
            $user_id = $request->user_id;
            $withdraw_status = $request->withdraw_status;
            $withdrawl_amount = $request->amount;

            $mannual_transaction_detail = MannualTransaction::where('id', $mannual_transaction_id)->first();
            $created_transaction = TransactionHistory::where('mannual_withdraw_id', $mannual_transaction_id)->first();
            if ($withdraw_status === '2') {
                // rejected
                $user = User::where('id', $user_id)->first();
                $user->increment('winning_cash', $withdrawl_amount);
                $created_transaction->update([
                    'status' => $withdraw_status,
                ]);
                $message = 'Withdrawl Request Rejected';
                $this->updateStatus($withdraw_status, $mannual_transaction_detail, $message);
                return redirect()->route('admin.mannual_withdrawls.index')->with('success', $message);
            }
            if ($withdraw_status === '1') {
                // success
                $created_transaction->update([
                    'status' => $withdraw_status,
                ]);
                $message = 'Withdrawl Request Successfully Completed!';
                $this->updateStatus($withdraw_status, $mannual_transaction_detail);
                return redirect()->route('admin.mannual_withdrawls.index')->with('success', $message);
            }
        } catch (\Throwable $th) {
            if ($th) {
                return redirect()->route('admin.mannual_withdrawls.index')->with('error', 'Something went wrong');
            }
        }
        // $mannual_transaction_detail->update([
        //     'status' => $withdraw_status,
        // ]);
        // return redirect()->route('admin.mannual_withdrawls.index')->with('success', 'Withdrawl Request Updated Successfully !');
    }

    public function updateStatus($withdraw_status, $mannual_transaction_detail)
    {
        try {
            $mannual_transaction_detail->update([
                'status' => $withdraw_status,
            ]);
        } catch (\Throwable $th) {
            if ($th) {
                return redirect()->route('admin.mannual_withdrawls.index')->with('error', 'Something went wrong');
            }
        }
    }
}
