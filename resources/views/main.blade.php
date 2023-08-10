<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="storage/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/freezeframe@3.0.10/build/css/freezeframe_styles.min.css">
    <script type="text/javascript" src="https://unpkg.com/freezeframe@3.0.10/build/js/freezeframe.pkgd.min.js"></script>
  
</head>
<body style="background-color: black">
<div class="container">
  <a href="./sort"><button>Sort by votes</button></a>
  <a href="./all"><button>All</button></a>
  <div class="row">
@foreach($static as $data)
   <div class="col-md-10">
    {{-- <a href="view?file={{$data->basename}}"><img class="img-fluid static" src="storage/static/{{$data->static}}"></a>
    <img class="img-fluid active" src="storage/gif/{{$data->gif}}"> --}}
    <img class="freezeframe freezeframe-responsive" src="storage/gif/{{"$data->gif"}}" />
    <a style="margin-left: 90%" href="view?file={{$data->basename}}">Vis</a>
</div>
@endforeach
<div style="margin-top: 10px;"></div>
  </div>
</div>
<button style="float: right;"><a href="./">Reolad site</a></button>
<script>$(function() {
  ff = new freezeframe().freeze();
})
</script>
</body>
</html>