<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <meta charset="utf-8">
    <title>Sign In</title>

    <meta name="author" content="themesflat.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="{{asset ('app/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('app/css/jquery.fancybox.min.css')}}">

    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="{{asset ('assets/images/favico.png')}}">
    <link rel="apple-touch-icon-precomposed" href="{{asset ('assets/images/favico.png')}}">


</head>

<body class="body header-fixed ">
    <div id="wrapper">
        <div id="pagee" class="clearfix">
            <main id="main">
                <section class="login">
                    <div class="tf-container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-wrap flex">
                                    <div class="image">
                                        <img src="./assets/images/page/sign-up.jpg" alt="image">
                                    </div>
                                    <div class="content">
                                        <div class="inner-header-login">
                                            <h3 class="title">Sign Up </h3>
                                        </div>
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        <form action="{{ route('register.store') }}" method="POST" id="sign-up" class="login-user">
                                                @csrf
                                                <div class="input-wrap">
                                                    <label>Name</label>
                                                    <input type="text" name="name" placeholder="Enter your name*" required>
                                                    @error('name')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="input-wrap">
                                                    <label>Username</label>
                                                    <input type="text" name="username" placeholder="Enter your username*" required>
                                                    @error('username')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="input-wrap">
                                                    <label>Email</label>
                                                    <input type="email" name="email" placeholder="Enter your email*" required>
                                                    @error('email')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="input-wrap">
                                                    <label>Your password</label>
                                                    <input type="password" name="password" placeholder="Enter your password*" required>
                                                    @error('password')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                
                                                <div class="input-wrap">
                                                    <label>Confirm Password</label>
                                                    <input type="password" name="password_confirmation" placeholder="Confirm your password*" required>
                                                </div>

                                                <div class="col-lg-12 mb-30">
                                                    <button type="submit" class="btn-submit">Sign Up</button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="flex-three">
                                                            <span class="account">Don,t you have an account?</span>
                                                            <a href="{{ route('login') }}" class="link-login">Login</a>
                                                        </div>
                                                    </div>
                                                </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
            <!-- Bottom -->
        </div>
        <!-- /#page -->
    </div>

    <!-- Modal Popup Bid -->

    <a id="scroll-top" class="button-go"></a>

    <!-- Javascript -->
   <script src="{{ asset ('app/js/jquery.min.js') }}"></script>
    <script src="{{ asset ('app/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset ('app/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset ('app/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset ('app/js/swiper.js') }}"></script>
    <script src="{{ asset ('app/js/plugin.js') }}"></script>
    <script src="{{ asset ('app/js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset ('app/js/shortcodes.js') }}"></script>
    <script src="{{ asset ('app/js/main.js') }}"></script>

</body>

</html>