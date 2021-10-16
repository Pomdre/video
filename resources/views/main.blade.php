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
</head>
<body>
<div class="container">
  <div class="row">
@foreach($static as $data)
   <div class="col-md-10">
    <a href="view?file={{$data->basename}}"><img class="img-fluid static" src="storage/static/{{$data->static}}"></a>
    <img class="img-fluid active" src="storage/gif/{{$data->gif}}">
</div>
@endforeach
<div style="margin-top: 10px;"></div>
  </div>
</div>
</body>
</html>