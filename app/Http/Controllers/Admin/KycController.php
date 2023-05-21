<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\KycRequest;
use App\Kyc;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;


class KycController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);

        // $users = User::all();
        //return view('admin.kycs.index', compact('users'));
        return view('admin.kycs.index');
    }


    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);
        $user->load('kycs');
        return view('admin.kycs.show', compact('user'));
    }

    public function store(User $user, KycRequest $request)
    {
        abort_unless(\Gate::allows('user_show'), 403);
        $kyc = new Kyc();
        if (!in_array($request->document_id, array_keys(config('document')))) {
            abort(500);
        }
            if (!$path = $this->upload($request->file('document'), $user->id)) {
                return redirect()->back()->withErrors("Could Not Upload Document");
            }
        $kyc->create([
            'path' => $path,
            'document_id' => $request->document_id, // request se laana hai
            'user_id' => $user->id,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return redirect()->back()->with('success', 'Document Uploaded Successfully !');
    }

    public function delete(Kyc $kyc, User $user)
    {
        $destination_path = 'storage/images/documents/' . $user->id . '/' . $kyc->path;
        if (File::exists($destination_path)) {
            File::delete($destination_path);
            $kyc->delete();
            return redirect()->back()->withErrors("Document Deleted !");
        } else {
            return redirect()->back()->withErrors("File Not Exists");
        }
    }

    public function upload($file, $userid)
    {
        $destination_path = 'public/images/documents/' . $userid;
        $image = $file;
        $image_name = time() . '.' . $image->extension();
        if (File::exists('storage/images/documents/' . $userid . '/' . $image_name)) {
            return false;
        }
        $path = $file->storeAs($destination_path, $image_name);
        return $image_name;
    }
}
