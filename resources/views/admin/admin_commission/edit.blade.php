@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edit Commission Management For Battle
    </div>

    <div class="card-body">
        <form action="{{ route("admin.admin_commissions.update", [$admin_commission->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('from_amount') ? 'has-error' : '' }}">
                <label for="from_amount">From-Amount</label>
                <input type="text" id="from_amount" name="from_amount" class="form-control" value="{{ old('from_amount', isset($admin_commission) ? $admin_commission->from_amount : '') }}" required>
                @if($errors->has('from_amount'))
                <p class="help-block text-danger">
                    {{ $errors->first('from_amount') }}
                </p>
                @endif
            </div>

            @if($admin_commission->to_amount != 0)
            <div class="form-group {{ $errors->has('to_amount') ? 'has-error' : '' }}">
                <label for="to_amount">To-Amount</label>
                <input type="text" id="to_amount" name="to_amount" class="form-control" value="{{ old('to_amount', isset($admin_commission) ? $admin_commission->to_amount : '') }}" required>
                @if($errors->has('to_amount'))
                <p class="help-block text-danger">
                    {{ $errors->first('to_amount') }}
                </p>
                @endif
            </div>
            @endif

            <div class="form-group {{ $errors->has('commission_type') ? 'has-error' : '' }}">
                <label for="commission_type">Commission-Type</label>
                <select class="form-control" id="commission_type" name="commission_type">
                    @if ($admin_commission->commission_type == '1')
                    <option selected value="1">Percentage</option>
                    <option value="2">Amount</option>
                    @endif
                    @if ($admin_commission->commission_type == '2')
                    <option selected value="2">Amount</option>
                    <option value="1">Percentage</option>
                    @endif
                    <!-- <option>{{$admin_commission->commission_type}}</option> -->
                </select>
                @if($errors->has('commission_type'))
                <p class="help-block text-danger">
                    {{ $errors->first('commission_type') }}
                </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('commission_value') ? 'has-error' : '' }}">
                <label for="commission_value">Commission-Value</label>
                <!-- <input type="number" min="1" max="{{$admin_commission->commission_type == '1' ? 100 : 100000}}" id="commission_value" name="commission_value" class="form-control" value="{{ old('commission_value', isset($admin_commission) ? $admin_commission->commission_value : '') }}" required> -->
                <input type="text" id="commission_value" name="commission_value" class="form-control" value="{{ old('commission_value', isset($admin_commission) ? $admin_commission->commission_value : '') }}" required>
                @if($errors->has('commission_value'))
                <p class="help-block text-danger">
                    {{ $errors->first('commission_value') }}
                </p>
                @endif
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')

@parent
@endsection
