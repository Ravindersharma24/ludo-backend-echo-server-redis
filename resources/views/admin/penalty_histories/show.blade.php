@extends('layouts.admin')
@section('content')

<div class="card outer-card">
    <div class="card-header" style="display: flex;justify-content: space-between;">
        <div class="left_sec" style="display: flex;align-items:center">
            <a href="{{ route('admin.transactions.penalty_histories') }}" class="btn btn-primary text-light">Back</a>
            <h4 style="text-transform: capitalize" class="ml-4">{{ $user_data->name }} Penalty History</h4>
        </div>
        <!-- <div class="right_sec" style="display: flex;justify-content:flex-end;align-items:center">
            <h5>Current-Refer-Balance {{ $user_data->refer_cash }}</h5>
        </div> -->
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="transactions-table">
                <thead>
                    <tr>
                        <th>
                            Battle-Id
                        </th>
                        <th>
                            Penalty-Amount
                        </th>
                        <th>
                            Penalty-Type
                        </th>
                        <th>
                            Date
                        </th>

                    </tr>
                </thead>
            </table>

        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent


<script>
    $(document).ready(function() {
        var user_id = "{!! ($user_id) !!}";
        var table = $('#transactions-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'user_penalty_transaction']) !!}",
                data: function(data) { data.user_id = user_id}
            },
            columns: [
                {
                    data: 'battle_id',
                    name: 'battle_id',
                },
                {
                    data: 'penalty_amount',
                    name: 'penalty_amount',
                },
                {
                    data: 'penalty_type',
                    name: 'penalty_type',
                    render: function (data, type, full, meta) {
                        if(data == 1){
                            return data = 'Wrong Result';
                        }
                        if(data == 2){
                            return data = 'Pending Game';
                        }
                    },
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ],
        });
    })
</script>

@endsection
