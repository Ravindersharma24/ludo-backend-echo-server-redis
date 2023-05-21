@extends('layouts.admin')
@section('content')
@can('user_create')
<div style="margin-bottom: 10px;" class="row">
    <!-- <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.rooms.create") }}">
            {{ trans('global.add') }} Room
        </a>
    </div> -->
</div>
@endcan
<div class="card">
    <div class="card-header">
        <h4>Rooms {{ trans('global.list') }}</h4>
    </div>

    <div class="card-body">
        <div class="form-group">
                <label><strong>Status :</strong></label>
                <select id='status' class="form-control" style="width: 200px">
                    <option value="">--Select Status--</option>
                    <!-- <option value="0">Open</option> -->
                    <option value="1">Waiting</option>
                    <!-- <option value="2">Closed</option> -->
                    <option value="3">Conflict</option>
                    <!-- <option value="4">Cancelled</option> -->
                </select>
        </div>
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="rooms_table">
                <thead>
                    <tr>
                        <th>
                           Game
                        </th>
                        <!-- <th>
                            Battle-Id
                        </th> -->
                        <th>
                            Room Code
                        </th>
                        <th>
                            Status
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
        var table = $('#rooms_table').DataTable({
            language: {
                searchPlaceholder: "Search for Game and Room Code"
            },
            initComplete: function () {
                $('.dataTables_filter input[type="search"]').css({ 'width': '250px', 'display': 'inline-block' });
            },// for styling search box
            processing: true,
            serverSide: true,
            responsive: true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'rooms']) !!}",
                data: function (d) {
                        d.status = $('#status').val(),
                        d.search = $('input[type="search"]').val()
                    }
                },
            // ajax: "{!! route('admin.ajax.datatable', ['type'=>'rooms']) !!}",
            columns: [
                {
                    data: 'game_name',
                    name: 'game_name'
                },
                // {
                //     data: 'battle_id',
                //     name: 'battle_id'
                // },
                {
                    data: 'code',
                    name: 'code',
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, full, meta) {
                        // return "<img width='40px' src='/storage/images/game_listing/"+data+"' alt='"+data+"'/>";
                        if(data == 1){
                            return "<p style='color:#0cad52'>waiting</p>";
                        }
                        if(data == 3){
                            return "<p style='color:red'>conflict</p>";
                        }
                },
                },
                {
                    data: 'view',
                    name: 'view'
                },
            ],
            //  createdRow: function ( row, data, index,cell ) {
            //         if (data.status == 2) {
            //             $(cell[2]).css("color", "#0cad52")
            //         }
            //         if (data.status == 3) {
            //             $(cell[2]).css("color", "red")
            //         }
            //     }
        });
        $('#status').change(function(){
        table.draw();
    });
    })
</script>
@endsection
