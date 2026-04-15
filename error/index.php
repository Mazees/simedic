<?php
$rawCode = trim((string) ($_GET['code'] ?? '404'));
$codeInt = (int) $rawCode;
$code = $codeInt > 0 ? (string) $codeInt : '';
if ($code === '') {
    $code = '404';
}

$errorMap = [
    '400' => ['title' => 'Permintaan Tidak Valid', 'description' => 'Permintaan yang dikirim tidak dapat diproses oleh server.'],
    '401' => ['title' => 'Akses Perlu Login', 'description' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'],
    '403' => ['title' => 'Akses Ditolak', 'description' => 'Kamu tidak memiliki izin untuk membuka halaman ini.'],
    '404' => ['title' => 'Halaman Tidak Ditemukan', 'description' => 'Alamat yang kamu tuju tidak tersedia atau sudah dipindahkan.'],
    '500' => ['title' => 'Terjadi Kesalahan Server', 'description' => 'Ada gangguan di server. Coba lagi beberapa saat.'],
];

if (!isset($errorMap[$code])) {
    $code = '404';
}

$title = $errorMap[$code]['title'];
$description = $errorMap[$code]['description'];
$customMessage = trim((string) ($_GET['message'] ?? ''));
?>
<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Error <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?> - SIMEDIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Space Grotesk', 'sans-serif'] },
                }
            }
        };
    </script>
    <style>
        .e-403, .e-500 { --a: #ef4444; --a-l: #fef2f2; --a-b: #fecaca; }
        .e-401 { --a: #f59e0b; --a-l: #fffbeb; --a-b: #fde68a; }
        .e-404 { --a: #6366f1; --a-l: #eef2ff; --a-b: #c7d2fe; }
        .e-400 { --a: #8b5cf6; --a-l: #f5f3ff; --a-b: #ddd6fe; }
    </style>
</head>

<body
    class="min-h-full bg-slate-900 font-sans text-white e-<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>">

    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg text-center">

            <!-- Icon box -->
            <div class="flex justify-center mb-8">
                <div class="flex h-24 w-24 items-center justify-center border-4"
                    style="border-color:var(--a); background:var(--a-l)">

                    <?php if ($code === '403'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" style="color:var(--a)">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>

                    <?php elseif ($code === '401'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" style="color:var(--a)">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>

                    <?php elseif ($code === '500'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" style="color:var(--a)">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008V17.25m0-3h.008v.008h-.008v-.008Zm0-3h.008v.008h-.008V11.25Z" />
                        </svg>

                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" style="color:var(--a)">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM13.5 10.5h-6" />
                        </svg>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Badge -->
            <span
                class="inline-flex items-center gap-2 px-4 py-1 text-xs font-bold uppercase tracking-widest"
                style="background:var(--a); color:white">
                SIMEDIC &middot; Error <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>
            </span>

            <!-- Heading -->
            <h1 class="mt-4 text-4xl font-bold text-white">
                <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="mt-3 text-base leading-relaxed text-slate-400">
                <?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <!-- Optional detail box -->
            <?php if ($customMessage !== ''): ?>
                <div class="mx-auto mt-5 max-w-sm border-l-4 px-5 py-4 text-left text-sm"
                    style="border-color:var(--a); background:var(--a-l); color:var(--a)">
                    <span class="font-semibold">Detail: </span>
                    <?php echo htmlspecialchars($customMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="mt-9 flex flex-wrap justify-center gap-3">
                <?php if ($code === '401'): ?>
                    <a href="/simedic/login"
                        class="px-6 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                        style="background:var(--a)">
                        Login Sekarang
                    </a>
                <?php else: ?>
                    <a href="/simedic/dashboard"
                        class="px-6 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                        style="background:var(--a)">
                        Ke Dashboard
                    </a>
                <?php endif; ?>

                <button onclick="history.back()"
                    class="border-2 border-slate-600 bg-slate-800 px-6 py-2.5 text-sm font-bold text-slate-300 transition hover:bg-slate-700">
                    &larr; Kembali
                </button>
            </div>

        </div>
    </main>
</body>

</html>