<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <title>Video Player</title>
</head>
<body style="background-color: black">

<video width="320" height="240" controls>
    <source src="storage/video/{{$video}}" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  
  @foreach($votes as $key => $data)
  <p class="text-light">Votes: {{$data->votes}}</p>
  <form action="{{url('vote')}}?file={{$video}}" method="post">
    @csrf
   <input type="text" name="vote" placeholder="Vote +1" disabled />
   <input type="submit">
   </form>
   <p class="text-light">ID: {{$data->id}}</p>
   @endforeach
</body>
</html>