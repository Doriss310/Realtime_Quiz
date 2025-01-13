<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <link rel="icon" href="/assets/fcc-favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#000000">

        <!--Twitter cards information-->
        <meta name="description" content="This is a developer quiz made by freeCodeCamp">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:site" content="@freeCodeCamp">
        <meta name="twitter:creator" content="@freeCodeCamp">
        <meta property="og:url" content="https://developerquiz.org">
        <meta property="og:title" content="Learn to Code RPG Quiz">
        <meta property="og:description" content="A website with over 1000 questions to practice with.">
        <meta property="og:image" content="https://www.freecodecamp.org/news/content/images/2021/12/Splash-Art.png">
        <meta name="twitter:url" content="https://developerquiz.org">

        <!--Bootstrap links-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!--Bootstrap script-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <!--Font Awesome icons-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">

        <!--Google fonts-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&amp;display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Lato&amp;display=swap" rel="stylesheet">
        <title>freeCodeCamp Developer Quiz</title>
        <link rel="stylesheet" crossorigin="" href="/assets/index-CsXYobPj.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite('resources/js/app.js')
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-[#0a0a23]">

        <!-- Page Heading -->

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>

</html>
