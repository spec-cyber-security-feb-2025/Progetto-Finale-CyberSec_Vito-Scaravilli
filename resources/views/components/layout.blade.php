<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <title>The Aulab Post</title>
</head>
<body>
    <x-navbar />
    @if (session('message'))
        <div class="alert alert-success my-2">
            {{ session('message') }}
        </div>
    @endif
    @if (session('alert'))
        <div class="alert alert-danger my-2">
            {{ session('alert') }}
        </div>
    @endif
    <div class="min-vh-100">
        {{ $slot }}
    </div>
    <!-- Place the first <script> tag in your HTML's <head> -->
        <script src="https://cdn.tiny.cloud/1/6aujx1sqwo157ot7gbesha78g0qbl2ci3hz6lw4ddt7url4e/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
        <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
            <script>
                tinymce.init({
                    selector: 'textarea',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                });
            </script>
        </body>
        </html>