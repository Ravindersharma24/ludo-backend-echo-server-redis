@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

<div class="card">
    <div class="card-header">
        <h4>Room History {{ trans('global.list') }}</h4>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="p-0 m-0" style="list-style: none;">
                    <li>{!! implode('', $errors->all(':message')) !!}</li>
                </ul>
            </div>
        @endif
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul class="p-0 m-0" style="list-style: none;">
                <li>{{ session('success') }}</li>
            </ul>
        </div>
        @endif

        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable" id="room_history_table">
                <thead>
                    <tr>
                        <th>
                            Room-Code
                        </th>
                        <th>
                            Game-Name
                        </th>
                        <th>
                            Player-Name
                        </th>
                        <th>
                            Player-Shared-Status
                        </th>
                        <th>
                            Admin_provided-Status
                        </th>
                        <th>
                            Screenshot
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
    $(function() {
        let deleteButtonTrans = '{{ trans('
        global.datatables.delete ') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.users.massDestroy') }}",
            className: 'btn-danger',
            action: function(e, dt, node, config) {
                var ids = $.map(dt.rows({
                    selected: true
                }).nodes(), function(entry) {
                    return $(entry).data('entry-id')
                });

                if (ids.length === 0) {
                    alert('{{ trans('
                        global.datatables.zero_selected ') }}')

                    return
                }

                if (confirm('{{ trans('
                        global.areYouSure ') }}')) {
                    $.ajax({
                            headers: {
                                'x-csrf-token': _token
                            },
                            method: 'POST',
                            url: config.url,
                            data: {
                                ids: ids,
                                _method: 'DELETE'
                            }
                        })
                        .done(function() {
                            location.reload()
                        })
                }
            }
        }
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('user_delete')
        dtButtons.push(deleteButton)
        @endcan

        $('.datatable:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
    })
</script>

<script>
    $(document).ready(function() {
        var table = $('#room_history_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'room_history']) !!}",
            },
            columns: [

                {
                    data: 'room_code',
                    name: 'room_code',
                },
                {
                    data: 'game_name',
                    name: 'game_name',
                },
                {
                    data: 'player_name',
                    name: 'player_name',
                },
                {
                    data: 'player_shared_status',
                    name: 'player_shared_status',
                    render: function (data, type, full, meta) {
                        if(data == 0){
                            return data = 'ongoing';
                        }
                        if(data == 1){
                            return data = 'win';
                        }
                        if(data == 2){
                            return data = 'loss';
                        }
                },
                },
                {
                    data: 'admin_provided_status',
                    name: 'admin_provided_status',
                    render: function (data, type, full, meta) {
                        if(data == 0){
                            return data = 'pending';
                        }
                        if(data == 1){
                            return data = 'win';
                        }
                        if(data == 2){
                            return data = 'loss';
                        }
                        if(data == 3){
                            return data = 'cancel';
                        }
                },
                },
                {
                    data: 'game_screenshot',
                    name: 'game_screenshot',
                },
                {
                    data: 'updateStatus',
                    name: 'updateStatus',
                },
            ],
        });
    })
</script>
@endsection




