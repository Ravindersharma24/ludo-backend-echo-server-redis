@extends('layouts.admin')
@section('content')
@can('user_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.game_listings.create") }}">
            {{ trans('global.add') }} Games
        </a>
    </div>
</div>

@endcan
<div class="card">
    <div class="card-header">
        @include('partials.alert')
        <h4>Games {{ trans('global.list') }}</h4>
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="gamelisting-table">
                <thead>
                    <tr>
                        <th>
                            Name
                        </th>
                        <th>
                            Image
                        </th>
                        <th>
                            Description
                        </th>
                        <th>
                            Status
                        </th>
                        <!-- <th>
                            &nbsp;
                        </th> -->
                        <th>
                            &nbsp;
                        </th>
                        <!-- <th>
                            &nbsp;
                        </th> -->
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>
@include('partials.modal')
@endsection
@section('scripts')
@parent
<!-- <script>
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
</script> -->

<script>
    $(document).ready(function() {

        var table = $('#gamelisting-table').DataTable({
            processing: true,
            serverSide: true,
            responsive:true,
            aaSorting: [],
            ajax: "{!! route('admin.ajax.datatable', ['type'=>'gamelistings']) !!}",
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'image',
                    name: 'image',
                    render: function (data, type, full, meta) {
                        return "<img onclick=setModal(this) width='60px' height='50px' data-toggle='modal' data-target='#exampleModalCenter' src='{{ env('IMAGE_URL')}}game_listing/"+data+"' alt='"+data+"'/>";
                },
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, full, meta) {
                        if(data){
                            return "<p style='color:blue'>Active</p>";
                        }
                        if(!data){
                            return "<p style='color:grey'>Deactive</p>";
                        }
                    },
                },
                // {
                //     data: 'view',
                //     name: 'view',
                // },
                {
                    data: 'action',
                    name: 'action',
                },
                // {
                //     data: 'action2',
                //     name: 'action2',
                // },
                // {
                //     data: 'id',
                //     name: 'id',
                //     render: function (data, type, full, meta) {
                //         return "<form action='game_listings/destroy/"+data+" method='POST' onsubmit='return confirm('Are you sure ?')' style='display: inline-block;'><input type='submit' class='btn btn-xs btn-danger' value='Delete'><input type='hidden' name='_method' value='DELETE'><input type='hidden' name='_token' value='{{ csrf_token() }}'></form>";
                // },
                // },
            ],
            // createdRow: function ( row, data, index,cell ) {
            //         if (data.status) {
            //             $(cell[3]).css("color", "blue")
            //         }
            //         if (!data.status) {
            //             $(cell[3]).css("color", "grey")
            //         }
            //     }
        });
    })
</script>

@endsection



