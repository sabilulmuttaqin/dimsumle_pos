<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="p-6">
        <h1 class="text-4xl font-bold text-blue-600">
            Tailwind jalan
        </h1>

        <div x-data="{ open: false }" class="mt-6">
            <button @click="open = !open" class="rounded-lg bg-black px-4 py-2 text-white">
                Toggle Alpine
            </button>

            <p x-show="open" class="mt-4 text-green-600 font-semibold">
                Alpine jalan
            </p>
        </div>
    </div>
</body>

</html>
