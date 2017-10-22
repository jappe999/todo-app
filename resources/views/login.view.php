@extend('layout/base.view.php')

@section('body')
    <div id="app">
        <div class="center center-top form">
            <h2>Login at {{ $this->domain }}</h2>
            <form action="/login" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="name" id="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                </div>
                <input type="submit" value="Login" class="right">
            </form>
            <a href="/register" class="right margin-top-10">To register page</a>
        </div>
    </div>
@endsection
