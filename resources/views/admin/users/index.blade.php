@extends('layouts.admin')
@section('content')
@can('user_create')
<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-success" href="{{ route("admin.users.create") }}">
            {{ trans('global.add') }} {{ trans('global.user.title_singular') }}
        </a>
    </div>
</div>
@endcan
@include('partials.modal')
<div class="card">
    <div class="card-header">
        @include('partials.alert')
        <h4>{{ trans('global.user.title_singular') }} {{ trans('global.list') }}</h4>
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="users_table">
                <thead>
                    <tr>
                        <th>
                            {{ trans('global.user.fields.name') }}
                        </th>
                        <th>
                            Phone No
                        </th>
                        <!-- <th>
                            {{ trans('global.user.fields.email') }}
                        </th> -->
                        <th>
                            {{ trans('global.user.fields.wallet_balance') }}
                        </th>
                        <th>
                            Refferal Ballance
                        </th>
                        <th>
                            {{ trans('global.user.fields.affilate_id') }}
                        </th>
                        <th>
                            User-Image
                        </th>
                        <!-- <th>
                            {{ trans('global.user.fields.roles') }}
                        </th> -->
                        <th>
                            &nbsp;
                        </th>
                        <th>
                            {{ trans('global.user.fields.active') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr data-entry-id="{{ $user->id }}">
                        <td class="text-capitalize">
                            {{ $user->name ?? '' }}
                        </td>
                        <td>
                            {{ $user->phone_no ?? '' }}
                        </td>
                        <td>
                            {{ $user->balance + $user->winning_cash}}
                        </td>
                        <td>
                            {{ $user->refer_cash ?? '' }}
                        </td>
                        <td>
                            <!-- <a href="{{url('/').'/?ref='.$user->affiliate_id}}">{{$user->affiliate_id ?? '' }}</a> -->
                            {{$user->affiliate_id ?? '' }}
                        </td>
                        <td>
                        <img onclick=setModal(this) width="40px" height="40px" style="border-radius: 50%" data-toggle="modal" data-target="#exampleModalCenter" src="{{ env('IMAGE_URL')}}user-profiles/{{$user->user_image}}" alt="" class="imgResize"/>                        </td>
                        <!-- <td>
                            @foreach($user->roles as $key => $item)
                            <span class="badge badge-info">{{ $item->title }}</span>
                            @endforeach
                        </td> -->
                        <td>
                            @can('user_show')
                            <!-- <a class="btn btn-xs btn-primary" href="{{ route('admin.users.show', $user->id) }}">
                                {{ trans('global.view') }}
                            </a> -->
                            @endcan
                            @can('user_edit')
                            <a class="btn btn-info" href="{{ route('admin.users.edit', $user->id) }}">
                                {{ trans('global.edit') }}
                            </a>
                            @endcan

                            <!-- @can('user_delete')
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                            </form>
                            @endcan -->
                        </td>

                        <td>
                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" style="display: inline-block;">
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="checkbox" onchange="toggleUser(this)" class="toggleactive" {{$user->active ? "checked" : ""}} data-toggle="toggle" data-on="Active" data-off="Deactive">
                            </form>
                        </td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
</div>
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
    $(function () {
        $('#users_table').DataTable({
            // processing: true,
            responsive:true,
            paginate:false,
            bInfo: false,
            aaSorting: [],
        });
    });
</script>
<script>
    function toggleUser(e) {
        if ($(e).parents("form").length > 0) {
            $(e).parents("form")[0].submit()
        }

    }
</script>
@endsection
