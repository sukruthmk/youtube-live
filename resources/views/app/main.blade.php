<html>
    <head>
        <title>Youtube Live</title>
        <link rel="stylesheet" href="../resources/libs/bootstrap/css/bootstrap.min.css">
        <script src="../resources/libs/jquery/jquery.min.js" ></script>
        <script src="../resources/libs/bootstrap/js/bootstrap.min.js" ></script>
        <script src="../resources/libs/highcharts/highcharts.js"></script>
        <script src="../resources/libs/highcharts/exporting.js"></script>
        <script src="../resources/assets/js/livestream.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="#">Youtube Live</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item active">
                <a class="nav-link" href="live-broadcasts">All Broadcasts <span class="sr-only"></span></a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="live-stream">New Broadcast <span class="sr-only">(current)</span></a>
              </li>
            </ul>
            <div class="form-inline my-2 my-lg-0">
              <a class="btn btn-outline-success my-2 my-sm-0" href="log-out">Logout</a>
            </div>
          </div>
        </nav>

        @section('content')
        @show
    </body>
</html>
