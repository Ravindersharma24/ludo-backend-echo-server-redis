@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Edit Information For User Creating Battles
    </div>

    <div class="card-body">
        <form action="{{ route("admin.battle_managements.update", [$battle_management->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('minimum_amount') ? 'has-error' : '' }}">
                <label for="minimum_amount">Minimum Amount To Create Battle</label>
                <input type="text" id="minimum_amount" name="minimum_amount" class="form-control" value="{{ old('minimum_amount', isset($battle_management) ? $battle_management->minimum_amount : '') }}">
                @if($errors->has('minimum_amount'))
                    <p class="help-block text-danger">
                        {{ $errors->first('minimum_amount') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('maximum_battle') ? 'has-error' : '' }}">
                <label for="maximum_battle">Maximum Numbers Of Battle</label>
                <input type="text" id="maximum_battle" name="maximum_battle" class="form-control" value="{{ old('maximum_battle', isset($battle_management) ? $battle_management->maximum_battle : '') }}">
                @if($errors->has('maximum_battle'))
                    <p class="help-block text-danger">
                        {{ $errors->first('maximum_battle') }}
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
