@extends('layouts.admin')
@section('content')
@can('user_create')

@endcan
<div class="card">
    <div class="card-header">
        <h4>Commission Management For Battle</h4>
        @include('partials.alert')
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="commission_table">
                <thead>
                    <tr>
                        <th>
                            Condition
                        </th>
                        <th>
                            From-Amount
                        </th>
                        <th>
                            To-Amount
                        </th>
                        <th>
                            Commission-Value
                        </th>
                        <!-- <th>
                            Commission-Type
                        </th> -->
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $commission)
                    <tr>
                        <td style="color:red;font-weight: bold;">
                            @if($commission->condition == '1')
                            Less-Than
                            @elseif($commission->condition == '2')
                            Greater-Than
                            @elseif($commission->condition == '3')
                            Between
                            @else
                            @endif
                        </td>
                        <td>{{$commission->from_amount}}</td>
                        <td>{{$commission->to_amount == 0 ? '-' : $commission->to_amount}}</td>
                        <td>
                            @if($commission->commission_type == '1')
                            {{$commission->commission_value}} %
                            @else
                            Rs. {{$commission->commission_value}}
                            @endif
                        </td>
                        <!-- <td>{{$commission->commission_type}}</td> -->
                        <td><a href="admin_commissions/{{$commission->id}}/edit" class="btn btn-sm btn-warning">Edit</a></td>
                    </tr>
                    @endforeach
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
        $('#commission_table').DataTable({
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
