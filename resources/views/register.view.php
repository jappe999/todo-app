@extend('layout/base.view.php')

@section('body')
    <div id="app">
        <div class="center center-top form">
            <h2>Register at {{ $this->domain }}</h2>
            <form action="/register" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="name" id="username">
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                </div>
                <input type="submit" value="Register" class="right">
            </form>
            <a href="/login" class="right margin-top-10">To login page</a>
        </div>
    </div>
@endsection
