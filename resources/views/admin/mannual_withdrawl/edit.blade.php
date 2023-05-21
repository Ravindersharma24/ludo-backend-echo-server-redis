@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header d-flex">
        <a href="{{ route('admin.mannual_withdrawls.index') }}" class="btn btn-primary text-light">Back</a>
        <h4 class="ml-4"> <b style="text-transform:capitalize;">{{ isset($withdraw_request->username) ? $withdraw_request->username : '' }}</b> Withdrawl Request Status</h4>
    </div>
    <div class="card-body">
        <form action="{{ route("admin.mannual_withdrawls.update", [$withdraw_request->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="id" name="id" value="{{ old('withdraw_id', isset($withdraw_request->id) ? $withdraw_request->id : '') }}">
            <input type="hidden" id="id" name="user_id" value="{{ old('user_id', isset($withdraw_request->user_id) ? $withdraw_request->user_id : '') }}">

            <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                <label for="name">Withdrawl-Amount</label>
                <input type="text" id="amount" name="amount" class="form-control w-50" value="{{ old('amount', isset($withdraw_request->amount) ? $withdraw_request->amount : '') }}" readonly>
                @if($errors->has('amount'))
                <p class="help-block">
                    {{ $errors->first('amount') }}
                </p>
                @endif
            </div>
            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                <label for="status">Withdrawl-Request-Status</label>
                @if ($withdraw_request->status == '0')
                <select class="form-control w-50" id="withdraw_request" name="withdraw_status">
                    <option selected value="0">pending</option>
                    <option value="1">success</option>
                    <option value="2">rejected</option>
                </select>
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-lg btn-danger">Update Status</button>
                </div>
                @endif
                @if ($withdraw_request->status == '1')
                <p class="text-success">
                    Successfully Completed*
                </p>
                @endif
                @if ($withdraw_request->status == '2')
                <p class="text-danger">
                    Rejected*
                </p>
                @endif
                @if($errors->has('status'))
                <p class="help-block">
                    {{ $errors->first('status') }}
                </p>
                @endif
            </div>

        </form>
    </div>
</div>

@include('partials.modal')
@endsection

@section('scripts')
@parent
<!-- <script type="text/javascript">
    $(document).ready(function() {
        const pad2 = (n) => {
            return (n < 10 ? '0' : '') + n;
        }
        var today = new Date();
        var month = pad2(today.getMonth() + 1); //months (0-11)
        var day = pad2(today.getDate()); //day (1-31)
        var year = today.getFullYear() - 18;
        const maxDate = `${year}-${month}-${day}`;
        $('#dob').prop('max', maxDate)
    })
</script> -->
@endsection
