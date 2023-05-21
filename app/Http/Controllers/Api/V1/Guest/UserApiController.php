<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\CommissionLimitManagement;
use App\Http\Controllers\Api\V1\Guest\BattleApiController;
use App\Http\Controllers\Controller;
use App\KycUpload;
use App\ActivateMannaulSetting;
use App\ReferCommission;
use App\RoomHistory;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class UserApiController extends Controller
{

    public function sendOtp(Request $request)
    {
        // $otp_no = rand(100000,999999);
        // $refer_code = $request->input('refer_code');
        $refer_code = $request->refer_code;
        $rules = array(
            'phone_no' => 'required|unique:otp_logins|digits:10|numeric',
        );
        $messages = array(
            'phone_no.required' => 'phone number field is required',
            'phone_no.digits' => 'phone number must be 10 digit number',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
        }

        $is_active = User::where('phone_no', $request->phone_no)->first();
        if ($is_active && !$is_active->active) {
            return response()->json(["msg" => "Your account is currently deactivated by admin . Please contact to support"], 401);
        }
        $otp_no = 123456;
        $newUser = User::where('phone_no', $request->phone_no)->first();
        if (!empty($newUser)) {
            $newUser->update([
                "otp" => $otp_no,
            ]);
        } else {
            $number = rand(100, 999);
            $character = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $two_character = substr(str_shuffle($character), 0, 2) . $number;
            $referral = str_shuffle($two_character);
            $user = User::create(
                ["phone_no" => $request->phone_no, "otp" => $otp_no, "affiliate_id" => $referral]
            );
            // update refer_code if any exists when a new user join with link
            if (!empty($refer_code)) {
                $check_affiliate_id = User::where('affiliate_id', $refer_code)->where('id', '!=', $user->id)->first();
                if (!$check_affiliate_id) {
                    return response()->json(['msg' => "Otp sent! But referral code is not valid", "referCode" => $request->referCode, "Success" => true], 200);
                    die;
                } else {
                    User::where('id', $user->id)->update(['referred_by' => $refer_code]);
                    return response()->json(["msg'" => "Otp sent by matching referral code", "referCode" => $request->referCode, "Success" => true], 200);
                }
            }
        }
        // $user->roles()->attach(2);
        // send otp to mobile number
        return response()->json(["msg" => "Otp sent successfully", "otp" => $otp_no]);
    }

    public function verifyOtp(Request $request)
    {
        $rules = array(
            'phone_no' => 'required|digits:10|numeric',
            'otp_no' => 'required|min:6|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
        }
        $phone_no = $request->phone_no;
        $otp_no = $request->otp_no;
        $verify_user = User::where('phone_no', $phone_no)->first();
        // genarate random name for new user
        $bytes = random_bytes(3);
        $data = bin2hex($bytes);
        $random_name = "jj" . $data . substr($request->phone_no, -5);
        // $prev_user_name = isset($verify_user->name) ? $verify_user->name :'';
        if (!$verify_user->name) {
            User::where('phone_no', $phone_no)->first()->update(['name' => $random_name]);
        }
        if ($verify_user->otp != $otp_no) {
            return response()->json(["msg" => "Invalid OTP"], 401);
        }
        if ($verify_user) {
            User::where('phone_no', $phone_no)->first()->update(['otp' => '']);
            $token = $verify_user->createToken($verify_user->phone_no . '_Token')->plainTextToken;
            return response()->json(["msg" => "Otp verified Successfully.", "token" => $token]);
        } else {
            return response()->json(["msg" => "Invalid OTP"], 401);
        }
    }
    // public function checkAuth()
    // {
    //     $user = auth('sanctum')->user();
    //     if ($user) {
    //         $getUserInfo = KycUpload::where('user_id', $user->id)->first();
    //         // find total battle played by user
    //         $totalBattle = RoomHistory::where('player_id', $user->id)->count();
    //         $total_Refferal = User::where('referred_by', '=', $user->affiliate_id)->get();
    //         $referralCount = $total_Refferal->count();
    //         $commission_data = CommissionLimitManagement::orderBy('id', 'DESC')->first();
    //         $mannualPayment = MannualPayment::where('id', 1)->first();
    //         $userDetails = (object) [
    //             'phoneNumber' => $user->phone_no,
    //             'userId' => $user->id,
    //             'name' => $user->name,
    //             'profileImage' => $user->user_image,
    //             'walletBalance' => $user->balance,
    //             'upiId' => $user->upi_id,
    //             'paytm' => $mannualPayment->status,
    //             'depositeCash' => $user->deposit_cash,
    //             'winningCash' => $user->winning_cash,
    //             'refer_cash' => $user->refer_cash,
    //             'kyc' => $getUserInfo ? $getUserInfo->kyc_status : false,
    //             'referCode' => $user->referred_by,
    //             'affiliateId' => $user->affiliate_id,
    //             'battlePlayed' => $totalBattle,
    //             'totalRefers' => $referralCount,
    //             'refer_commission_percentage' => $commission_data->refer_commission_percentage,
    //             'wallet_withdraw_limit' => $commission_data->wallet_withdraw_limit,
    //             'refer_reedem_limit' => $commission_data->refer_reedem_limit,
    //             'max_refer_commission' => $commission_data->max_refer_commission,
    //             'pending_game_penalty_amt' => $commission_data->pending_game_penalty_amt,
    //             'wrong_result_penalty_amt' => $commission_data->wrong_result_penalty_amt,
    //             'created_battles' => $user->created_battles,
    //         ];
    //         return response()->json(["msg" => "Logged In", "details" => $userDetails], 200);
    //     } else {
    //         return response()->json(["msg" => "Invalid User"], 401);
    //     }
    // }
    // public function logout()
    // {
    //     auth('sanctum')->user()->tokens()->delete();
    //     return response()->json(["msg" => "Logged Out"], 200);
    // }

    // public function update_referral(Request $request)
    // {
    //     $user = auth('sanctum')->user();
    //     $check_affiliate_id = User::where('affiliate_id', $request->referCode)->where('id', '!=', $user->id)->first();

    //     if (!$check_affiliate_id) {
    //         return response()->json(['msg' => "Invalid Referral Code", "referCode" => $request->referCode, "Success" => false], 400);
    //         die;
    //     }
    //     User::where('id', $user->id)->update(['referred_by' => $request->referCode]);
    //     return response()->json(["msg" => "Referral Updated Successfully", "referCode" => $request->referCode, "success" => true], 200);
    // }

    // public function totalRefferal()
    // {
    //     $user = auth('sanctum')->user();
    //     $total_referral = User::where('referred_by', '=', $user->affiliate_id)->get();
    //     $referralCount = $total_referral->count();
    //     $commission_percentage = ReferCommission::select('commission_percentage')->orderBy('id', 'DESC')->first();
    //     return response()->json(['total_referral' => $referralCount, 'commission_percentage' => $commission_percentage->commission_percentage]);
    // }

    // public function update_userProfile(Request $request)
    // {
    //     $user = auth('sanctum')->user();
    //     if ($request->userName) {
    //         $check_username = User::where('name', $request->userName)->first();
    //         if ($check_username) {
    //             return response()->json(['msg' => "Username already exists.", "Success" => false], 400);
    //         }
    //         User::where('id', $user->id)->update(['name' => $request->userName]);
    //         return response()->json(["msg" => "Username Updated Successfully", "userName" => $request->userName, "Success" => true], 200);
    //     } else if ($request->upiId) {
    //         $check_upiId = User::where('upi_id', $request->upiId)->first();
    //         if ($check_upiId) {
    //             return response()->json(['msg' => "Upi Id is already exists.", "Success" => false], 400);
    //         }
    //         User::where('id', $user->id)->update(['upi_id' => $request->upiId]);
    //         return response()->json(["msg" => "Upi id Updated Successfully", "upiId" => $request->upiId, "Success" => true], 200);
    //     } else if ($request->file('user_image')) {
    //         $rules = array(
    //             'user_image' => 'required|mimes:jpeg,jpg,png|max:5120',
    //         );
    //         $validator = Validator::make($request->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
    //         }
    //         $user_image = $this->upload($request->file('user_image'), $user->id);
    //     } else if ($request->user_image) {
    //         $user_image = $request->user_image;
    //     } else {
    //         return response()->json(["msg" => "Username is required", "Success" => false], 401);
    //     }
    //     User::where('id', $user->id)->update(['user_image' => $user_image]);
    //     return response()->json(["msg" => "User Image Updated Successfully", "Success" => true, 'profileImage' => $user_image], 200);
    // }

    // public function upload($image, $userid)
    // {

    //     try {
    //         $destination_path = storage_path('app/public/images/user-profiles/');
    //         $image_name = time() . '.' . $image->getClientOriginalName();
    //         $img = Image::make($image->getRealPath());
    //         $img->resize(1920, 1080);
    //         if (!file_exists($destination_path)) {
    //             mkdir($destination_path, 0777);
    //         }
    //         $img->save($destination_path . $image_name);
    //         return $image_name;
    //     } catch (Exception $err) {
    //         print_r($err);
    //     }
    // }

    // public function getData()
    // {
    //     $data = BattleApiController::index(1);

    //     return response()->json($data->original);
    // }
    // public function sendOtp(Request $request)
    // {
    //     $refer_code = $request->refer_code;
    //     $otp_no = null;
    //     $otpResponse = null;
    //     $rules = array(
    //         'phone_no' => 'required|unique:otp_logins|digits:10|numeric',
    //     );
    //     $messages = array(
    //         'phone_no.required' => 'phone number field is required',
    //         'phone_no.digits' => 'phone number must be 10 digit number',
    //     );
    //     $validator = Validator::make($request->all(), $rules, $messages);
    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
    //     }
    //     $is_active = User::where('phone_no', $request->phone_no)->first();
    //     if ($is_active && !$is_active->active) {
    //         return response()->json(["msg" => "Your account is currently deactivated by admin . Please contact to support"], 401);
    //     }

    //     $existingUser = User::where('phone_no', $request->phone_no)->first();
    //     if (!$existingUser) {
    //         $number = rand(100, 999);
    //         $character = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //         $two_character = substr(str_shuffle($character), 0, 2) . $number;
    //         $referral = str_shuffle($two_character);
    //         $user = User::create(
    //             ["phone_no" => $request->phone_no, "affiliate_id" => $referral]
    //         );
    //         $otpResponse = $this->generateOtp($user->phone_no);
    //         $user->update([
    //             "otp" => $otpResponse['OTP'],
    //         ]);
    //         // update refer_code if any exists when a new user join with link
    //         if (!empty($refer_code)) {
    //             $check_affiliate_id = User::where('affiliate_id', $refer_code)->where('id', '!=', $user->id)->first();
    //             if (!$check_affiliate_id) {
    //                 // $otpResponse = $this->generateOtp($user->phone_no);
    //                 return response()->json(['msg' => "Otp sent! But referral code is not valid", "referCode" => $request->refer_code, "Success" => true], 200);
    //             } else {
    //                 // $otpResponse = $this->generateOtp($user->phone_no);
    //                 $user = User::where('id', $user->id)->update(['referred_by' => $refer_code]);
    //                 return response()->json(["msg" => "Otp sent to matching referral code", "referCode" => $request->refer_code, "Success" => true], 200);
    //             }
    //             return response()->json(["msg" => "Otp sent successfully", "otp" => $otpResponse['OTP']]);
    //         }
    //         return response()->json(["msg" => "Otp sent successfully", "otp" => $otpResponse['OTP']]);
    //     } else {
    //         $otpResponse = $this->generateOtp($request->phone_no);
    //         $existingUser->update([
    //             "otp" => $otpResponse['OTP'],
    //         ]);
    //         return response()->json(["msg" => "Otp sent successfully", "otp" => $otpResponse['OTP']]);
    //     }

    // }

    // public function generateOtp($phone_no)
    // {
    //     # User Does not Have Any Existing OTP
    //     try {
    //         $api_key = env("2FACTOR_API_KEY");
    //         $curl = curl_init("https://2factor.in/API/V1/$api_key/SMS/$phone_no/AUTOGEN2/Jjludo123");
    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //         $json = curl_exec($curl);
    //         $jsonArray = json_decode($json, true);
    //         return $jsonArray;
    //     } catch (\Throwable $th) {
    //         print_r($th);
    //     }
    // }
    // public function verifyOtp(Request $request)
    // {
    //     $rules = array(
    //         'phone_no' => 'required|digits:10|numeric',
    //         'otp_no' => 'required|min:6|numeric',
    //     );
    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
    //     }
    //     $phone_no = $request->phone_no;
    //     $otp_no = $request->otp_no;
    //     $verify_user = User::where('phone_no', $phone_no)->first();
    //     $bytes = random_bytes(4);
    //     $data = bin2hex($bytes);
    //     $random_name = $data;
    //     if (!$verify_user->name) {
    //         User::where('phone_no', $phone_no)->first()->update(['name' => $random_name]);
    //     }
    //     try {
    //         $api_key = env("2FACTOR_API_KEY");
    //         $curl = curl_init("https://2factor.in/API/V1/$api_key/SMS/VERIFY3/$phone_no/$otp_no");
    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //         $json = curl_exec($curl);
    //         $jsonArray = json_decode($json, true);
    //         if ($jsonArray['Status'] === 'Success') {
    //             $token = $verify_user->createToken($verify_user->phone_no . '_Token')->plainTextToken;
    //             return response()->json(["msg" => "Otp verified Successfully.", "token" => $token, $jsonArray]);
    //         } else {
    //             return response()->json(["msg" => "Invalid OTP"], 401);
    //         }
    //     } catch (\Throwable $th) {
    //         print_r($th);
    //     }
    // }

    public function checkAuth()
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $getUserInfo = KycUpload::where('user_id', $user->id)->first();
            // find total battle played by user
            $totalBattle = RoomHistory::where('player_id', $user->id)->count();
            $total_Refferal = User::where('referred_by', '=', $user->affiliate_id)->get();
            $referralCount = $total_Refferal->count();
            $commission_data = CommissionLimitManagement::orderBy('id', 'DESC')->first();
            $activate_setting = ActivateMannaulSetting::all();
            $userDetails = (object) [
                'phoneNumber' => $user->phone_no,
                'userId' => $user->id,
                'name' => $user->name,
                'profileImage' => $user->user_image,
                'walletBalance' => $user->balance,
                'upiId' => $user->upi_id,
                'paytm' => $activate_setting[0]->status,
                'mannual_roomcode' => $activate_setting[1]->status,
                'depositeCash' => $user->deposit_cash,
                'winningCash' => $user->winning_cash,
                'refer_cash' => $user->refer_cash,
                'kyc' => $getUserInfo ? $getUserInfo->kyc_status : false,
                'referCode' => $user->referred_by,
                'affiliateId' => $user->affiliate_id,
                'battlePlayed' => $totalBattle,
                'totalRefers' => $referralCount,
                'refer_commission_percentage' => $commission_data->refer_commission_percentage,
                'wallet_withdraw_limit' => $commission_data->wallet_withdraw_limit,
                'refer_reedem_limit' => $commission_data->refer_reedem_limit,
                'max_refer_commission' => $commission_data->max_refer_commission,
                'pending_game_penalty_amt' => $commission_data->pending_game_penalty_amt,
                'wrong_result_penalty_amt' => $commission_data->wrong_result_penalty_amt,
                'created_battles' => $user->created_battles,
            ];
            return response()->json(["msg" => "Logged In", "details" => $userDetails], 200);
        } else {
            return response()->json(["msg" => "Invalid User"], 401);
        }
    }
    public function logout()
    {
        auth('sanctum')->user()->tokens()->delete();
        return response()->json(["msg" => "Logged Out"], 200);
    }

    public function update_referral(Request $request)
    {
        $user = auth('sanctum')->user();
        $check_affiliate_id = User::where('affiliate_id', $request->referCode)->where('id', '!=', $user->id)->first();

        if (!$check_affiliate_id) {
            return response()->json(['msg' => "Invalid Referral Code", "referCode" => $request->referCode, "Success" => false], 400);
            die;
        }
        User::where('id', $user->id)->update(['referred_by' => $request->referCode]);
        return response()->json(["msg" => "Referral Updated Successfully", "referCode" => $request->referCode, "success" => true], 200);
    }

    public function totalRefferal()
    {
        $user = auth('sanctum')->user();
        $total_referral = User::where('referred_by', '=', $user->affiliate_id)->get();
        $referralCount = $total_referral->count();
        $commission_percentage = ReferCommission::select('commission_percentage')->orderBy('id', 'DESC')->first();
        return response()->json(['total_referral' => $referralCount, 'commission_percentage' => $commission_percentage->commission_percentage]);
    }

    public function update_userProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        if ($request->userName) {
            $check_username = User::where('name', $request->userName)->first();
            if ($check_username) {
                return response()->json(['msg' => "Username already exists.", "Success" => false], 400);
            }
            User::where('id', $user->id)->update(['name' => $request->userName]);
            return response()->json(["msg" => "Username Updated Successfully", "userName" => $request->userName, "Success" => true], 200);
        } else if ($request->upiId) {
            $check_upiId = User::where('upi_id', $request->upiId)->first();
            if ($check_upiId) {
                return response()->json(['msg' => "Upi Id is already exists.", "Success" => false], 400);
            }
            User::where('id', $user->id)->update(['upi_id' => $request->upiId]);
            return response()->json(["msg" => "Upi id Updated Successfully", "upiId" => $request->upiId, "Success" => true], 200);
        } else if ($request->file('user_image')) {
            $rules = array(
                'user_image' => 'required|mimes:jpeg,jpg,png|max:5120',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
            }
            $user_image = $this->upload($request->file('user_image'), $user->id);
        } else if ($request->user_image) {
            $user_image = $request->user_image;
        } else {
            return response()->json(["msg" => "Username is required", "Success" => false], 401);
        }
        User::where('id', $user->id)->update(['user_image' => $user_image]);
        return response()->json(["msg" => "User Image Updated Successfully", "Success" => true, 'profileImage' => $user_image], 200);
    }

    public function upload($image)
    {
        $destination_path = storage_path('app/public/images/user-profiles/');
        $image_name = time() . '.' . $image->getClientOriginalName();
        $img = Image::make($image->getRealPath());
        $img->resize(1920, 1080);
        $img->save($destination_path . $image_name, 10);
        return $image_name;
    }

    public function getData()
    {
        $data = BattleApiController::index(1);

        return response()->json($data->original);
    }
}

// class UserApiController extends Controller
// {
//     public function sendOtp(Request $request)
//     {
//         // $otp_no = rand(100000,999999);
//         // $refer_code = $request->input('refer_code');
//         $refer_code = $request->refer_code;
//         $rules = array(
//             'phone_no' => 'required|unique:otp_logins|digits:10|numeric',
//         );
//         $messages = array(
//             'phone_no.required' => 'phone number field is required',
//             'phone_no.digits' => 'phone number must be 10 digit number',
//         );
//         $validator = Validator::make($request->all(), $rules, $messages);
//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
//         }

//         $is_active = User::where('phone_no', $request->phone_no)->first();
//         if ($is_active && !$is_active->active) {
//             return response()->json(["msg" => "Your account is currently deactivated by admin . Please contact to support"], 401);
//         }
//         $otp_no = 123456;
//         $newUser = User::where('phone_no', $request->phone_no)->first();
//         if (!empty($newUser)) {
//             $newUser->update([
//                 "otp" => $otp_no,
//             ]);
//         } else {
//             $number = rand(100, 999);
//             $character = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
//             $two_character = substr(str_shuffle($character), 0, 2) . $number;
//             $referral = str_shuffle($two_character);
//             $user = User::create(
//                 ["phone_no" => $request->phone_no, "otp" => $otp_no, "affiliate_id" => $referral]
//             );
//             // update refer_code if any exists when a new user join with link
//             if (!empty($refer_code)) {
//                 $check_affiliate_id = User::where('affiliate_id', $refer_code)->where('id', '!=', $user->id)->first();
//                 if (!$check_affiliate_id) {
//                     return response()->json(['msg' => "Otp sent! But referral code is not valid", "referCode" => $request->referCode, "Success" => true], 200);
//                     die;
//                 } else {
//                     User::where('id', $user->id)->update(['referred_by' => $refer_code]);
//                     return response()->json(["msg'" => "Otp sent by matching referral code", "referCode" => $request->referCode, "Success" => true], 200);
//                 }
//             }
//         }
//         // $user->roles()->attach(2);
//         // send otp to mobile number
//         return response()->json(["msg" => "Otp sent successfully", "otp" => $otp_no]);
//     }

//     public function verifyOtp(Request $request)
//     {
//         $rules = array(
//             'phone_no' => 'required|digits:10|numeric',
//             'otp_no' => 'required|min:6|numeric',
//         );
//         $validator = Validator::make($request->all(), $rules);
//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
//         }
//         $phone_no = $request->phone_no;
//         $otp_no = $request->otp_no;
//         $verify_user = User::where('phone_no', $phone_no)->first();
//         // genarate random name for new user
//         $bytes = random_bytes(4);
//         $data = bin2hex($bytes);
//         $random_name = $data;
//         // $prev_user_name = isset($verify_user->name) ? $verify_user->name :'';
//         if (!$verify_user->name) {
//             User::where('phone_no', $phone_no)->first()->update(['name' => $random_name]);
//         }
//         if ($verify_user->otp != $otp_no) {
//             return response()->json(["msg" => "Invalid OTP"], 401);
//         }
//         if ($verify_user) {
//             User::where('phone_no', $phone_no)->first()->update(['otp' => '']);
//             $token = $verify_user->createToken($verify_user->phone_no . '_Token')->plainTextToken;
//             return response()->json(["msg" => "Otp verified Successfully.", "token" => $token]);
//         } else {
//             // return response()->json(["msg" => "Invalid OTP"], 401);
//         }
//     }
//     public function checkAuth()
//     {
//         $user = auth('sanctum')->user();
//         if ($user) {
//             $getUserInfo = KycUpload::where('user_id', $user->id)->first();
//             // find total battle played by user
//             $totalBattle = RoomHistory::where('player_id', $user->id)->count();
//             $total_Refferal = User::where('referred_by', '=', $user->affiliate_id)->get();
//             $referralCount = $total_Refferal->count();
//             $commission_data = CommissionLimitManagement::orderBy('id', 'DESC')->first();
//             $userDetails = (object) [
//                 'phoneNumber' => $user->phone_no,
//                 'userId' => $user->id,
//                 'name' => $user->name,
//                 'profileImage' => $user->user_image,
//                 'walletBalance' => $user->balance,
//                 'depositeCash' => $user->deposit_cash,
//                 'winningCash' => $user->winning_cash,
//                 'refer_cash' => $user->refer_cash,
//                 'kyc' => $getUserInfo ? $getUserInfo->kyc_status : false,
//                 'referCode' => $user->referred_by,
//                 'affiliateId' => $user->affiliate_id,
//                 'battlePlayed' => $totalBattle,
//                 'totalRefers' => $referralCount,
//                 'refer_commission_percentage' => $commission_data->refer_commission_percentage,
//                 'wallet_withdraw_limit' => $commission_data->wallet_withdraw_limit,
//                 'refer_reedem_limit' => $commission_data->refer_reedem_limit,
//                 'max_refer_commission' => $commission_data->max_refer_commission,
//                 'refer_cash' => $user->refer_cash,
//                 'created_battles' => $user->created_battles,
//             ];
//             return response()->json(["msg" => "Logged In", "details" => $userDetails], 200);
//         } else {
//             return response()->json(["msg" => "Invalid User"], 401);
//         }
//     }
//     public function logout()
//     {
//         auth('sanctum')->user()->tokens()->delete();
//         return response()->json(["msg" => "Logged Out"], 200);
//     }

//     public function update_referral(Request $request)
//     {
//         $user = auth('sanctum')->user();
//         $check_affiliate_id = User::where('affiliate_id', $request->referCode)->where('id', '!=', $user->id)->first();

//         if (!$check_affiliate_id) {
//             return response()->json(['msg' => "Invalid Referral Code", "referCode" => $request->referCode, "Success" => false], 400);
//             die;
//         }
//         User::where('id', $user->id)->update(['referred_by' => $request->referCode]);
//         return response()->json(["msg" => "Referral Updated Successfully", "referCode" => $request->referCode, "success" => true], 200);
//     }

//     public function totalRefferal()
//     {
//         $user = auth('sanctum')->user();
//         $total_referral = User::where('referred_by', '=', $user->affiliate_id)->get();
//         $referralCount = $total_referral->count();
//         $commission_percentage = ReferCommission::select('commission_percentage')->orderBy('id', 'DESC')->first();
//         return response()->json(['total_referral' => $referralCount, 'commission_percentage' => $commission_percentage->commission_percentage]);
//     }

//     public function update_userProfile(Request $request)
//     {
//         $user = auth('sanctum')->user();
//         if ($request->userName) {
//             $check_username = User::where('name', $request->userName)->first();
//             if ($check_username) {
//                 return response()->json(['msg' => "Username already exists.", "Success" => false], 400);
//             }
//             User::where('id', $user->id)->update(['name' => $request->userName]);
//             return response()->json(["msg" => "Username Updated Successfully", "userName" => $request->userName, "Success" => true], 200);
//         } else if ($request->file('user_image')) {
//             $rules = array(
//                 'user_image' => 'required|mimes:jpeg,jpg,png|max:5120',
//             );
//             $validator = Validator::make($request->all(), $rules);
//             if ($validator->fails()) {
//                 return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
//             }
//             $user_image = $this->upload($request->file('user_image'), $user->id);
//         } else if ($request->user_image) {
//             $user_image = $request->user_image;
//         } else {
//             return response()->json(["msg" => "Username is required", "Success" => false], 401);
//         }
//         User::where('id', $user->id)->update(['user_image' => $user_image]);
//         return response()->json(["msg" => "User Image Updated Successfully", "Success" => true, 'profileImage' => $user_image], 200);
//     }

//     public function upload($image)
//     {
//         $destination_path = storage_path('app/public/images/user-profiles/');
//         $image_name = time() . '.' . $image->getClientOriginalName();
//         $img = Image::make($image->getRealPath());
//         $img->resize(1920, 1080);
//         $img->save($destination_path . $image_name, 10);
//         return $image_name;
//     }

//     public function getData()
//     {
//         $data = BattleApiController::index(1);

//         return response()->json($data->original);
//     }
// }
