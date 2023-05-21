<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\Document;
use App\Http\Controllers\Controller;
use App\KycUpload;
use App\State;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class KycUploadApiController extends Controller
{
    public function store(User $user, Request $request)
    {
        $user = auth('sanctum')->user(); // check authorised user
        $rules = array(
            'document_id' => 'required',
            'document_number' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
            'state' => 'required',
            'front_photo' => 'required|mimes:jpeg,jpg,png|max:5120',
            'back_photo' => 'required|mimes:jpeg,jpg,png|max:5120',
        );
        // $messages = array(
        //     'phone_no.required' => 'phone number field is required',
        //     'phone_no.digits' => 'phone number must be 10 digit number',
        // );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
        }
        $front_photo = $this->upload($request->file('front_photo'), $user->id);
        $back_photo = $this->upload($request->file('back_photo'), $user->id);

        // fetching actual name for document,state of a user entered
        $document_name = Document::select(['document_type'])->where('id', $request->document_id)->first();
        $state_name = State::select(['state'])->where('id', $request->state)->first();
        // return $state_name->state;
        // return $document_name->document_type;
        // die;

        KycUpload::updateOrCreate(
            ['user_id' => $user->id],
            [
                'document_id' => $request->document_id,
                'user_id' => $user->id,
                'document_number' => $request->document_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob,
                'state_id' => $request->state,
                'front_photo' => $front_photo,
                'back_photo' => $back_photo,
                // 'kyc_status' => $request->kyc_status,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ]
        );

        return response()->json(["msg" => "kyc uploaded", "success" => true]);
    }

    public function upload($image, $userid)
    {
        try {
            $destination_path = storage_path('app/public/images/kyc-documents/' . $userid . '/');
            $image_name = time() . '.' . $image->getClientOriginalName();
            $img = Image::make($image->getRealPath());
            $img->resize(1920, 1080);
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0777);
            }
            $img->save($destination_path . '/' . $image_name);
            return $image_name;
        } catch (Exception $err) {
            print_r($err);
        }
    }

    public function getDocuments()
    {
        return response()->json(Document::select(['id', 'document_type as name'])->get());
    }
    public function getStates()
    {
        return response()->json(State::select(['id', 'state as name'])->get());
    }
}
