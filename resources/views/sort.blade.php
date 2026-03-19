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
      <a href="./" class="btn btn-primary">Go Back</a>
      <a href="./all" class="btn btn-secondary">All</a>
      <a href="./people" class="btn btn-outline-warning">People</a>
    </div>
    @if(isset($people) && count($people) > 0)
    <div class="my-3">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted">People:</span>
        @foreach($people as $p)
          <a href="/person?id={{$p->id}}" class="btn btn-sm btn-outline-light">
            @if($p->thumbnail)
              <img src="storage/faces/{{$p->thumbnail}}" style="width:24px;height:24px;border-radius:50%;object-fit:cover;" class="me-1">
            @endif
            {{ $p->name ?? 'Person #'.$p->id }}
          </a>
        @endforeach
      </div>
    </div>
    @endif
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
      <button class="btn btn-warning float-right""><a href="./sort">Reolad site</a></button>
    </div>
  </div>
</body>
<script>$(function() {
  ff = new freezeframe().freeze();
})
</script>
</body>
</html>