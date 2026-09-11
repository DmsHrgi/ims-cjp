<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
    <title>IMS | Connecti Jelajah Priangan</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo.png') }}?v={{ file_exists(public_path('img/logo.png')) ? filemtime(public_path('img/logo.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <script>window.location.href = "{{ route('login') }}";</script>
</body>
</html>
