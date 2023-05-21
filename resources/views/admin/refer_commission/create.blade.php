@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Add Commission
    </div>

    <div class="card-body">
        <form action="{{ route("admin.refer_commissions.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('commission_percentage') ? 'has-error' : '' }}">
                <label for="search">Commission Percentage</label>
                <input type="text" id="commission_percentage" name="commission_percentage" class="form-control" value="{{ old('commission_percentage', isset($gamelistings) ? $refer_commission->commission_percentage : '') }}">
                @if($errors->has('commission_percentage'))
                    <p class="help-block">
                        {{ $errors->first('commission_percentage') }}
                    </p>
                @endif
            </div>
            <div>
                <button type="submit" class="btn btn-sm btn-danger">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')

@parent
@endsection
