<?php

namespace App\Http\Controllers\Admin;

use App\Document;
use App\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateKycUploadRequest;
use App\KycUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class KycUploadController extends Controller
{
    public function index()
    {
        try{
            return view('admin.kycs_upload.index');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.kyc_uploads.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function edit($id)
    {
        try{
            $data = KycUpload::with('user')->with('document')->where('user_id', $id)->first();
            $get_all_documents = Document::select(['id', 'document_type'])->get();
            $get_all_states = State::select(['id', 'state'])->get();
            return view('admin.kycs_upload.edit', compact('data', 'get_all_documents', 'get_all_states'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.kyc_uploads.index')->with('error', 'Something went wrong');
            }
        }
    }
    public function update(KycUpload $kyc_upload,UpdateKycUploadRequest $request)
    {
        try
        {
            $front_current_path = 'storage/images/kyc-documents/' . $kyc_upload->user_id . '/' . $request->front_photo;
            $back_current_path = 'storage/images/kyc-documents/' . $kyc_upload->user_id . '/' . $request->back_photo;
            $front_image_destination_path = 'storage/images/kyc-documents/' . $kyc_upload->user_id . '/' . $kyc_upload->front_photo;
            $back_image_destination_path = 'storage/images/kyc-documents/' . $kyc_upload->user_id . '/' . $kyc_upload->back_photo;

            $front_image = $kyc_upload->front_photo;
            $back_image = $kyc_upload->back_photo;
            if (!empty($request->file('front_photo'))) {
                $front_image = $this->upload($request->file('front_photo'), $kyc_upload);
            }
            if (!empty($request->file('back_photo'))) {
                $back_image = $this->upload($request->file('back_photo'), $kyc_upload);
            }
            if ($front_current_path != "storage/images/kyc-documents/" . $kyc_upload->user_id . "/") {
                File::delete($front_image_destination_path);
            }
            if ($back_current_path != "storage/images/kyc-documents/" . $kyc_upload->user_id . "/") {
                File::delete($back_image_destination_path);
            }
            $kyc_upload->update([
                'document_id' => $request->document_id,
                'document_number' => $request->document_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob,
                'state_id' => $request->state,
                'front_photo' => $front_image,
                'back_photo' => $back_image,
                'kyc_status' => $request->kyc_status,
                'updated_at' => Carbon::now()->timestamp,
            ]);
            if ($kyc_upload->kyc_status == '2') {
                // at the time of rejection delete user kyc details
                $kyc_upload->delete();
                File::delete($front_image_destination_path);
                File::delete($back_image_destination_path);
                return redirect()->route('admin.kyc_uploads.index')->with('error', 'Kyc Rejected !');
            }
            return redirect()->route('admin.kyc_uploads.index')->with('success', 'Kyc Updated Successfully !');
        }
        catch (\Throwable $th){
            if($th){
                return redirect()->route('admin.kyc_uploads.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function upload($file, $user)
    {
        try
        {
        $front_image_destination = 'storage/images/kyc-documents/' . $user->user_id . '/' . $user->front_photo;
        $back_image_destination = 'storage/images/kyc-documents/' . $user->user_id . '/' . $user->back_photo;

        $destination_path = 'public/images/kyc-documents/' . $user->user_id;
        $image = $file;

        $image_name = time() . '.' . $image->getClientOriginalName();
        if (File::exists('storage/images/kyc-documents/' . $user->user_id . '/' . $image_name)) {
            return false;
        }
        // if($front_image_destination != 'storage/images/kyc-documents/' . $user->user_id . '/' . $image_name ){
        //     File::delete($front_image_destination);
        // }
        // if($back_image_destination != 'storage/images/kyc-documents/' . $user->user_id . '/' . $image_name){
        //     File::delete($back_image_destination);
        // }
        $path = $file->storeAs($destination_path, $image_name);
        return $image_name;
        }
        catch (\Throwable $th) {
            if($th){
                return $th;
            }
        }
    }
}
