@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <a href="{{ route('admin.room_historys.index') }}" class="btn btn-primary text-light">Back</a>
        <h4 class="ml-4">Update Player Game Result</h5>
    </div>

    <div class="card-body">
        <form action="{{ route("admin.room_historys.update", [$room_history->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }} w-25">
                <label for="admin_provided_status">Admin Provided Status</label>
                <select id="admin_provided_status" name="admin_provided_status" class="form-control">
                @foreach (config('admin_game_status_update') as $index => $game_status )
                        <option {{ $room_history->admin_provided_status == $index ? 'selected' : '' }} value="{{ $room_history->admin_provided_status == $index ? $index : $index}}"> {{ $game_status }}</option>
                @endforeach
                </select>
                @if($errors->has('admin_provided_status'))
                    <p class="help-block">
                        {{ $errors->first('admin_provided_status') }}
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
