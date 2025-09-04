<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    @stack('styles')
    <title>@yield('title')</title>
</head>
<body>
  @yield('body')
  @stack('scripts')
</body>
</html>
