<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title data-inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script>
            (function () {
                const theme = localStorage.getItem('theme');

                if (
                    theme === 'dark' ||
                    (!theme &&
                        window.matchMedia('(prefers-color-scheme: dark)').matches)
                ) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        <!--
        THESIS: Hellen Suite is a property directory that opens into a focused hotel workspace, never a generic card dashboard.
        OWN-WORLD: Porcelain and graphite surfaces, fine directory rails, compact geometry, and fixed cyan for location and primary action; amber appears only when attention is required.
        STORY: The operator finds a hotel, opens it, understands which property is active, and moves through that hotel's modules without losing context.
        FIRST VIEWPORT: A compact utility bar leads into a fluid heading and a full-width linear hotel directory; the create action remains visible and every row ends in one clear open action.
        FORM: Contemporary reception directory, chosen from grounded direction 7; seed 4d791dc3.
        FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance
        -->
        @inertia
    </body>
</html>
