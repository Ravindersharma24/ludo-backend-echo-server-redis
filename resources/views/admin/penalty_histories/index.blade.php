@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

<div class="card">
    <div class="card-header">
        <h4>All Penalty Transactions {{ trans('global.list') }}</h4>
    </div>

    <div class="card-body">
    <div style="display: flex" class="transaction-filter">
        <div class="form-group">
                    <select id='penalties_type' class="form-control" style="width: 200px">
                        <option value="">--Select Penalty Type--</option>
                        <option value="1">Wrong Result</option>
                        <option value="2">Pending Game</option>
                    </select>
            </div>
    </div>
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="penalty-transactions-table">
                <thead>
                    <tr>
                        <th>
                            Username
                        </th>
                        <th>
                            Mobile-No
                        </th>
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
        var table = $('#penalty-transactions-table').DataTable({
            processing: true,
            serverSide: true,
            responsive:true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'penalty_transaction']) !!}",
                data: function (d) {
                        d.penalties_type = $('#penalties_type').val(),
                        d.search = $('input[type="search"]').val()
                    }
            },
            columns: [

                {
                    data: 'username',
                    name: 'username',
                    render: function (data, type, full, meta) {
                        return "<a class='text-primary' href='penalty_histories/"+full.user_id+"'>"+data+"</a>";
                },
                },
                {
                    data: 'mobile_no',
                    name: 'mobile_no',
                },
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
        $('#penalties_type').change(function(){
            table.draw();
        });
        // $('#debit_credit').change(function(){
        //     table.draw();
        // });
    })
</script>
@endsection
