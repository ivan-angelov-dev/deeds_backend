<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Deeds admin panel</title>

    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css"/>
    <!-- /theme JS files -->

</head>

<body>
@yield('body')

<script>
    window.baseUrl = '{{url('/')}}';
    window.assetUrl = '{{asset('/')}}';
</script>

<script src="{{asset('/js/app.js')}}"></script>


@yield('script')
</body>


</html>
