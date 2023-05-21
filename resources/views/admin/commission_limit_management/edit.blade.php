@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edit Commission Percentage and Withdraw Limit
    </div>

    <div class="card-body">
        <form action="{{ route("admin.commission_limit_managements.update", [$commission_limit_management->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('refer_commission_percentage') ? 'has-error' : '' }}">
                <label for="refer_commission_percentage">Refer-Commission-Percentage</label>
                <input type="text" id="refer_commission_percentage" name="refer_commission_percentage" class="form-control" value="{{ old('refer_commission_percentage', isset($commission_limit_management) ? $commission_limit_management->refer_commission_percentage : '') }}">
                @if($errors->has('refer_commission_percentage'))
                    <p class="help-block text-danger">
                        {{ $errors->first('refer_commission_percentage') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('wallet_withdraw_limit') ? 'has-error' : '' }}">
                <label for="wallet_withdraw_limit">Wallet-Withdraw-Limit</label>
                <input type="text" id="wallet_withdraw_limit" name="wallet_withdraw_limit" class="form-control" value="{{ old('wallet_withdraw_limit', isset($commission_limit_management) ? $commission_limit_management->wallet_withdraw_limit : '') }}">
                @if($errors->has('wallet_withdraw_limit'))
                    <p class="help-block text-danger">
                        {{ $errors->first('wallet_withdraw_limit') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('refer_reedem_limit') ? 'has-error' : '' }}">
                <label for="refer_reedem_limit">Refer-Reedem-Limit</label>
                <input type="text" id="refer_reedem_limit" name="refer_reedem_limit" class="form-control" value="{{ old('refer_reedem_limit', isset($commission_limit_management) ? $commission_limit_management->refer_reedem_limit : '') }}">
                @if($errors->has('refer_reedem_limit'))
                    <p class="help-block text-danger">
                        {{ $errors->first('refer_reedem_limit') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('max_refer_commission') ? 'has-error' : '' }}">
                <label for="max_refer_commission">Max-Refer-Commission</label>
                <input type="text" id="max_refer_commission" name="max_refer_commission" class="form-control" value="{{ old('max_refer_commission', isset($commission_limit_management) ? $commission_limit_management->max_refer_commission : '') }}">
                @if($errors->has('max_refer_commission'))
                    <p class="help-block text-danger">
                        {{ $errors->first('max_refer_commission') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('pending_game_penalty_amt') ? 'has-error' : '' }}">
                <label for="pending_game_penalty_amt">Pending-Game-Penalty-Amount</label>
                <input type="text" id="pending_game_penalty_amt" name="pending_game_penalty_amt" class="form-control" value="{{ old('pending_game_penalty_amt', isset($commission_limit_management) ? $commission_limit_management->pending_game_penalty_amt : '') }}">
                @if($errors->has('pending_game_penalty_amt'))
                    <p class="help-block text-danger">
                        {{ $errors->first('pending_game_penalty_amt') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('wrong_result_penalty_amt') ? 'has-error' : '' }}">
                <label for="wrong_result_penalty_amt">Wrong-Result-Penalty-Amount</label>
                <input type="text" id="wrong_result_penalty_amt" name="wrong_result_penalty_amt" class="form-control" value="{{ old('wrong_result_penalty_amt', isset($commission_limit_management) ? $commission_limit_management->wrong_result_penalty_amt : '') }}">
                @if($errors->has('wrong_result_penalty_amt'))
                    <p class="help-block text-danger">
                        {{ $errors->first('wrong_result_penalty_amt') }}
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
