@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan
<div class="card">
    <div class="card-header">
        <h4>{{ trans('global.kyc.title') }} {{ trans('global.list') }}</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable" id="kycs-table">
                <thead>
                    <tr>
                        <th>
                            {{ trans('global.kyc.fields.s_no') }}
                        </th>
                        <th>
                            {{ trans('global.kyc.fields.username') }}
                        </th>
                        <th>
                            {{ trans('global.kyc.fields.action') }}
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
    function toggleUser(e) {
        if ($(e).parents("form").length > 0) {
            $(e).parents("form")[0].submit()
        }

    }
</script>

<script>
    $(document).ready(function() {
        var table = $('#kycs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.ajax.datatable', ['type'=>'kyc']) !!}",
            columns: [{
                data: 'id',
                name: 'id',
            },
            {
                data: 'name',
                name: 'name',
            },
            {
                data: 'action',
                name: 'action',
            },
        //     {
        //     "defaultContent": "<a class='btn btn-xs btn-primary text-light' href='{{ route('admin.kycs.show',1) }}'>View</a>"
        // },
        ],
        createdRow: function ( row, data, index ) {
            //    console.log("data-",data.id);
            },
    });
})
</script>
@endsection
