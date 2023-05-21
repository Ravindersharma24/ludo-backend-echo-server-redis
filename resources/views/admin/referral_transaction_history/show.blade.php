@extends('layouts.admin')
@section('content')

<div class="card outer-card">
    <div class="card-header" style="display: flex;justify-content: space-between;">
        <div class="left_sec" style="display: flex;align-items:center">
            <a href="{{ route('admin.transactions.referral_transactions') }}" class="btn btn-primary text-light">Back</a>
            <h4 style="text-transform: capitalize" class="ml-4">{{ $user_data->name }} Refer Transaction History</h4>
        </div>
        <div class="right_sec" style="display: flex;justify-content:flex-end;align-items:center">
            <h5>Current-Refer-Balance {{ $user_data->refer_cash }}</h5>
        </div>
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="transactions-table">
                <thead>
                    <tr>
                        <th>
                            Referral-Amount
                        </th>
                        <th>
                            Referral-Type
                        </th>
                        <th>
                            Referral-Closing-Balance
                        </th>
                        <th>
                            Order-Id
                        </th>
                        <!-- <th>
                            Date
                        </th> -->

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
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'user_referral_transaction']) !!}",
                data: function(data) { data.user_id = user_id}
            },
            columns: [
                {
                    data: 'referral_amount',
                    name: 'referral_amount',
                },
                {
                    data: 'transaction_type',
                    name: 'transaction_type',
                    render: function (data, type, full, meta) {
                        if(data == 1){
                            return data = 'Earn';
                        }
                        if(data == 2){
                            return data = 'Reedem';
                        }
                    },
                },
                {
                    data: 'referral_closing_balance',
                    name: 'referral_closing_balance',
                },
                {
                    data: 'order_id',
                    name: 'order_id',
                },
                // {
                //     data: 'created_at',
                //     name: 'created_at'
                // },
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
