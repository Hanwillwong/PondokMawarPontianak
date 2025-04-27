<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="style.css">
    <title>Pondok Mawar | Login</title>
</head>
<body class="text-center">
     <div class="container d-flex justify-content-center align-items-center min-vh-100 col-lg-3">
       <div class=" rounded-5 p-3 bg-white box-area">        
        <div class="row">
            <div class="col-12">
                @if (session()->has('sukses'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('sukses') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if (session()->has('loginError'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('loginError') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div id="loginError" class="alert alert-danger" style="display: none;"></div>
                
            </div>
        </div>
       <div class="col-md-12">
          <div class="row align-items-center">
                <div class="header-text mb-4">
                     <h2>Login</h2>
                </div>
            <form id="loginForm" action="{{ url('/login') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" id="login" name="login" required>
                </div>
                <div class="input-group mb-1">
                    <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" id="password" name="password" required minlength="8">
                </div>
                <div class="input-group mb-5 d-flex justify-content-between">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="formCheck">
                        <label for="formCheck" class="form-check-label text-secondary"><small>Remember Me</small></label>
                    </div>
                    <div class="forgot">
                        <small><a href="#">Forgot Password?</a></small>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">
                        <span id="btn-text">Login</span>
                        <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
                <div class="row">
                    <small>Don't have account? <a href="{{ url('/register') }}">Register</a></small>
                </div>
          </div>
       </div> 

      </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+AMvyTG2qkz04t+PAMvyTG2qkz04t+" crossorigin="anonymous"></script>
   
</html>
<script>
    document.getElementById("loginForm").addEventListener("submit", function() {
        document.getElementById("btn-text").classList.add("d-none");
        document.getElementById("btn-spinner").classList.remove("d-none");
    });
</script>
