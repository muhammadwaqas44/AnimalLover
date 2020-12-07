@extends('layout.dashboard.dashboard')

@section('title','Admin-Dashboard')

@section('content')
    <div class="content-page">

        <!-- Start content -->
        <div class="content">

            <div class="container-fluid">
                <div class="pt-4 mb-4 text-center">
                    <h1>Pets</h1>
                </div>
                <div>

                    <table class="table table-bordered data-table-show-data">
                        <thead>
                        <tr>
                            <th style="width:100px;text-align: center">Species</th>
                            <th style="width:250px;text-align: center">Name</th>
                            <th style="width:250px;text-align: center">Age</th>
                            <th style="width:250px;text-align: center">Breed</th>
                            <th style="width:250px;text-align: center;">Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th></th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td id="des"></td>
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
                ajax: "{{ route('show-user-data', ['userid' => $id, 'option' => 'pets']) }}",
                columns: [
                    {data: 'kind', name: 'kind'},
                    {data: 'name', name: 'name'},
                    {data: 'age', name: 'age'},
                    {data: 'breed', name: 'breed'},
                    {data: 'pets_description', name: 'description'}
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



