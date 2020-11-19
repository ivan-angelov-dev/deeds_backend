@extends('layout.base')

@section('body')

    <!-- Main navbar -->
    <div class="navbar navbar-expand-md navbar-dark">
        <div class="navbar-brand">
            <a href="index.html" class="d-inline-block">
                <img src="{{asset('assets/images/logo_light.png')}}" alt="">
            </a>
        </div>
    </div>
    <!-- /main navbar -->


    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content d-flex justify-content-center align-items-center">

                <!-- Login form -->
                <form action="{{url('/auth/login')}}" method="post" class="login-form">
                    <div class="card mb-0">
                        <div class="card-body">
                            <h3 class="text-center">Deeds Admin Panel</h3>
                            <div class="text-center mb-3">
                                <i class="icon-reading icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
                                <h5 class="mb-0">Login to your account</h5>
                                <span class="d-block text-muted">Enter your credentials below</span>
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                <input type="email"
                                       class="form-control"
                                       name="email"
                                       placeholder="Email">
                                <div class="form-control-feedback">
                                    <i class="icon-user text-muted"></i>
                                </div>
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-left">
                                <input type="password"
                                       class="form-control"
                                       placeholder="Password"
                                       name="password">
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                            </div>

                            @if (session()->has('error_msg'))
                                <div class="form-group">
                                    <span class="form-text text-danger">{{ session()->get('error_msg') }}</span>
                                </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Sign in <i
                                            class="icon-circle-right2 ml-2"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /login form -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->


@endsection

@section('script')

    <script>
        $(() => {

            window.page = new App.pages.LoginPage()

        })
    </script>

@endsection
