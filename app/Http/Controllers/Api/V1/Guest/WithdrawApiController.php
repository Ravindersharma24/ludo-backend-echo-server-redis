<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\CommissionLimitManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\TransactionHistory;
use App\WithdrawalRequests;
use Carbon\Carbon;
use Exception;

class WithdrawApiController extends Controller
{
    private $urls = array(
        'auth' => '/payout/v1/authorize',
        'getBene' => '/payout/v1/getBeneficiary/',
        'addBene' => '/payout/v1/addBeneficiary',
        'requestTransfer' => '/payout/v1/requestTransfer',
        'getTransferStatus' => '/payout/v1/getTransferStatus?transferId='
    );
    private function create_header($token)
    {
        $headers = array(
            'X-Client-Id: ' . env('CASHFREE_API_PAYOUT_KEY'),
            'X-Client-Secret: ' . env('CASHFREE_API_PAYOUT_SECRET'),
            'Content-Type: application/json',
        );
        if (!is_null($token)) {
            array_push($headers, 'Authorization: Bearer ' . $token);
        }
        return $headers;
    }
    private function get_helper($finalUrl, $token)
    {
        $headers = $this->create_header($token);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $finalUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);

        $r = curl_exec($ch);

        if (curl_errno($ch)) {
            print('error in posting');
            print(curl_error($ch));
            die();
        }
        curl_close($ch);

        $rObj = json_decode($r, true);
        if ($rObj['status'] != 'SUCCESS' || $rObj['subCode'] != '200') throw new Exception('incorrect response: ' . $rObj['message']);
        return $rObj;
    }
    private function post_helper($action, $data, $token)
    {
        $finalUrl = env("CASHFREE_API_PAYOUT_URL") . $this->urls[$action];
        $headers = $this->create_header($token);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $finalUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        if (!is_null($data)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $r = curl_exec($ch);

        if (curl_errno($ch)) {
            print('error in posting');
            print(curl_error($ch));
            die();
        }
        curl_close($ch);
        $rObj = json_decode($r, true);
        if (($rObj['status'] == 'SUCCESS' && $rObj['subCode'] == '200') || ($rObj['status'] == 'PENDING' && ($rObj['subCode'] == '201' || $rObj['subCode'] == '202'))) return $rObj;
        throw new Exception('incorrect response: ' . $rObj['message']);
    }

    #get auth token
    private function getToken()
    {
        try {
            $response = $this->post_helper('auth', null, null);
            return $response['data']['token'];
        } catch (Exception $ex) {
            error_log('error in getting token');
            error_log($ex->getMessage());
        }
    }

    #get beneficiary details
    private function getBeneficiary($token, $beneId)
    {
        try {
            $finalUrl = env("CASHFREE_API_PAYOUT_URL") . $this->urls['getBene'] . $beneId;
            $response = $this->get_helper($finalUrl, $token);
            return true;
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            if (strpos($msg, 'Beneficiary does not exist')) return false;
            error_log('error in getting beneficiary details');
            error_log($msg);
        }
    }

    #add beneficiary
    private function addBeneficiary($token, $beneficiary)
    {
        try {
            $this->post_helper('addBene', $beneficiary, $token);
            error_log('beneficiary created');
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            return (object) ['status' => 'ERROR', 'subCode' => 401, 'msg' => $msg];
            error_log('error in creating beneficiary');
            error_log($msg);
        }
    }

    #request transfer
    private function requestTransfer($token, $transfer)
    {
        try {
            $res = $this->post_helper('requestTransfer', $transfer, $token);
            return (object) ['status' => $res['status'], 'subCode' => $res['subCode'], 'msg' => $res['message'], 'data' => $res['data']];
            error_log('transfer requested successfully');
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            return (object) ['status' => 'ERROR', 'subCode' => 401, 'msg' => $msg];
            error_log('error in requesting transfer');
            error_log($msg);
        }
    }

    #get transfer status
    private function getTransferStatus($token)
    {
        try {
            global $transfer;
            $transferId = $transfer['transferId'];
            $finalUrl = env("CASHFREE_API_PAYOUT_URL") . $this->urls['getTransferStatus'] . $transferId;
            $response = $this->get_helper($finalUrl, $token);
            error_log(json_encode($response));
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            error_log('error in getting transfer status');
            error_log($msg);
        }
    }

    public function withdraw(Request $request)
    {
        $user = auth('sanctum')->user();
        $min_withdraw_amount = CommissionLimitManagement::select('wallet_withdraw_limit')->orderBy('id', 'DESC')->first();
        if ($request->amount > $user->winning_cash) {
            return response()->json(['success' => false, 'msg' => "Insuficient wallet balance!"], 400);
        }
        if ($request->amount < $min_withdraw_amount->wallet_withdraw_limit) {
            return response()->json(['success' => false, 'msg' => "Minimum withdraw amount should be ".$min_withdraw_amount->wallet_withdraw_limit." or more"], 400);
        }
        $user = User::find($user->id);
        // $user->decrement('balance', $request->amount);
        // $user->decrement('deposit_cash', $request->amount);
        // $user->decrement('winning_cash', $request->amount);
        $withdrawal_req = WithdrawalRequests::create(['user_id' => $user->id, 'amount' => $request->amount]);

        // start withdrawal transaction
        $transferId = Carbon::now()->timestamp;
        $beneId = $user->phone_no . "_" . $user->id;

        // add in transaction table when user successfully withdraw winning cash;
        // TransactionHistory::create([
        //     'user_id' => $user->id,
        //     'username' => $user->name,
        //     'transaction_amount' => $request->amount,
        //     'dr_cr' => 0,
        //     'order_id' => $transferId,
        //     'transaction_type' => '2',
        //     'closing_balance' => $user->balance,
        //     'game_image' => '',
        //     'opposition_player' => '',
        //     'battle_id' => '',
        //     'game_name' => '',
        // ]);


        $transfer_details = array(
            'beneId' => $beneId,
            'amount' => $request->amount,
            'transferId' => $transferId,
        );
        $token = $this->getToken();
        $ben = $this->getBeneficiary($token, $beneId);
        if (!$ben) {
            $beneficiary = [
                'beneId' => $beneId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $user->phone_no,
                'bankAccount' => $request->account_number,
                'ifsc' => $request->ifsc_code,
                'address1' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
            ];
            $add_ben = $this->addBeneficiary($token, $beneficiary);
            if ($add_ben && $add_ben->status !== "SUCCESS" && $add_ben->subCode !== 200) {
                return response()->json(['success' => false, 'msg' => $add_ben->msg], 401);
            }
        }
        $transfer_req = $this->requestTransfer($token, $transfer_details);
        // end withdrawal transaction
        // return response()->json(['success' => true, 'msg' => $transfer_req], 200);

        $withdrawal_req->description = $transfer_req->msg;
        $withdrawal_req->transferId = $transferId;
        $withdrawal_req->beneficiary_id = $beneId;
        $withdrawal_req->save();

        if (($transfer_req->status === "SUCCESS") && ($transfer_req->subCode == 200)) {

            $withdrawal_req->referenceId = $transfer_req->data['referenceId'];
            $withdrawal_req->save();
                        // decrement withdraw amount from user wallet
                        // $user->decrement('balance', $request->amount);
                        // $user->decrement('deposit_cash', $request->amount);
                        $user->decrement('winning_cash', $request->amount);

                        // add in transaction table when user successfully withdraw winning cash;
                        TransactionHistory::create([
                            'user_id' => $user->id,
                            'username' => $user->name,
                            'transaction_amount' => $request->amount,
                            'dr_cr' => 0,
                            'order_id' => $transferId,
                            'transaction_type' => '2',
                            'closing_balance' => $user->balance + $user->winning_cash,
                            'game_image' => '',
                            'opposition_player' => '',
                            'battle_id' => '',
                            'game_name' => '',
                        ]);
            return response()->json(['success' => true, 'msg' => $transfer_req->msg], 200);
        } else if (($transfer_req->status === "PENDING") && ($transfer_req->subCode == 201 || $transfer_req->subCode == 202)) {
            $withdrawal_req->referenceId = $transfer_req->data['referenceId'];
            $withdrawal_req->save();
            return response()->json(['success' => 'pending', 'msg' => $transfer_req->msg], 201);
        } else {
            return response()->json(['success' => false, 'msg' => $transfer_req->msg], 401);
        }
    }
}
