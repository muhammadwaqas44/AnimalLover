@extends('layout.dashboard.dashboard')

@section('title','Admin-Dashboard')

@section('content')
    <div class="content-page">

        <!-- Start content -->
        <div class="content">

            <div class="container-fluid">
                <div class="pt-4 mb-4 text-center">
                    <h1>Feedbacks List</h1>
                </div>
                <div>

                    <table class="table table-bordered data-table-show-data-like">
                        <thead>
                        <tr>
                            <th style="width:250px;text-align: center">Full name</th>
                            <th style="width:250px;text-align: center">Feedback</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th></th>
                            <td></td>
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
            var table = $('.data-table-show-data-like').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('feedbacks') }}",
                columns: [
                    {data: 'full_name', name: 'name'},
                    {data: 'feedback', name: 'feedback'},
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



