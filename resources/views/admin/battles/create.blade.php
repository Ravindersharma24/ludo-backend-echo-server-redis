@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} Battle For {{ $game_name->name }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.battles.create.store", ['gameId' => $gameId]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="search">Game Name</label>
                <input id="search" class="form-control" name="search">
                <input type="hidden" name="game_listing_id" id="game_listing_id" />
                @if($errors->has('name'))
                    <p class="help-block">
                        {{ $errors->first('name') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div> -->

            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="price">Price</label>
                <input type="text" id="price" name="price" class="form-control" value="{{ old('price', isset($battles) ? $battles->price : '') }}">
                @if($errors->has('price'))
                    <p class="help-block">
                        {{ $errors->first('price') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('entry_fees') ? 'has-error' : '' }}">
                <label for="entry_fees">Entry Fees</label>
                <input type="text" id="entry_fees" name="entry_fees" class="form-control" value="{{ old('entry_fees', isset($battles) ? $battles->entry_fees : '') }}">
                @if($errors->has('entry_fees'))
                    <p class="help-block">
                        {{ $errors->first('entry_fees') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.password_helper') }}
                </p>
            </div>

            {{-- <div class="form-group {{ $errors->has('live_player') ? 'has-error' : '' }}">
                <label for="live_player">Live Player</label>
                <input type="text" id="live_player" name="live_player" class="form-control" value="{{ old('live_player', isset($battles) ? $battles->live_player : '') }}">
                @if($errors->has('live_player'))
                    <p class="help-block">
                        {{ $errors->first('live_player') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.password_helper') }}
                </p>
            </div> --}}

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
