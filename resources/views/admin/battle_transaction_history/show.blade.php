@extends('layouts.admin')
@section('content')

<div class="card outer-card">
    <div class="card-header" style="display: flex;justify-content: space-between;">
        <div class="left_sec" style="display: flex;align-items:center">
            <a href="{{ route('admin.transactions.battle_transactions') }}" class="btn btn-primary text-light">Back</a>
            <h4 style="text-transform: capitalize" class="ml-4">{{ $user_data->name }} Battle Transaction History</h4>
        </div>
        <div class="right_sec" style="display: flex;justify-content:flex-end;align-items:center">
            <h5>Current-Balance {{ $user_data->balance + $user_data->winning_cash }}</h5>
        </div>
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="transactions-table">
                <thead>
                    <tr>
                        <th>
                            Transaction-Amount
                        </th>
                        <th>
                            Transaction-Type
                        </th>
                        <th>
                            Closing-Balance
                        </th>
                        <th>
                            Game-Name
                        </th>
                        <th>
                            Battle-Id
                        </th>
                        <th>
                            Opposition-Player
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
            responsive:true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'user_battle_transaction']) !!}",
                data: function(data) { data.user_id = user_id}
            },
            columns: [
                {
                    data: 'transaction_amount',
                    name: 'transaction_amount',
                },
                {
                    data: 'transaction_type',
                    name: 'transaction_type',
                    render: function (data, type, full, meta) {
                        if(data == 1){
                            return data = 'Add';
                        }
                        if(data == 2){
                            return data = 'Withdraw';
                        }
                        if(data == 3){
                            return data = 'Win';
                        }
                        if(data == 4){
                            return data = 'Loss';
                        }
                        if(data == 6){
                            return data = 'Penalty Charge';
                        }
                    },
                },
                {
                    data: 'closing_balance',
                    name: 'closing_balance',
                },
                {
                    data: 'game_name',
                    name: 'game_name',
                },
                {
                    data: 'battle_id',
                    name: 'battle_id',
                },
                {
                    data: 'opposition_player',
                    name: 'opposition_player'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ],
            // createdRow: function ( row, data, index,cell ) {
            //         if (data.status == 2) {
            //             $(cell[2]).css("color", "#0cad52")
            //         }
            //         if (data.status == 3) {
            //             $(cell[2]).css("color", "red")
            //         }
            //         if (data.status == 4) {
            //             $(cell[2]).css("color", "blue")
            //         }
            //     }
        });
    })
</script>

@endsection
