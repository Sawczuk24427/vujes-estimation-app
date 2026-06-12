<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estimation Calculator</title>
</head>
<body>
    <div id="app">
        <v-app>
            <v-main class="d-flex align-center justify-center" style="min-height: 100vh;">
                <v-btn color="primary" size="large">
                </v-btn>
            </v-main>
        </v-app>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>