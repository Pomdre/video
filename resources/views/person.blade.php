<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video - {{ $person->name ?? 'Person #'.$person->id }}</title>
    <link rel="stylesheet" href="storage/css/main.css">
    <link rel="stylesheet" href="https://unpkg.com/freezeframe@3.0.10/build/css/freezeframe_styles.min.css">
    <script type="text/javascript" src="https://unpkg.com/freezeframe@3.0.10/build/js/freezeframe.pkgd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="background-color: black;">
  <div class="container text-white">
    <div class="my-3">
      <a href="./" class="btn btn-primary">Go Back</a>
      <a href="./sort" class="btn btn-primary">Sort by votes</a>
      <a href="./all" class="btn btn-secondary">All</a>
      <a href="./people" class="btn btn-outline-warning">People</a>
    </div>

    {{-- People filter --}}
    @if(isset($people) && count($people) > 0)
    <div class="my-3">
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted">People:</span>
        @foreach($people as $p)
          <a href="/person?id={{$p->id}}" class="btn btn-sm {{ $p->id == $person->id ? 'btn-warning' : 'btn-outline-light' }}">
            @if($p->thumbnail)
              <img src="storage/faces/{{$p->thumbnail}}" style="width:24px;height:24px;border-radius:50%;object-fit:cover;" class="me-1">
            @endif
            {{ $p->name ?? 'Person #'.$p->id }}
          </a>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Rename person --}}
    <div class="my-3 p-3" style="background-color: #222; border-radius: 8px;">
      <div class="d-flex align-items-center gap-3">
        @if($person->thumbnail)
          <img src="storage/faces/{{$person->thumbnail}}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
        @endif
        <div>
          <h4 class="mb-1">{{ $person->name ?? 'Person #'.$person->id }}</h4>
          <p class="text-muted mb-2">{{ count($static) }} video(s)</p>
          <form action="/person/rename" method="post" class="d-flex gap-2">
            @csrf
            <input type="hidden" name="id" value="{{$person->id}}">
            <input type="text" name="name" value="{{ $person->name }}" placeholder="Enter name" class="form-control form-control-sm" style="width:200px;">
            <input type="submit" value="Rename" class="btn btn-sm btn-outline-warning">
          </form>
        </div>
      </div>
    </div>

    <div class="row">
      @foreach($static as $data)
        <div class="col-12 col-md-6 mb-4">
          <img class="freezeframe freezeframe-responsive img-fluid" src="storage/gif/{{"$data->gif"}}" />
          <a class="d-block text-right mt-2 text-white" href="view?file={{$data->basename}}">Vis</a>
        </div>
      @endforeach
    </div>

    @if(count($static) == 0)
      <p class="text-muted">No videos found for this person.</p>
    @endif
  </div>
</body>
<script>$(function() {
  ff = new freezeframe().freeze();
})
</script>
</body>
</html>
