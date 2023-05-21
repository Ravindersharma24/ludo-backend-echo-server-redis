@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

<div class="card">
    <div class="card-header">
        <h4>All Referral Transactions {{ trans('global.list') }}</h4>
        @include('partials.alert')
    </div>

    <div class="card-body">
    <div style="display: flex" class="transaction-filter">
        <div class="form-group">
                    <select id='transactions' class="form-control" style="width: 200px">
                        <option value="">--Select Transaction--</option>
                        <option value="1">Earn</option>
                        <option value="2">Reedem</option>
                    </select>
            </div>
        <!-- <div class="form-group ml-4">
                    <select id='debit_credit' class="form-control" style="width: 200px">
                        <option value="">--Debit/Credit--</option>
                        <option value="0">Debit</option>
                        <option value="1">Credit</option>
                    </select>
            </div> -->
    </div>
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="referral-transactions-table">
                <thead>
                    <tr>
                        <th>
                            Username
                        </th>
                        <th>
                            Mobile-No
                        </th>
                        <th>
                            Referral-Amount
                        </th>
                        <th>
                            Transaction-Type
                        </th>
                        <!-- <th>
                            Debit/Credit
                        </th> -->
                        <th>
                            Referral-Closing-Balance
                        </th>
                        <th>
                            Order-Id
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
        var table = $('#referral-transactions-table').DataTable({
            processing: true,
            serverSide: true,
            responsive:true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'referral_transaction']) !!}",
                data: function (d) {
                        d.transaction = $('#transactions').val(),
                        d.debit_credit = $('#debit_credit').val(),
                        d.search = $('input[type="search"]').val()
                    }
            },
            columns: [

                {
                    data: 'username',
                    name: 'username',
                    render: function (data, type, full, meta) {
                        return "<a class='text-primary' href='referral_transactions/"+full.user_id+"'>"+data+"</a>";
                },
                },
                {
                    data: 'phone_no',
                    name: 'phone_no',
                },
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
                {
                    data: 'referral_closing_balance',
                    name: 'referral_closing_balance',
                },
                {
                    data: 'order_id',
                    name: 'order_id',
                },
                {
                    data: 'date',
                    name: 'date'
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
        $('#transactions').change(function(){
            table.draw();
        });
        $('#debit_credit').change(function(){
            table.draw();
        });
    })
</script>
@endsection
