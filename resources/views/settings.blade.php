@extends('layout.backend')

@section('body')

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><span class="font-weight-semibold">Settings</span></h4>
            </div>
        </div>
    </div>
    <!-- /Page header -->

    <!-- Content area -->
    <div class="content">

        <div class="row">
            <div class="col-md-6">

                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title">Twilio</h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                                <a class="list-icons-item" data-action="remove"></a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="#">
                            <div class="form-group">
                                <label>Twilio SID:</label>
                                <input type="text" class="form-control" placeholder="Input Twilio SID." value="{{$twilio[0]['value']}}">
                            </div>

                            <div class="form-group">
                                <label>Twilio Token:</label>
                                <input type="text" class="form-control" placeholder="Input Twilio Token." value="{{$twilio[1]['value']}}">
                            </div>

                            <div class="form-group">
                                <label>Twilio Number:</label>
                                <input type="text" class="form-control" placeholder="Input Twilio Number" value="{{$twilio[2]['value']}}">
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title">Terms and Privacy</h5>
                        <div class="header-elements">
                            <div class="list-icons">
                                <a class="list-icons-item" data-action="collapse"></a>
                                <a class="list-icons-item" data-action="remove"></a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="#">

                            <div class="form-group">
                                <label>Terms:</label>
                                <textarea rows="3" cols="4" class="form-control" placeholder="Enter your message here"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Privacy:</label>
                                <textarea rows="3" cols="3" class="form-control" placeholder="Enter your message here"></textarea>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Save <i class="icon-paperplane ml-2"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /Content area -->


@endsection

@section('js_after')
    <script src="{{asset('global_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
    <script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>

    <script src="{{asset('assets/js/user-list.js')}}"></script>
@endsection
