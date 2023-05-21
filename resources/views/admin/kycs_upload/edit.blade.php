@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <a href="{{ route('admin.kyc_uploads.index') }}" class="btn btn-primary text-light">Back</a>
        <h4 class="ml-4"> <b style="text-transform:capitalize;">{{ isset($data->user->name) ? $data->user->name : '' }}</b> Kyc Detail</h4>
    </div>
    <div class="card-body">
        <form action="{{ route("admin.kyc_uploads.update", [$data]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('id') ? 'has-error' : '' }}">
                <input type="hidden" id="id" name="id" value="{{ old('username', isset($user) ? $user->id : '') }}">
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('document_id') ? 'has-error' : '' }}">
                    <label for="name">Document-Type</label>
                    <select id="document_id" name="document_id" class="form-control">
                        <!-- <option selected value="{{ old('document_id', isset($data->document_id) ? $data->document_id : '') }}">{{$data->document->document_type}}
                        </option> -->
                        @foreach ($get_all_documents as $key => $document )
                        <option {{ $data->document_id == $document->id ? 'selected' : '' }} value="{{ $document->id}}"> {{ $document->document_type   }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('document_id'))
                    <p class="help-block text-danger">
                        {{ $errors->first('document_id') }}
                    </p>
                    @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('document_number') ? 'has-error' : '' }}">
                    <label for="name">Document-Number</label>
                    <input type="text" id="document_number" name="document_number" class="form-control" value="{{ old('document_number', isset($data->document_number) ? $data->document_number : '') }}">
                    @if($errors->has('document_number'))
                    <p class="help-block text-danger">
                        {{ $errors->first('document_number') }}
                    </p>
                    @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                    <label for="name">First-Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name', isset($data->first_name) ? $data->first_name : '') }}">
                    @if($errors->has('first_name'))
                    <p class="help-block text-danger">
                        {{ $errors->first('first_name') }}
                    </p>
                    @endif
                    </div>
                </div>


                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                    <label for="name">Last-Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name', isset($data->last_name) ? $data->last_name : '') }}">
                    @if($errors->has('last_name'))
                    <p class="help-block text-danger">
                        {{ $errors->first('last_name') }}
                    </p>
                    @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('dob') ? 'has-error' : '' }}">
                    <label for="name">DOB</label>
                    <input type="date" id="dob" name="dob" class="form-control" value="{{ old('dob', isset($data->dob) ? $data->dob : '') }}">
                    @if($errors->has('dob'))
                    <p class="help-block text-danger">
                        {{ $errors->first('dob') }}
                    </p>
                    @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('state') ? 'has-error' : '' }}">
                    <label for="name">State</label>
                    <select class="form-control" id="state" name="state">
                        @foreach ($get_all_states as $key => $state )
                        <option {{ $data->state_id == $state->id ? 'selected' : '' }} value="{{ $state->id}}"> {{ $state->state   }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('state'))
                    <p class="help-block text-danger">
                        {{ $errors->first('state') }}
                    </p>
                    @endif
                    </div>
                </div>


                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('kyc_status') ? 'has-error' : '' }}">
                    <label for="kyc_status">Kyc-Status</label>
                    @if ($data->kyc_status == 0)
                    <select class="form-control" id="kyc_status" name="kyc_status">
                        <option selected value="0">pending</option>
                        <option value="1">verified</option>
                        <option value="2">rejected</option>
                    </select>
                    @endif
                    @if ($data->kyc_status == 1)
                    <p class="text-success">
                        <b>Verified *</b>
                    </p>
                    @endif
                    @if($errors->has('kyc_status'))
                    <p class="help-block text-danger">
                        {{ $errors->first('kyc_status') }}
                    </p>
                    @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('front_photo') ? 'has-error' : '' }}">
                    <label for="front_photo" class="w-100">Front-Photo</label>
                    <input type="file" id="front_photo" name="front_photo" class="form-control float-left" style="width:70%" value="{{ old('front_photo', isset($data) ? $data->front_photo : '') }}">
                    <img onclick=setModal(this) class="ml-3" width="40px" height="40px" data-toggle="modal" data-target="#exampleModalCenter" src="{{asset(env('IMAGE_URL').'kyc-documents/'.$data->user_id.'/'.$data->front_photo)}}" alt="{{$data->front_photo}}" />
                    @if($errors->has('front_photo'))
                    <p class="help-block text-danger">
                        {{ $errors->first('front_photo') }}
                    </p>
                    @endif
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="form-group {{ $errors->has('back_photo') ? 'has-error' : '' }}">
                    <label for="back_photo" class="w-100">Back-Photo</label>
                    <input type="file" id="back_photo" name="back_photo" class="form-control float-left" style="width:70%" value="{{ old('back_photo', isset($data) ? $data->back_photo : '') }}">
                    <img onclick=setModal(this) class="ml-3" width="40px" height="40px" data-toggle="modal" data-target="#exampleModalCenter" src="{{asset(env('IMAGE_URL').'kyc-documents/'.$data->user_id.'/'.$data->back_photo)}}" alt="{{$data->back_photo}}" />
                    @if($errors->has('back_photo'))
                    <p class="help-block text-danger">
                        {{ $errors->first('back_photo') }}
                    </p>
                    @endif
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-lg btn-danger">Save</button>
            </div>
        </form>
    </div>
</div>

@include('partials.modal')
@endsection

@section('scripts')
@parent
<script type="text/javascript">
    $(document).ready(function() {
        const pad2 = (n) => {
            return (n < 10 ? '0' : '') + n;
        }
        var today = new Date();
        var month = pad2(today.getMonth() + 1); //months (0-11)
        var day = pad2(today.getDate()); //day (1-31)
        var year = today.getFullYear() - 18;
        const maxDate = `${year}-${month}-${day}`;
        $('#dob').prop('max', maxDate)
    })
</script>
@endsection
