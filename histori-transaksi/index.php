<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/transaction.php';
if (!$user->canAccess('histori-transaksi')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$history = $trs->getTransactions();
$todayStatistic = $trs->getTodayStatistic();
$jumlahTransaksiHariIni = (int) ($todayStatistic['total'] ?? 0);
$omzet = (int) round((float) ($todayStatistic['omzet'] ?? 0));
$rataRata = (int) round((float) ($todayStatistic['rata_rata'] ?? 0));
$activePage = 'histori';
$pageTitle = 'Histori Transaksi';
$pageSubtitle = 'Pantau riwayat transaksi penjualan obat.';

?>
<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Histori Transaksi - SIMEDIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ["Space Grotesk", "sans-serif"] },
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
</head>

<body class="min-h-full bg-slate-100 font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <section class="space-y-6">
                <div class="grid gap-0 sm:grid-cols-3">
                    <article class="bg-cyan-600 p-5 text-white">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-100">Total Transaksi Hari Ini</p>
                        <p class="mt-2 text-3xl font-bold"><?= $jumlahTransaksiHariIni ?></p>
                    </article>
                    <article class="bg-slate-900 p-5 text-white">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Omzet Hari Ini</p>
                        <p class="mt-2 text-3xl font-bold text-cyan-400">Rp <?= $omzet ?></p>
                    </article>
                    <article class="bg-cyan-700 p-5 text-white">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-100">Rata-rata per Transaksi</p>
                        <p class="mt-2 text-3xl font-bold">Rp <?= $rataRata ?></p>
                    </article>
                </div>

                <article class="bg-white p-6 border-t-4 border-slate-900">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-bold">Daftar Histori</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] text-left text-sm">
                            <thead>
                                <tr class="bg-slate-900 text-xs uppercase tracking-[0.12em] text-slate-300">
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Waktu</th>
                                    <th class="px-4 py-3">Total Harga</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $row): ?>
                                    <tr class="border-b-2 border-slate-100 hover:bg-slate-50">
                                        <td class="px-4 py-3 font-bold">#TRX-<?= $row['id'] ?></td>
                                        <td class="px-4 py-3 text-slate-600"><?= $row['tgl_pembelian'] ?></td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">
                                            Rp<?= number_format((int) $row['total_harga'], 0, ',', '.') ?></td>
                                        <td class="px-4 py-3">
                                            <a href="/simedic/invoice?id=<?= (int) $row['id'] ?>"
                                                class="inline-flex items-center bg-cyan-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-cyan-700">
                                                Lihat Struk
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>

</html>