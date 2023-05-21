@extends('layouts.admin')
@section('content')
@can('user_create')

@endcan
<div class="card">
    <div class="card-header">
        <h4>Refer Commission And Limit Management</h4>
        @include('partials.alert')
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="commission_limit_table">
                <thead>
                    <tr>
                        <th>
                           S.No
                        </th>
                        <th>
                            Refer-Commission-Percentage
                        </th>
                        <th>
                            Wallet-Withdraw-Limit
                        </th>
                        <th>
                            Refer_Reeedem-Limit
                        </th>
                        <th>
                            Maximum-Refer-Commission
                        </th>
                        <th>
                            Pending-Game-Penalty-Amount
                        </th>
                        <th>
                            Wrong-Result-Penalty-Amount
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <td>1</td>
                    <td>{{ isset($data[0]['refer_commission_percentage']) ?$data[0]['refer_commission_percentage'] : '' }}</td>
                    <td>{{ isset($data[0]['wallet_withdraw_limit']) ?$data[0]['wallet_withdraw_limit'] : '' }}</td>
                    <td>{{ isset($data[0]['refer_reedem_limit']) ?$data[0]['refer_reedem_limit'] : '' }}</td>
                    <td>{{ isset($data[0]['max_refer_commission']) ?$data[0]['max_refer_commission'] : '' }}</td>
                    <td>{{ isset($data[0]['pending_game_penalty_amt']) ?$data[0]['pending_game_penalty_amt'] : '' }}</td>
                    <td>{{ isset($data[0]['wrong_result_penalty_amt']) ?$data[0]['wrong_result_penalty_amt'] : '' }}</td>
                    <td><a href="commission_limit_managements/{{$data[0]['id']}}/edit" class="btn btn-sm btn-warning">Edit</a></td>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
        $('#commission_limit_table').DataTable({
            // processing: true,
            responsive:true,
            paginate:false,
            bInfo: false,
            bFilter: false,
            aaSorting: [],
        });
    });
</script>
@endsection
