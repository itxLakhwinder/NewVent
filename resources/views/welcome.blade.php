 @include('layouts.site')
 <body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @auth
                    <a href="{{ url('/home') }}">Home</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <!-- <a href="{{ route('register') }}">Register</a> -->
                    @endif
                @endauth
                    <a href="{{ url('vent-center') }}">VentCenter</a>
                    <a href="{{ url('terms-policies') }}">Terms & Policies</a>
                    <a href="{{ url('privacy-policy') }}">Privacy Policy</a>
                    <a href="{{ url('posting-guidelines') }}">Posting Guidelines</a>
            </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                <b> VENT </b> SPACE
            </div>
        </div>
    </div>
</body>
</html>
