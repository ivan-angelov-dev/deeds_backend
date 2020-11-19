@extends('layout.backend')

@section('body')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">User</span></h4>
            </div>
        </div>
    </div>
    <!-- /Page header -->

    <!-- Content area -->
    <div class="content">

        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">User list</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>
            </div>


            <table class="table datatable-responsive">
                <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Birthday</th>
                    <th>Gender</th>
                    <th>Location</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <a href="{{asset('/images').'/'.$user->avatar_filename}}" data-popup="lightbox">
                            <div class="user-list-avatar-div">
                                <img src="{{asset('/images').'/'.$user->avatar_filename}}" style="width: 60px;" class="img-preview rounded">
                            </div>
                        </a>
                    </td>
                    <td>
                        <a href="{{url('/user').'/'.$user->id}}">{{$user->name}}</a>
                    </td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->country.$user->mobile}}</td>
                    <td>{{$user->birthday}}</td>
                    <td>
                        @if($user->gender == 'MAN')
                        <span class="badge badge-primary">
                        @elseif($user->gender == 'WOMAN')
                        <span class="badge badge-success">
                        @else
                        <span class="badge badge-info">
                        @endif
                            {{$user->gender}}
                        </span>
                    </td>
                    <td>
                        ({{$user->latitude}}, {{$user->longitude}})
                    </td>
                    <td class="text-center">
                        <div class="list-icons">
                            <div class="list-icons">
                                <a href="#" class="list-icons-item" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{url('/user').'/'.$user->id}}" class="dropdown-item">Show details</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- /Content area -->


@endsection

@section('js_after')
    <script src="{{asset('global_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('global_assets/js/plugins/tables/datatables/extensions/responsive.min.js')}}"></script>
    <script src="{{asset('global_assets/js/plugins/media/fancybox.min.js')}}"></script>
    <script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>

    <script src="{{asset('assets/js/user-list.js')}}"></script>
@endsection
