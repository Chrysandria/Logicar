<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ asset("images/soflogomodif.png") }}" type="image/x-icon">
    <meta name="description" content="@yield('description', 'LogiCar, la plateforme de gestion des plannings de transport et du suivi de flotte.')">
    @vite("resources/css/app.css")
    @stack("script")
    <title>@yield('title', 'LogiCar')</title>
</head>

<body>
    <div id="app">@yield("content")</div>
</body>
</html>
