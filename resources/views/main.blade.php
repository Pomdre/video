<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="storage/css/main.css">
    <link rel="stylesheet" href="https://unpkg.com/freezeframe@3.0.10/build/css/freezeframe_styles.min.css">
    <script type="text/javascript" src="https://unpkg.com/freezeframe@3.0.10/build/js/freezeframe.pkgd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="background-color: black;">
  <div class="container text-white">
    <div class="my-3">
      <a href="./sort" class="btn btn-primary">Sort by votes</a>
      <a href="./all" class="btn btn-secondary">All</a>
    </div>
    <div class="row">
      @foreach($static as $data)
        <div class="col-12 col-md-6 mb-4">
          {{-- <a href="view?file={{$data->basename}}"><img class="img-fluid static" src="storage/static/{{$data->static}}"></a>
          <img class="img-fluid active" src="storage/gif/{{$data->gif}}"> --}}
          <img class="freezeframe freezeframe-responsive img-fluid" src="storage/gif/{{"$data->gif"}}" />
          <a class="d-block text-right mt-2 text-white" href="view?file={{$data->basename}}">Vis</a>
        </div>
      @endforeach
    </div>
    <div class="mt-3">
      <button class="btn btn-warning float-right""><a href="./">Reolad site</a></button>
    </div>
  </div>
</body>
<script>$(function() {
  ff = new freezeframe().freeze();
})
</script>
</body>
</html>