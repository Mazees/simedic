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
<html lang="id" x-data="loginPage()" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login SIMEDIC</title>
    <link rel="icon" type="image/svg+xml" href="/simedic/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-full bg-slate-50 font-sans text-slate-800">
    <div class="min-h-full">
        <main class="mx-auto flex min-h-screen max-w-5xl items-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm lg:grid-cols-2">
                <section class="bg-cyan-600 p-8 lg:p-10">
                    <h1 class="text-4xl font-bold leading-tight text-white md:text-5xl">
                        SIMEDIC
                    </h1>
                    <p class="mt-4 max-w-md text-cyan-100">
                        Kendalikan operasional apotek dari dashboard utama hingga
                        transaksi penjualan POS dalam satu sistem yang sederhana.
                    </p>
                </section>

                <section class="p-8 lg:p-10">
                    <h2 class="text-2xl font-bold text-slate-800">Masuk ke SIMEDIC</h2>
                    <p class="mt-2 text-sm text-slate-400">
                        Gunakan akun staf apotek untuk mengakses modul sistem.
                    </p>

                    <?php if ($loginError): ?>
                        <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-600">
                            <?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>

                    <form class="mt-8 space-y-5" method="post">
                        <div>
                            <label for="username" class="mb-2 block text-sm font-medium text-slate-600">Username</label>
                            <input id="username" name="username" type="text" x-model="form.username" required
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                                placeholder="Masukkan username" />
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-slate-600">Password</label>
                            <div class="relative" x-data="{showPassword:false}">
                                <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                                    placeholder="Masukkan password" />
                                <button type="button"
                                    class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-400 hover:text-slate-600"
                                    @click="showPassword = !showPassword">
                                    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="login"
                            class="w-full rounded-lg bg-cyan-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">
                            Masuk
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>

</html>