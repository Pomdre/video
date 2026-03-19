<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video - People</title>
    <link rel="stylesheet" href="storage/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="background-color: black;">
  <div class="container text-white">
    <div class="my-3">
      <a href="./" class="btn btn-primary">Go Back</a>
      <a href="./sort" class="btn btn-primary">Sort by votes</a>
      <a href="./all" class="btn btn-secondary">All</a>
    </div>

    <h3 class="mb-4">People ({{ count($people) }})</h3>

    @if(count($people) == 0)
      <p class="text-muted">No people detected yet. Run <code>php artisan faces:scan</code> to scan your videos.</p>
    @endif

    <div class="row">
      @foreach($people as $p)
        <div class="col-6 col-md-4 col-lg-3 mb-4">
          <a href="/person?id={{$p->id}}" class="text-decoration-none">
            <div class="card bg-dark border-secondary h-100 text-center">
              <div class="card-body d-flex flex-column align-items-center py-4">
                @if($p->thumbnail)
                  <img src="storage/faces/{{$p->thumbnail}}"
                    style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #555;"
                    class="mb-3">
                @else
                  <div style="width:100px;height:100px;border-radius:50%;background:#333;border:3px solid #555;"
                    class="mb-3 d-flex align-items-center justify-content-center">
                    <span style="font-size:2rem;color:#666;">?</span>
                  </div>
                @endif
                <h5 class="card-title text-white mb-1">{{ $p->name ?? 'Person #'.$p->id }}</h5>
                <p class="text-muted mb-0">{{ $p->video_count }} video{{ $p->video_count != 1 ? 's' : '' }}</p>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</body>
</html>
