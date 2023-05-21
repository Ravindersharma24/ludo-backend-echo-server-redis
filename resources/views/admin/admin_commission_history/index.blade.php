@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

<div class="card">
<div style="display: flex">
    </div>
    <div class="card-header">
        <h4>Admin Commission Details {{ trans('global.list') }}</h4>
    </div>
    <div class="card-header">
        <h6>Total Commission - <b>{{ $total_commission }}</b></h4>
        @include('partials.alert')
    </div>

    <div class="card-body">
        <!-- <div style="display: flex;" class="transaction-filter">
            <div class="form-group">
                             <select id='transactions' class="form-control" style="width: 200px">
                                <option value="">--Select Transaction--</option>
                                 <option value="1">Add</option>
                                <option value="2">Withdraw</option>
                                <option value="3">Referral Commission</option>
                            </select>
                    </div>
        </div> -->
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="admin-commission-history">
                <thead>
                    <tr>
                        <th>
                            Battle-Id
                        </th>
                        <th>
                            Room-Code
                        </th>
                        <th>
                            Entry-Fees
                        </th>
                        <th>
                            Winning-Price
                        </th>
                        <th>
                            Admin-Commission-Amount
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
        var table = $('#admin-commission-history').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'admin_commission_history']) !!}",
                // data: function (d) {
                //         d.transaction = $('#transactions').val(),
                //         d.search = $('input[type="search"]').val()
                //     }
            },
            columns: [

                {
                    data: 'battle_id',
                    name: 'battle_id',
                //     render: function (data, type, full, meta) {
                //         return "<a class='text-primary' href='wallet_transactions/"+full.user_id+"'>"+data+"</a>";
                // },
                },
                {
                    data: 'room_code',
                    name: 'room_code',
                },
                {
                    data: 'entry_fees',
                    name: 'entry_fees',
                },
                {
                    data: 'price',
                    name: 'price',
                },
                {
                    data: 'admin_commission',
                    name: 'admin_commission',
                },
                // {
                //     data: 'transaction_type',
                //     name: 'transaction_type',
                //     render: function (data, type, full, meta) {
                //         if(data == 1){
                //             return data = 'Add';
                //         }
                //         if(data == 2){
                //             return data = 'Withdraw';
                //         }
                //         if(data == 5){
                //             return data = 'Referral Commission';
                //         }
                //     },
                // },
                // {
                //     data: 'dr_cr',
                //     name: 'dr_cr',
                //     render: function (data, type, full, meta) {
                //         if(data == 0){
                //             return data = '-';
                //         }
                //         if(data == 1){
                //             return data = '+';
                //         }
                //     },
                // },
                // {
                //     data: 'closing_balance',
                //     name: 'closing_balance',
                // },
                // {
                //     data: 'order_id',
                //     name: 'order_id',
                // },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ],
        });
    })
</script>
@endsection
