@extends('layouts.admin')
@section('content')
@can('user_create')


@endcan
<div class="card">
    <div class="card-header">
        <h4>User-Profile {{ trans('global.list') }}</h4>
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
            <table class=" table table-bordered table-striped table-hover datatable" id="user_profile_table">
                <thead>
                    <tr>
                        <th>
                            S.No
                        </th>
                        <th>
                            Username
                        </th>
                        <th>
                            Update Profile
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
        var table = $('#user_profile_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.ajax.datatable', ['type'=>'user_profiles']) !!}",
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

        ],
    });
})
</script>



<!-- <script>
    $(document).ready(function() {

        var table = $('#user_profiles_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('admin.ajax.datatable', ['type'=>'user_profiles']) !!}",
            columns: [
                {
                    data: 'user.name',
                    name: 'user.name'
                },
                {
                    data: 'kyc_upload',
                    name: 'kyc_upload'
                },
                {
                    data: 'kyc_verified',
                    name: 'kyc_verified'
                },
                {
                    data: 'phone_no',
                    name: 'phone_no',
                },
                {
                    data: 'cash_won',
                    name: 'cash_won',
                },
                {
                    data: 'battle_played',
                    name: 'battle_played',
                },
                {
                    data: 'kyc_link',
                    name: 'kyc_link',
                },
            ],
        });
    })
</script> -->

@endsection



