<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
    <meta name="author" content="themesflat.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('app/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('app/css/jquery.fancybox.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favico.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/favico.png') }}">
</head>
<body class="body header-fixed">
    <div id="wrapper">
        <div id="pagee" class="clearfix">
            <main id="main">
                <section class="login">
                    <div class="tf-container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-wrap flex">
                                    <div class="image">
                                        <img src="{{ asset('assets/images/page/sign-up.jpg') }}" alt="image">
                                    </div>
                                    <div class="content">
                                        <div class="inner-header-login">
                                            <h3 class="title">Reset Your Password</h3>
                                        </div>
                                        <form action="{{ route('password.update') }}" method="POST" id="reset-password" class="login-user">
                                            @csrf
                                            {{-- Token reset yang dikirim di URL --}}
                                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="input-wrap">
                                                        <label>Email</label>
                                                        <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Enter your email*" required autofocus>
                                                        @error('email')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="input-wrap">
                                                        <label>Password</label>
                                                        <input type="password" name="password" placeholder="Enter new password*" required>
                                                        @error('password')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="input-wrap">
                                                        <label>Confirm Password</label>
                                                        <input type="password" name="password_confirmation" placeholder="Confirm new password*" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 mb-30">
                                                    <button type="submit" class="btn-submit">Reset Password</button>
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
        </div>
    </div>
    <a id="scroll-top" class="button-go"></a>
    <script src="{{ asset('app/js/jquery.min.js') }}"></script>
    <script src="{{ asset('app/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('app/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('app/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('app/js/swiper.js') }}"></script>
    <script src="{{ asset('app/js/plugin.js') }}"></script>
    <script src="{{ asset('app/js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('app/js/shortcodes.js') }}"></script>
    <script src="{{ asset('app/js/main.js') }}"></script>
</body>
</html>