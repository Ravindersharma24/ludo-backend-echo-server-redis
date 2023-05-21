@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

<div class="card">
    <div class="card-header">
        <h4>Kycs {{ trans('global.list') }}</h4>
        @include('partials.alert')
    </div>

    <div class="card-body">
        <div style="display: flex;" class="transaction-filter">
            <div class="form-group">
                             <select id='kyc_status' class="form-control" style="width: 200px">
                                <option value="">--Select Kyc Status--</option>
                                 <option value="0">Pending</option>
                                <option value="1">Verified</option>
                            </select>
                </div>
        </div>
        <div>
            <table class=" table table-bordered table-striped table-hover datatable" id="kycs_upload_table">
                <thead>
                    <tr>
                        <th>
                            Username
                        </th>
                        <th>
                            Document-Type
                        </th>
                        <th>
                            Document-Number
                        </th>
                        <th>
                            First-Name
                        </th>
                        <th>
                            Last-Name
                        </th>
                        <th>
                            DOB
                        </th>
                        <th>
                            State
                        </th>
                        <th>
                            Front-Photo
                        </th>
                        <th>
                            Back-Photo
                        </th>
                        <th>
                            Kyc-Status
                        </th>
                        <th>
                            Date
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
@include('partials.modal')
@endsection
@section('scripts')
@parent



<script>
    $(document).ready(function() {

        var table = $('#kycs_upload_table').DataTable({
            processing: true,
            serverSide: true,
            responsive:true,
            aaSorting: [],
            ajax: {
                url: "{!! route('admin.ajax.datatable', ['type'=>'kycUpload']) !!}",
                data: function (d) {
                        d.status = $('#kyc_status').val(),
                        d.search = $('input[type="search"]').val()
                    }
            },
            // createdRow: function ( row, data, index ) {
            //         console.log("created-row----",$(row).children());
            //         $(row).children(':nth-child(8),:nth-child(9)').addClass('text-center');
            //         if(data['kyc_status'] === '0'){
            //             $(row).children(':nth-child(10)').text('pending');
            //             $(row).children(':nth-child(10)').addClass('text-danger');
            //         }
            //         if(data['kyc_status'] === '1'){
            //             $(row).children(':nth-child(10)').text('verified');
            //             $(row).children(':nth-child(10)').addClass('text-success');
            //         }
            //     },
            columns: [
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'document_type',
                    name: 'document_type',
                },
                {
                    data: 'document_number',
                    name: 'document_number',
                },
                {
                    data: 'first_name',
                    name: 'first_name',
                },
                {
                    data: 'last_name',
                    name: 'last_name',
                },
                {
                    data: 'dob-date',
                    name: 'dob-date',
                },
                {
                    data: 'state',
                    name: 'state',
                },
                {
                    data: 'frontPhoto',
                    name: 'frontPhoto',
                },
                {
                    data: 'backPhoto',
                    name: 'backPhoto',
                },
                {
                    data: 'kyc_status',
                    name: 'kyc_status',
                    render: function (data, type, full, meta) {
                        if(data === '0'){
                            return "<p style='color:red'>pending</p>";
                        }
                        if(data === '1'){
                            return "<p style='color:#0cad52'>verified</p>";
                        }
                    },
                },
                {
                    data: 'date',
                    name: 'date',
                },
                {
                    data: 'editKyc',
                    name: 'editKyc',
                },

            ],
            // createdRow: function ( row, data, index,cell ) {
            //         if (data.kyc_status === '0') {
            //             $(cell[9]).css("color", "red")
            //         }
            //         if (data.kyc_status === '1') {
            //             $(cell[9]).css("color", "#0cad52")
            //         }
            //     },
            "fnInitComplete": function (oSettings, json) {

            }
        });
        $('#kyc_status').change(function(){
            table.draw();
        });

    })
</script>

@endsection
