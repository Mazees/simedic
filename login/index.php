<?php
require '../models/user.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loginError = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

if (isset($_POST['login'])) {
    $userLogin = trim((string) ($_POST['username'] ?? ''));
    $passLogin = trim((string) ($_POST['password'] ?? ''));
    $user->login($userLogin, $passLogin);
}
?>

<!doctype html>
<html lang="id" x-data="loginPage()" class="h-full bg-slate-900">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login SIMEDIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Space Grotesk", "sans-serif"],
                    },
                    colors: {
                        brand: {
                            50: "#ecfeff",
                            100: "#cffafe",
                            500: "#06b6d4",
                            600: "#0891b2",
                            700: "#0e7490",
                            900: "#164e63",
                        },
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-full bg-slate-900 text-white">
    <div class="min-h-full">
        <main class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid w-full gap-0 lg:grid-cols-2">
                <section class="bg-slate-800 border-l-4 border-cyan-500 p-8">
                    <p
                        class="mb-4 inline-flex items-center border-2 border-cyan-500 px-4 py-1 text-xs font-bold uppercase tracking-[0.2em] text-cyan-400">
                        Sistem Manajemen Obat
                    </p>
                    <h1 class="text-4xl font-bold leading-tight md:text-5xl text-white">
                        SIMEDIC
                    </h1>
                    <p class="mt-4 max-w-md text-slate-400">
                        Kendalikan operasional apotek dari dashboard utama hingga
                        transaksi penjualan POS dalam satu sistem yang sederhana.
                    </p>
                </section>

                <section class="bg-white p-8 text-slate-900">
                    <h2 class="text-2xl font-bold">Masuk ke SIMEDIC</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Gunakan akun staf apotek untuk mengakses modul sistem.
                    </p>

                    <?php if ($loginError): ?>
                        <div class="mt-4 border-l-4 border-rose-500 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form class="mt-8 space-y-5" method="post">
                        <div>
                            <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                            <input id="username" name="username" type="username" x-model="form.username" required
                                class="w-full border-2 border-slate-300 bg-white px-4 py-3 outline-none transition focus:border-cyan-500"
                                placeholder="Masukkan username" />
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                            <div class="relative" x-data="{showPassword:false}">
                                <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required
                                    class="w-full border-2 border-slate-300 bg-white px-4 py-3 pr-12 outline-none transition focus:border-cyan-500"
                                    placeholder="Masukkan password" />
                                <button type="button"
                                    class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-slate-500 hover:text-slate-700"
                                    @click="showPassword = !showPassword">
                                    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="login"
                            class="w-full bg-cyan-600 px-4 py-3 font-bold text-white transition hover:bg-cyan-700">
                            Masuk
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>

</html>