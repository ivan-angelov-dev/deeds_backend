@extends('layout.backend')

@section('body')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">Dashboard</span></h4>
            </div>
        </div>
    </div>
    <!-- /Page header -->

    <!-- Content area -->
    <div class="content">
        <h1>Welcome!</h1>
    </div>
    <!-- /Content area -->


@endsection

@section('js_after')
    <script src="{{asset('assets/js/user-list.js')}}"></script>

@endsection
