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

$colorMap = [
    '400' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'border' => 'border-violet-200', 'btn' => 'bg-violet-600 hover:bg-violet-700', 'icon' => 'text-violet-500'],
    '401' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200', 'btn' => 'bg-amber-500 hover:bg-amber-600', 'icon' => 'text-amber-500'],
    '403' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'btn' => 'bg-rose-500 hover:bg-rose-600', 'icon' => 'text-rose-500'],
    '404' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-200', 'btn' => 'bg-indigo-600 hover:bg-indigo-700', 'icon' => 'text-indigo-500'],
    '500' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'btn' => 'bg-rose-500 hover:bg-rose-600', 'icon' => 'text-rose-500'],
];
$c = $colorMap[$code];
?>
<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Error <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?> - SIMEDIC</title>
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
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        };
    </script>
</head>

<body class="min-h-full bg-slate-50 font-sans text-slate-800">

    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg text-center">

            <!-- Icon box -->
            <div class="flex justify-center mb-8">
                <div class="flex h-20 w-20 items-center justify-center rounded-xl <?= $c['bg'] ?>">

                    <?php if ($code === '403'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 <?= $c['icon'] ?>" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>

                    <?php elseif ($code === '401'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 <?= $c['icon'] ?>" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>

                    <?php elseif ($code === '500'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 <?= $c['icon'] ?>" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008V17.25m0-3h.008v.008h-.008v-.008Zm0-3h.008v.008h-.008V11.25Z" />
                        </svg>

                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 <?= $c['icon'] ?>" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM13.5 10.5h-6" />
                        </svg>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Badge -->
            <span
                class="inline-flex items-center gap-2 rounded-md px-3 py-1 text-xs font-medium <?= $c['bg'] ?> <?= $c['text'] ?>">
                SIMEDIC &middot; Error <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>
            </span>

            <!-- Heading -->
            <h1 class="mt-4 text-3xl font-bold text-slate-800">
                <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="mt-3 text-base leading-relaxed text-slate-400">
                <?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <!-- Optional detail box -->
            <?php if ($customMessage !== ''): ?>
                <div class="mx-auto mt-5 max-w-sm rounded-lg border px-5 py-4 text-left text-sm <?= $c['bg'] ?> <?= $c['border'] ?> <?= $c['text'] ?>">
                    <span class="font-semibold">Detail: </span>
                    <?php echo htmlspecialchars($customMessage, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="mt-9 flex flex-wrap justify-center gap-3">
                <?php if ($code === '401'): ?>
                    <a href="/simedic/login"
                        class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition <?= $c['btn'] ?>">
                        Login Sekarang
                    </a>
                <?php else: ?>
                    <a href="/simedic/dashboard"
                        class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition <?= $c['btn'] ?>">
                        Ke Dashboard
                    </a>
                <?php endif; ?>

                <button onclick="history.back()"
                    class="rounded-lg border border-slate-300 bg-white px-6 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                    &larr; Kembali
                </button>
            </div>

        </div>
    </main>
</body>

</html>