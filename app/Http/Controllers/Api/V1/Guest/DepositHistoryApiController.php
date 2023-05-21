<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\DepositHistory;
use App\Http\Controllers\Api\V1\Guest\PaytmChecksum;
use App\Http\Controllers\Controller;
use App\TransactionHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepositHistoryApiController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = auth('sanctum')->user();

        // curl request

        $paytmParams = array();
        $orderId = "ORDER_" . Carbon::now()->timestamp;
        $paytmParams["body"] = array(
            "requestType" => "Payment",
            "mid" => env("PAYTM_MERCHANT_ID"),
            "websiteName" => "JJLUDO",
            "orderId" => $orderId,
            "callbackUrl" => "http://localhost:3000",
            "txnAmount" => array(
                "value" => $request->amount,
                "currency" => "INR",
            ),
            "userInfo" => array(
                "custId" => $user->id,
            ),
        );

        /*
         * Generate checksum by parameters we have in body
         * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
         */
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env("PAYTM_MERCHANT_KEY"));

        $paytmParams["head"] = array(
            "signature" => $checksum,
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=" . env("PAYTM_MERCHANT_ID") . "&orderId=" . $orderId;

        /* for Production */
        // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            header('Content-Type: application/json; charset=utf-8');
            // echo json_encode(array("error" => 1));
            // echo "cURL Error #:" . $err;
            // die();
            return response()->json(["error" => 1, "curlError" => $err, "msg" => "Something went wrong while initiating payment!"], 400);
        } else {
            $result = json_decode($response, true);
            // store data into depost_historys table
            $order = new DepositHistory();
            $order->order_id = $orderId;
            $order->user_id = $user->id;
            $order->amount = $request->amount;
            $order->status = $result['body']['resultInfo']['resultStatus'];
            $order->save();

            // echo json_encode($result['order_status']);
            // die;
            header('Content-Type: application/json; charset=utf-8');
            $output = ["order_token" => $result["body"]["txnToken"], "order_id" => $orderId];
            // echo json_encode($output);
            // die();
            return response()->json($output, 200);
        }
    }

    public function confirmOrder(Request $request)
    {
        $user = auth('sanctum')->user();
        $orderId = $request->order_id;
        // echo $orderId;
        // die;
        /* initialize an array */
        $paytmParams = array();

        /* body parameters */
        $paytmParams["body"] = array(

            /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
            "mid" => env("PAYTM_MERCHANT_ID"),

            /* Enter your order id which needs to be check status for */
            "orderId" => $orderId,
        );

        /**
         * Generate checksum by parameters we have in body
         * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
         */
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env("PAYTM_MERCHANT_KEY"));

        /* head parameters */
        $paytmParams["head"] = array(

            /* put generated checksum value here */
            "signature" => $checksum,
        );

        /* prepare JSON string for request */
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        $url = "https://securegw-stage.paytm.in/v3/order/status";

        /* for Production */
        // $url = "https://securegw.paytm.in/v3/order/status";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);

        $err = curl_error($ch);

        curl_close($ch);
        if (!$err) {
            $result = json_decode($response, true);
            // return response()->json($result["order_amount"]);
            // die;
            if ($result["body"]['resultInfo']['resultStatus'] == 'TXN_SUCCESS' && $result["body"]['resultInfo']['resultCode'] == "01") {
                DepositHistory::where('order_id', $orderId)->update(['status' => 'S']);
                // User::where('id',$user->id)->update(['balance'=>$result["order_amount"]]);
                $amount = $result["body"]['txnAmount'];
                $user = User::find($user->id);
                $user->increment('balance', $amount);
                $user->increment('deposit_cash', $amount);

                // add in transaction table when user successfully added wallet balance;
                TransactionHistory::create([
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'transaction_amount' => $amount,
                    'dr_cr' => 1,
                    'order_id' => $orderId,
                    'transaction_type' => '1',
                    'closing_balance' => $user->balance + $user->winning_cash,
                    'game_image' => '',
                    'opposition_player' => '',
                    'battle_id' => '',
                    'game_name' => '',
                    'status' => '1',
                ]);

                return response()->json(['success' => true, 'msg' => "Wallet balance added successfully!", "amount" => $amount], 200);
            } else {
                // echo "Order has not been paid!";
                return response()->json(['success' => false, "msg" => "Something went wrong in payment. Please contact to support."], 400);
            }
        } else {
            return response()->json(['success' => false, "msg" => "Something went wrong in payment. Please contact to support."], 400);
        }
    }
}
