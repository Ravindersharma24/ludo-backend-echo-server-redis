@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} Game-Battle
    </div>

    <div class="card-body">
        <form action="{{ route("admin.rooms.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('game_id') ? 'has-error' : '' }}">
                <label for="search">Select Game</label>
                <select class="form-control" name="game_id" id="game_id">
                    <option selected disabled>Select</option>
                @foreach ($gamelisting as $key => $game )
                    <option value="{{ $game->id}}">{{$game->name}}</option>
                @endforeach
                </select>
                @if($errors->has('game_id'))
                    <p class="help-block">
                        {{ $errors->first('game_id') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                <label for="search">Code</label>
                <input type="text" id="code" name="code" class="form-control">
                @if($errors->has('code'))
                    <p class="help-block">
                        {{ $errors->first('code') }}
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
