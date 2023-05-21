@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} Games
    </div>

    <div class="card-body">
        <form action="{{ route("admin.game_listings.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Name*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($gamelistings) ? $gamelistings->name : '') }}">
                @if($errors->has('name'))
                    <p class="help-block text-danger">
                        {{ $errors->first('name') }}
                    </p>
                @endif
            </div>
            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                <label for="image">Image*</label>
                <input type="file" id="image" name="image" class="form-control" value="{{ old('image', isset($gamelistings) ? $gamelistings->image : '') }}">
                @if($errors->has('image'))
                    <p class="help-block text-danger">
                        {{ $errors->first('image') }}
                    </p>
                @endif
            </div>
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">Description*</label>
                <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($gamelistings) ? $gamelistings->description : '') }}">
                @if($errors->has('description'))
                    <p class="help-block text-danger">
                        {{ $errors->first('description') }}
                    </p>
                @endif
            </div>
            <div>
                <button type="submit" class="btn btn-md btn-danger">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection


