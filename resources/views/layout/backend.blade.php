<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Deeds admin panel</title>

    <meta name="description" content="Deeds">
    <meta name="author" content="Sandy">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts and Styles -->
    @yield('css_before')
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">

    <link href="{{asset('global_assets/css/icons/icomoon/styles.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/bootstrap_limitless.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/layout.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/components.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/colors.min.css')}}" rel="stylesheet" type="text/css">

    @yield('css_after')
    <link href="{{asset('assets/css/global.css')}}" rel="stylesheet" type="text/css">
{{--    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css"/>--}}

    <!-- Scripts -->
    <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>

</head>

<body>

    <!-- Main navbar -->
    <div class="navbar navbar-expand-md navbar-dark">
        <div class="navbar-brand">
            <a href="index.html" class="d-inline-block">
                <img src="{{asset('assets/images/logo_light.png')}}" alt="">
            </a>
        </div>

        <div class="d-md-none">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
                <i class="icon-tree5"></i>
            </button>
            <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
                <i class="icon-paragraph-justify3"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbar-mobile">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
                        <i class="icon-paragraph-justify3"></i>
                    </a>
                </li>
            </ul>

            <span class="navbar-text ml-md-3 mr-md-auto">

			</span>

            <ul class="navbar-nav">

                <li class="nav-item dropdown dropdown-user">
                    <a href="#" class="navbar-nav-link dropdown-toggle" data-toggle="dropdown">
                        <span>{{session('admin')->email}}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="javascript:onEditProfile();" class="dropdown-item"><i class="icon-user-plus"></i> My profile</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{url('/logout')}}" class="dropdown-item"><i class="icon-switch2"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!-- /main navbar -->

    <!-- Page content -->
    <div class="page-content">
        <!-- Main sidebar -->
        <div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

            <!-- Sidebar mobile toggler -->
            <div class="sidebar-mobile-toggler text-center">
                <a href="#" class="sidebar-mobile-main-toggle">
                    <i class="icon-arrow-left8"></i>
                </a>
                Navigation
                <a href="#" class="sidebar-mobile-expand">
                    <i class="icon-screen-full"></i>
                    <i class="icon-screen-normal"></i>
                </a>
            </div>
            <!-- /sidebar mobile toggler -->


            <!-- Sidebar content -->
            <div class="sidebar-content">

                <!-- Main navigation -->
                <div class="card card-sidebar-mobile">
                    <ul class="nav nav-sidebar" data-nav-type="accordion">
                        <li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Main</div> <i class="icon-menu" title="Main"></i></li>
                        <li class="nav-item">
                            <a href="{{url('/dashboard')}}" class="nav-link{{request()->is('dashboard*') ? ' active': ''}}">
                                <i class="icon-home4"></i>
                                <span>
									Dashboard
								</span>
                            </a>
                        </li>

                        <li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">User</div> <i class="icon-menu" title="Main"></i></li>
                        <li class="nav-item">
                            <a href="{{url('/user')}}" class="nav-link{{request()->is('user*') ? ' active': ''}}"><i class="icon-users2"></i><span>User</span></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/offer')}}" class="nav-link{{request()->is('offer*') ? ' active': ''}}"><i class="icon-paste"></i><span>Offer</span></a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/category')}}" class="nav-link{{request()->is('category*') ? ' active': ''}}"><i class="icon-price-tag2"></i><span>Category</span></a>
                        </li>

                        <li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Settings</div> <i class="icon-menu" title="Main"></i></li>
                        <li class="nav-item">
                            <a href="{{url('/settings')}}" class="nav-link{{request()->is('settings*') ? ' active': ''}}"><i class="icon-cog3"></i><span>Settings</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- /Main sidebar -->

        <!-- Main content -->
        <div class="content-wrapper">
            @yield('body')

            <!-- Footer -->
                <div class="navbar navbar-expand-lg navbar-light">
                    <div class="text-center d-lg-none w-100">
                        <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
                            <i class="icon-unfold mr-2"></i>
                            Footer
                        </button>
                    </div>

                    <div class="navbar-collapse collapse" id="navbar-footer">
                        <span class="navbar-text">
                            &copy; 2019 <a href="{{url('/')}}">Deeds</a> by <a href="{{url('/')}}" >Sandy</a>
                        </span>
                    </div>
                </div>
                <!-- /footer -->

        </div>
        <!-- /Main content -->

    </div>
    <!-- /Page content -->

    <div id="profile-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <form class="js-validation" >
                        <div class="col-lg-12 col-xl-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{Session::get('admin')->email}}" disabled="disabled">
                            </div>
                            <div class="form-group">
                                <label for="my-password">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="my-current-password" name="my-current-password" placeholder="Current password..">
                            </div>
                            <div class="form-group">
                                <label for="my-password">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="my-password" name="my-new-password" placeholder="Choose a safe one..">
                            </div>
                            <div class="form-group">
                                <label for="my-confirm-password">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="my-confirm-password" name="my-confirm-password" placeholder="..and confirm it!">
                            </div>
                        </div>

                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-primary" id="save-profile">Save changes</button>
                </div>
            </div>
        </div>
    </div>

{{--<script src="{{asset('/js/app.js')}}"></script>--}}

<!-- Core JS files -->
<script src="{{asset('global_assets/js/main/jquery.min.js')}}"></script>
<script src="{{asset('global_assets/js/main/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/loaders/blockui.min.js')}}"></script>

<script src="{{asset('assets/js/app.js')}}"></script>

<script>
    window.baseUrl = '{{url('/')}}';
    window.assetUrl = '{{asset('/')}}';

</script>

@yield('js_after')

</body>


</html>
