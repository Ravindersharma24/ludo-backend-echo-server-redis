@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

<div class="card">
    <div class="card-header">
        <h4>Mannual Withdrawl Transactions {{ trans('global.list') }}</h4>
        @include('partials.alert')
    </div>
    <div class="card-body">
        <div style="display: flex;" class="withdraw-filter">
            <div class="form-group">
                <select id='status' class="form-control" style="width: 200px">
                    <option value="">--Select Status--</option>
                        <option value="0">Pending</option>
                    <option value="1">Successful</option>
                    <option value="2">Rejected</option>
                </select>
            </div>
        </div>
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="mannual-withdrawl-transactions-table">
                <thead>
                    <tr>
                        <th>
                            Name
                        </th>
                        <th>
                            Mobile-No
                        </th>
                        <th>
                            Withdraw Amount
                        </th>
                        <th>
                            Transfer-Type
                        </th>
                        <th>
                            Upi-Id
                        </th>
                        <th>
                            Bank-Account
                        </th>
                        <th>
                            Ifsc-Code
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            &nbsp;
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
        var table = $('#mannual-withdrawl-transactions-table').DataTable({
            processing: true,
            serverSide: true,
            responsive:true,
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'mannual_withdrawl_transaction']) !!}",
                data: function (d) {
                        d.status = $('#status').val(),
                        d.search = $('input[type="search"]').val()
                    }
            },
            columns: [

                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'phone_no',
                    name: 'phone_no',
                },
                {
                    data: 'amount',
                    name: 'amount',
                },
                {
                    data: 'transfer_type',
                    name: 'transfer_type',
                    render: function (data, type, full, meta) {
                        if(data == 1){
                            return data = 'Upi Transfer';
                        }
                        if(data == 2){
                            return data = 'Bank Transfer';
                        }
                    },
                },
                {
                    data: 'upi_id',
                    name: 'upi_id',
                },
                {
                    data: 'account_number',
                    name: 'account_number',
                },
                {
                    data: 'ifsc_code',
                    name: 'ifsc_code',
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, full, meta) {
                        if(data == 0){
                            return "<p style='color:grey'>pending</p>";
                        }
                        if(data == 1){
                            return "<p style='color:#0cad52'>successful</p>";
                        }
                        if(data == 2){
                            return "<p style='color:red'>rejected</p>";
                        }
                    },
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'editWithdraw',
                    name: 'editWithdraw'
                },
            ],
        });
        $('#status').change(function(){
            table.draw();
        });
    })
</script>
@endsection
