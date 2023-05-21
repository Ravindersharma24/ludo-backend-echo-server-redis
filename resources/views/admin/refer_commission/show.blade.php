@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <a href="{{ route('admin.kycs.index') }}" class="btn btn-primary text-light">Back</a>
        <h4 class="ml-4">{{ trans('global.kyc.title') }} {{ trans('global.detail') }}</h5>
    </div>

    <div class="card-body">
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                <li>{!! implode('', $errors->all(':message')) !!}</li>
            </ul>
        </div>
    @endif



        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                <li>{{ session('success') }}</li>
            </ul>
        </div>
        @endif

        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>
                        {{ trans('global.kyc.fields.name') }}
                    </th>
                    <td>
                        {{ $user->name }}
                    </td>
                </tr>
                @foreach(config('document') as $index => $document)
                <tr>
                    <th>
                        {{ $document }}
                    </th>
                    <td>
                        @if(count($user->kycs) > 0)
                        @foreach($user->kycs as $id => $kyc)
                        @if($kyc->document_id == $index)
                        <img width="40px" src="{{asset('/storage/images/documents/'.$user->id.'/'.$kyc->path)}}" alt="{{asset('/storage/images/documents/'.$user->id.'/'.$kyc->path)}}" />
                        <form method="post" action="{{route('admin.kycs.delete', [$kyc->id,$user->id])}}">
                            <div class="form-group">
                                @method('delete')
                                @csrf
                                <input type="submit" class="btn btn-xs btn-danger" value="Delete" />
                            </div>
                        </form>
                        @break
                        @else
                        @if(count($user->kycs)-1 === $id )
                        <form method="post" action="{{ route('admin.kycs.store', $user->id) }}" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" name="document" />
                                <input type="hidden" value="{{ $index }}" name="document_id" />
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </form>
                        @break
                        @endif

                        @endif
                        @endforeach
                        @else
                        <form method="post" action="{{ route('admin.kycs.store', $user->id) }}" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" name="document" />
                                <input type="hidden" value="{{ $index }}" name="document_id" />
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </form>
                        @endif

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
