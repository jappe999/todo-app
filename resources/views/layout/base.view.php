<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Todo app at {{ $this->domain }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/app.css">
        @yield('head')
    </head>
    <body>
        @yield('body')
        <script src="/js/app.js" charset="utf-8"></script>
    </body>
</html>
