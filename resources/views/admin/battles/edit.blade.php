@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <h4 class="ml-4">{{ trans('global.edit') }} Battle</h5>
    </div>

    <div class="card-body">
        <form action="{{ route("admin.battles.update", [$battle->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="gameId" name="gameId" class="form-control" value="{{$gameId}}">
            <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                <label for="price">Price</label>
                <input type="text" id="price" name="price" class="form-control" value="{{ old('price', isset($battle) ? $battle->price : '') }}">
                @if($errors->has('price'))
                    <p class="help-block">
                        {{ $errors->first('price') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.name_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('entry_fees') ? 'has-error' : '' }}">
                <label for="entry_fees">Entry-Fees</label>
                <input type="text" id="entry_fees" name="entry_fees" class="form-control" value="{{ old('entry_fees', isset($battle) ? $battle->entry_fees : '') }}">
                @if($errors->has('entry_fees'))
                    <p class="help-block">
                        {{ $errors->first('entry_fees') }}
                    </p>
                @endif
                <p class="helper-block">
                    {{ trans('global.user.fields.email_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection
