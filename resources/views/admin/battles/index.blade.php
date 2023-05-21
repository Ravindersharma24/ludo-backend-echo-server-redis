@extends('layouts.admin')
@section('content')
@can('user_create')
<div style="margin-bottom: 10px;" class="row">
    <!-- <div class="col-lg-12">
        <a class="btn btn-success" href="{{route('admin.battles.create.game', ['gameId' => $gameId])}}">
            {{ trans('global.add') }} Battles
        </a>
    </div> -->
</div>

@endcan
<div class="card">
    <div class="card-header">
        <h4 style="text-transform:capitalize"> {{ isset($game_name->name) ? $game_name->name : '' }} Battles {{ trans('global.list') }}</h4>
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
            <table class=" table table-bordered table-striped table-hover datatable" id="battles_table">
                <thead>
                    <tr>
                        <!-- <th>
                            Game Name
                        </th> -->
                        <th>
                            Price
                        </th>
                        <th>
                            Entry-Fees
                        </th>
                        <!-- <th>
                            Live-Player
                        </th> -->
                        <th>
                            &nbsp;
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
    function toggleUser(e) {
        if ($(e).parents("form").length > 0) {
            $(e).parents("form")[0].submit()
        }

    }
</script>
<script>
    $(document).ready(function() {
        var gameId = "{{$gameId}}";
        var table = $('#battles_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url :"{!! route('admin.ajax.datatable', ['type'=>'battles']) !!}",
                data : function(data){data.gameId = gameId}
            },
            columns: [
                // {
                //     data: 'game_listing_id',
                //     name: 'game_listing_id'
                // },
                // {
                //     data: 'gamelisting.name',
                //     name: 'gamelisting.name'
                // },
                {
                    data: 'price',
                    name: 'price',
                },
                {
                    data: 'entry_fees',
                    name: 'entry_fees'
                },
                // {
                //     data: 'live_player',
                //     name: 'live_player'
                // },
                {
                    data: 'edit',
                    name: 'edit'
                },
                {
                    data: 'delete',
                    name: 'delete'
                },
            ],
        });
    })
</script>
@endsection
