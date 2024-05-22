<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Video Player</title>
</head>
<body style="background-color: black; color: white;">

  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 text-center">
        <video class="w-100 mb-3" controls>
          <source src="storage/video/{{$video}}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </div>

    @foreach($votes as $key => $data)
      <div class="row justify-content-center mb-3">
        <div class="col-12 col-md-6">
          <p class="text-light">Votes: {{$data->votes}}</p>
          <form action="{{url('vote')}}?file={{$video}}" method="post" class="d-flex">
            @csrf
            <input type="text" name="vote" placeholder="Vote +1" class="form-control mr-2" disabled>
            <input type="submit" class="btn btn-primary">
          </form>
          <p class="text-light mt-2">ID: {{$data->id}}</p>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>