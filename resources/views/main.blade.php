<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video</title>
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
      <a href="./people" class="btn btn-outline-warning">People</a>
    </div>
    <div class="row" id="video-grid">
      @foreach($static as $data)
        <div class="col-12 col-md-6 mb-4">
          <img class="freezeframe freezeframe-responsive img-fluid" src="storage/gif/{{ $data->gif }}" />
          <a class="d-block text-right mt-2 text-white" href="view?file={{ $data->basename }}">Vis</a>
        </div>
      @endforeach
    </div>
    <div id="loading" class="text-center my-4" style="display:none;">
      <div class="spinner-border text-light" role="status"></div>
    </div>
  </div>
<script>
(function() {
  var ff = new freezeframe().freeze();
  var loading = false;
  var grid = document.getElementById('video-grid');
  var spinner = document.getElementById('loading');

  function loadMore() {
    if (loading) return;
    loading = true;
    spinner.style.display = 'block';

    fetch('/api/videos', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(videos) {
      videos.forEach(function(v) {
        var col = document.createElement('div');
        col.className = 'col-12 col-md-6 mb-4';
        var img = document.createElement('img');
        img.className = 'freezeframe freezeframe-responsive img-fluid';
        img.src = 'storage/gif/' + v.gif;
        var link = document.createElement('a');
        link.className = 'd-block text-right mt-2 text-white';
        link.href = 'view?file=' + v.basename;
        link.textContent = 'Vis';
        col.appendChild(img);
        col.appendChild(link);
        grid.appendChild(col);
        new freezeframe(img).freeze();
      });
      loading = false;
      spinner.style.display = 'none';
    })
    .catch(function() {
      loading = false;
      spinner.style.display = 'none';
    });
  }

  window.addEventListener('scroll', function() {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 300) {
      loadMore();
    }
  });
})();
</script>
</body>
</html>
