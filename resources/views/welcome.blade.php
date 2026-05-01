<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexGen | Auth</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .neon-glow {
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
        }

        .bg-grid {
            background-image: radial-gradient(circle, #2d3748 1px, transparent 1px);
            background-size: 30px 30px;
        }
    </style>
</head>

<body class="bg-gray-950 text-white min-h-screen flex flex-col items-center justify-center font-sans bg-grid">

    <div class="absolute inset-0 bg-gradient-to-b from-blue-950/20 to-transparent pointer-events-none"></div>

    <div class="relative z-10 text-center space-y-8 p-8">
        <h1
            class="text-6xl font-black tracking-tighter neon-glow bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300">
            NEXGEN ACCESS
        </h1>
        <p class="text-gray-400 max-w-md mx-auto text-lg">
            Secure, decentralized authentication platform. Enter the next era of digital identity.
        </p>

        <div class="flex gap-4 justify-center">
            <a href="{{ route('login') }}"
                class="px-8 py-3 bg-blue-600 hover:bg-blue-500 rounded-lg font-bold transition-all shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                LOGIN
            </a>
            <a href="{{ route('register') }}"
                class="px-8 py-3 bg-gray-800 hover:bg-gray-700 rounded-lg font-bold border border-gray-700 transition-all">
                REGISTER
            </a>
        </div>
    </div>

    <footer class="absolute bottom-8 text-gray-600 text-sm tracking-widest uppercase">
        Vercel Deploy v1.0.0
    </footer>

</body>

</html>
