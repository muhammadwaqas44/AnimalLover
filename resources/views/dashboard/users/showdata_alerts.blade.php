@extends('layout.dashboard.dashboard')

@section('title','Admin-Dashboard')

@section('content')
    <div class="content-page">

        <!-- Start content -->
        <div class="content">

            <div class="container-fluid">
                <div class="pt-4 mb-4 text-center">
                    <h1>Alerts</h1>
                </div>

                <div>

                    <table class="table table-bordered data-table-show-data">
                        <thead>
                        <tr>
                            <th style="width:100px;text-align: center">Title</th>
                            <th style="width:100px;text-align: center">Type</th>
                            <th style="width:100px;text-align: center">Description</th>
                            <th style="width:100px;text-align: center">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <!-- end row -->
            </div>
            <!-- END container-fluid -->

        </div>
        <!-- END content -->

    </div>
@endsection


@section('script')

    <script>
        $(document).ready(function () {
            var table = $('.data-table-show-data').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('show-user-data', ['userid' => $id, 'option' => 'alerts']) }}",
                columns: [
                    {data: 'title', name: 'title'},
                    {data: 'type', name: 'type'},
                    {data: 'description', name: 'description'},
                    {data: 'status', name: 'status'},
                ]
            });
        });
    </script>
    <style>
        table.dataTable tbody tr td{
            text-align: center;
        }
        table.dataTable tbody tr td:nth-child(5){
            overflow: hidden !important;width: 250px !important;display: inline-block !important;
        }
    </style>
@endsection



