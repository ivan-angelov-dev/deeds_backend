@extends('layout.backend')

@section('body')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">User Detail</span> - {{$user->name}}</h4>
            </div>
        </div>
    </div>
    <!-- /Page header -->

    <!-- Content area -->
    <div class="content">

        <div class="mb-3">
            <h6 class="mb-0 font-weight-semibold">
                Offer list
            </h6>
        </div>
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title"></h5>
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
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($offers as $offer)
                    <tr>
                        <td>{{$offer->title}}</td>
                        <td>{{$offer->date}}</td>
                        <td>{{$offer->location_name}}</td>
                        <td>{{$offer->description}}</td>
                        <td>{{$offer->category->name}}</td>
                        <td>
                            ({{$offer->latitude}}, {{$offer->longitude}})
                        </td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="list-icons">
                                    <a href="#" class="list-icons-item" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{url('/offer').'/'.$offer->id}}" class="dropdown-item">Show
                                            details</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-3 pt-2">
            <h6 class="mb-0 font-weight-semibold">
                Photos
            </h6>
        </div>
        @foreach($photos as $photo)
            @if($loop->index % 4 == 0)
                <div class="row">
                    @endif
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-img-actions m-1">
                                <div style="width: 100%;position: relative;padding-top: calc(100% / 4 * 3);">

                                    <div style="position: absolute;
                                        width: 100%;
                                        height: 100%;
                                        top: 0;
                                        background-size: cover;
                                        background-repeat: no-repeat;
                                        background-position: center;
                                        background-image: url('{{asset('/images').'/'.$photo->image_filename}}')">

                                        <img class="card-img img-fluid"
                                             style="display: none;"
                                             src="{{asset('/images').'/'.$photo->image_filename}}"
                                             alt="">
                                        <div class="card-img-actions-overlay card-img">
                                            <a href="{{asset('/images').'/'.$photo->image_filename}}"
                                               class="btn btn-outline bg-white text-white border-white border-2 btn-icon rounded-round"
                                               data-popup="lightbox" rel="group">
                                                <i class="icon-plus3"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($loop->index % 4 == 3)
                </div>
            @endif
        @endforeach
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
