@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <a href="{{ route('admin.user_profiles.index') }}" class="btn btn-primary text-light">Back</a>
        <h4 class="ml-4"> <b style="text-transform:capitalize;">{{ isset($user->name) ? $user->name : '' }}</b> Profile</h4>
    </div>

    <div class="card-body">
    <form action="{{ route("admin.user_profiles.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('id') ? 'has-error' : '' }}">
                <input type="hidden" id="id" name="id" value="{{ old('username', isset($user) ? $user->id : '') }}">
            </div>
            <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                <label for="name">Phone-No</label>
                <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile', isset($user->phone_no) ? $user->phone_no : '') }}" readonly>
                @if($errors->has('mobile'))
                    <p class="help-block">
                        {{ $errors->first('mobile') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('kyc_upload') ? 'has-error' : '' }}">
                <label for="name">Kyc-Upload</label>
                <select class="form-control" name="kyc_upload" id="kyc_upload">
                    @if(isset($user->user_profiles[0]['kyc_upload']) ? $user->user_profiles[0]['kyc_upload'] == 1 : '')
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                    @endif
                    @if(isset($user->user_profiles[0]['kyc_upload']) ? $user->user_profiles[0]['kyc_upload'] == 0 : '')
                    <option selected value="0">No</option>
                    <option value="1">Yes</option>
                    @endif
                    @if (!isset($user->user_profiles[0]['kyc_upload']))
                    <option selected disabled>Select</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                    @endif
                </select>
                @if($errors->has('kyc_upload'))
                    <p class="help-block">
                        {{ $errors->first('kyc_upload') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('kyc_verified') ? 'has-error' : '' }}">
                <label for="name">Kyc-Verified</label>
                <select class="form-control" name="kyc_verified" id="kyc_verified">
                    @if(isset($user->user_profiles[0]['kyc_verified']) ? $user->user_profiles[0]['kyc_verified'] == 1 : '')
                    <option selected value="1">Yes</option>
                    <option value="0">No</option>
                    @endif
                    @if(isset($user->user_profiles[0]['kyc_verified']) ? $user->user_profiles[0]['kyc_verified'] == 0 : '')
                    <option selected value="0">No</option>
                    <option value="1">Yes</option>
                    @endif
                    @if (!isset($user->user_profiles[0]['kyc_verified']))
                    <option selected disabled>Select</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                    @endif
                </select>
                @if($errors->has('kyc_verified'))
                    <p class="help-block">
                        {{ $errors->first('kyc_verified') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('cash_won') ? 'has-error' : '' }}">
                <label for="name">Cash-Won</label>
                <input type="text" id="cash_won" name="cash_won" class="form-control" value="{{ old('cash_won', isset($user->user_profiles[0]['cash_won']) ? $user->user_profiles[0]['cash_won'] : '') }}">
                @if($errors->has('cash_won'))
                    <p class="help-block">
                        {{ $errors->first('cash_won') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('battle_played') ? 'has-error' : '' }}">
                <label for="name">Battle-Played</label>
                <input type="text" id="battle_played" name="battle_played" class="form-control" value="{{ old('battle_played', isset($user->user_profiles[0]['battle_played']) ? $user->user_profiles[0]['battle_played'] : '') }}">
                @if($errors->has('battle_played'))
                    <p class="help-block">
                        {{ $errors->first('battle_played') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('kyc_link') ? 'has-error' : '' }}">
                <label for="name">Kyc-Link</label>
                <input type="text" id="kyc_link" name="kyc_link" class="form-control" value="{{ old('kyc_link', isset($user->user_profiles[0]['kyc_link']) ? $user->user_profiles[0]['kyc_link'] : '') }}">
                @if($errors->has('kyc_link'))
                    <p class="help-block">
                        {{ $errors->first('kyc_link') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>
            <div>
                <button type="submit" class="btn btn-sm btn-danger">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection
