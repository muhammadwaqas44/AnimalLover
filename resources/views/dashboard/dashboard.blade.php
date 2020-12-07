@extends('layout.dashboard.dashboard')

@section('title','Admin-Dashboard')

@section('content')
    <div class="content-page">

        <!-- Start content -->
        <div class="content">

            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="breadcrumb-holder">
                            <h1 class="main-title float-left">Dashboard</h1>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item">Home</li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
                        <div class="card-box noradius noborder bg-default">
                            <i class="far fa-building float-right text-white"></i>
                            <h6 class="text-white text-uppercase m-b-20">Companies</h6>
                            <h1 class="m-b-20 text-white counter">1,587</h1>
                            <span class="text-white"></span>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
                        <div class="card-box noradius noborder bg-warning">
                            <i class="far fa-calendar-alt float-right text-white"></i>
                            <h6 class="text-white text-uppercase m-b-20">Scheduling</h6>
                            <h1 class="m-b-20 text-white counter">250</h1>
                            <!-- <span class="text-white">Bounce rate: 25%</span> -->
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6 col-lg-6 col-xl-4">
                        <div class="card-box noradius noborder bg-info">
                            <i class="fas fa-users float-right text-white"></i>
                            <h6 class="text-white text-uppercase m-b-20">Employees</h6>
                            <h1 class="m-b-20 text-white counter">120</h1>
                            <!-- <span class="text-white">25 New Employees</span> -->
                        </div>
                    </div>


                </div>

                <div>

                    <table class="table table-bordered data-table-user">
                        <thead>
                        <tr>
                            <th style="width:100px">Full Name</th>
                            <th style="width:250px">E-mail</th>
                            <th style="width:250px">Phone</th>
                            <th style="width:250px">Role</th>
                            <th style="width:100px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th></th>
                            <td></td>
                            <td></td>
                            <td></td>
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
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('.data-table-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin-dashboard') }}",
                columns: [
// {data: 'id', name: 'id'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'role.name', name: 'role'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection



