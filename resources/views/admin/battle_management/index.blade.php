@extends('layouts.admin')
@section('content')
@can('user_create')

@endcan
<div class="card">
    <div class="card-header">
        <h4>Battle Management</h4>
        @include('partials.alert')
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="commission_table">
                <thead>
                    <tr>
                        <th>
                            Minimum Amount To Create Battle
                        </th>
                        <th>
                            Maximum Numbers Of Battle
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <td>{{ isset($data[0]['minimum_amount']) ?$data[0]['minimum_amount'] : '' }}</td>
                    <td>{{ isset($data[0]['maximum_battle']) ?$data[0]['maximum_battle'] : '' }}</td>
                    <td><a href="battle_managements/{{$data[0]['id']}}/edit" class="btn btn-sm btn-warning">Edit</a></td>
                </tbody>
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

<!-- <script>
    $(document).ready(function() {

        var table = $('#commission_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.ajax.datatable', ['type'=>'commissions']) !!}",
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'commission_percentage',
                    name: 'commission_percentage'
                },
            ],
        });
    })
</script> -->
@endsection
