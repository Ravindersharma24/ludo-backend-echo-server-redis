@extends('layouts.admin')
@section('content')
@include('partials.modal')
<div class="card">
    <div class="card-header">
    @include('partials.alert')
        <h4>Activate Mannual Setting</h4>
    </div>

    <div class="card-body">
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="withdrawl_type_table">
                <thead>
                    <tr>
                        <th>
                            Setting Type
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activate_settings as $key => $activate_setting)
                    <tr data-entry-id="{{ $activate_setting->id }}">
                        <td class="text-capitalize text-primary font-weight-bold">
                            {{ $activate_setting->setting_type ?? '' }}
                        </td>

                        <td>
                            <form action="{{ route('admin.activate_mannual_settings.toggle', $activate_setting->id) }}" method="POST" style="display: inline-block;">
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="checkbox" onchange="toggleWithdraw(this)" class="toggleactive" {{$activate_setting->status ? "checked" : ""}} data-toggle="toggle" data-on="Active" data-off="Deactive">
                            </form>
                        </td>

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
        $('#withdrawl_type_table').DataTable({
            // processing: true,
            responsive:true,
            paginate:false,
            bInfo: false,
            bFilter:false,
            aaSorting: [],
        });
    });
</script>
<script>
    function toggleWithdraw(e) {
        if ($(e).parents("form").length > 0) {
            $(e).parents("form")[0].submit()
        }

    }
</script>
@endsection
