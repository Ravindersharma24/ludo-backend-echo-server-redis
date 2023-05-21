@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <a href="{{ route('admin.game_listings.index') }}" class="btn btn-primary text-light">Back</a>
        <h4 class="ml-4">{{ trans('global.edit') }} Game</h5>
    </div>

    <div class="card-body">
        <form action="{{ route("admin.game_listings.update", [$game_listing->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($game_listing) ? $game_listing->name : '') }}">
                @if($errors->has('name'))
                    <p class="help-block text-danger">
                        {{ $errors->first('name') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                <label for="image">Image</label>
                <input type="file" id="image" style="color:transparent;" onchange="this.style.color = 'black';" name="image" class="form-control" value="{{ old('image', isset($game_listing) ? $game_listing->image : '') }}">
                <img onclick=setModal(this) width="40px" data-toggle="modal" data-target="#exampleModalCenter" src="{{asset('/storage/images/game_listing/'.$game_listing->image)}}" alt="{{asset('/storage/images/game_listing/'.$game_listing->image)}}" />
                @if($errors->has('image'))
                    <p class="help-block text-danger">
                        {{ $errors->first('image') }}
                    </p>
                @endif
            </div>

            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($game_listing) ? $game_listing->description : '') }}">
                @if($errors->has('description'))
                    <p class="help-block text-danger">
                        {{ $errors->first('description') }}
                    </p>
                @endif
            </div>

                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                    <label for="status">Status</label>
                    @if ($game_listing->status)
                    <select class="form-control" id="status" name="status">
                        <option selected value="1">Active</option>
                        <option value="0">Deactive</option>
                    </select>
                    @endif
                    @if (!$game_listing->status)
                    <select class="form-control" id="status" name="status">
                        <option selected value="0">Deactive</option>
                        <option value="1">Active</option>
                    </select>
                    @endif
                    @if($errors->has('status'))
                    <p class="help-block text-danger">
                        {{ $errors->first('status') }}
                    </p>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('room_code_url') ? 'has-error' : '' }}">
                <label for="room_code_url">Room Code Url</label>
                <input type="text" id="room_code_url" name="room_code_url" class="form-control" value="{{ old('room_code_url', isset($game_listing) ? $game_listing->room_code_url : '') }}">
                @if($errors->has('room_code_url'))
                    <p class="help-block text-danger">
                        {{ $errors->first('room_code_url') }}
                    </p>
                @endif
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
    @include('partials.modal')
</div>
@endsection
