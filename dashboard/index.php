<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/transaction.php';
if (!$user->canAccess('dashboard')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'dashboard';
$pageTitle = 'Dashboard';
$pageSubtitle = 'Ringkasan performa operasional apotek hari ini.';

$todayStatistic = $trs->getTodayStatistic();
$jumlahTransaksiHariIni = (int) ($todayStatistic['total'] ?? 0);
$omzetHariIni = (int) ($todayStatistic['omzet'] ?? 0);
$rataRataTransaksi = (int) ($todayStatistic['rata_rata'] ?? 0);

$jumlahProduk = $product->getJumlahProduct();
$rataRataHargaProduk = (int) $product->getRataRataHargaProduct();

$produkDenganStok = $product->getProductWithStock();

$history = $trs->getTransactions();
?>
<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - SIMEDIC</title>
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
                    fontFamily: { sans: ["Inter", "sans-serif"] },
                },
            },
        };
    </script>
</head>

<body class="min-h-full bg-slate-50 font-sans text-slate-800">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <section class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Omzet Hari Ini</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800">Rp <?= number_format($omzetHariIni, 0, ',', '.') ?></p>
                        <p class="mt-2 text-sm text-cyan-600"><?= $jumlahTransaksiHariIni ?> transaksi hari ini</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Transaksi Hari Ini</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800"><?= $jumlahTransaksiHariIni ?></p>
                        <p class="mt-2 text-sm text-slate-500">Rata-rata Rp
                            <?= number_format($rataRataTransaksi, 0, ',', '.') ?></p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Produk</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800"><?= $jumlahProduk ?></p>
                        <p class="mt-2 text-sm text-cyan-600">Harga rata-rata Rp
                            <?= number_format($rataRataHargaProduk, 0, ',', '.') ?>
                        </p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Transaksi Tercatat</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800"><?= count($history) ?></p>
                        <p class="mt-2 text-sm text-slate-500">Total riwayat transaksi</p>
                    </article>
                </div>

                <div class="grid gap-6 xl:grid-cols-3">
                    <article class="xl:col-span-2 rounded-lg border border-slate-200 bg-white p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-slate-800">Transaksi Terbaru</h3>
                            <a href="/simedic/histori-transaksi"
                                class="text-sm font-medium text-cyan-600 hover:text-cyan-700">Lihat semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">ID</th>
                                        <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">Waktu</th>
                                        <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($history, 0, 5) as $row): ?>
                                        <tr class="border-t border-slate-100">
                                            <td class="py-3 font-medium text-slate-800">#TRX-<?= (int) ($row['id'] ?? 0) ?></td>
                                            <td class="py-3 text-slate-500">
                                                <?= htmlspecialchars((string) ($row['tgl_pembelian'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="py-3 text-slate-600">Rp
                                                <?= number_format((int) ($row['total_harga'] ?? 0), 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <article class="rounded-lg border border-slate-200 bg-white p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-slate-800">Stok Produk</h3>
                            <a href="/simedic/stok-obat"
                                class="text-sm font-medium text-cyan-600 hover:text-cyan-700">Lihat stok</a>
                        </div>
                        <div class="space-y-3">
                            <?php foreach (array_slice($produkDenganStok, 0, 4) as $item): ?>
                                <div class="rounded-lg border border-slate-100 bg-slate-50 p-3">
                                    <p class="font-medium text-slate-700">
                                        <?= htmlspecialchars((string) ($item['nama'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mt-1 text-sm text-slate-400">Stok: <?= (int) ($item['total_stok'] ?? 0) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </div>

                <article class="rounded-lg border border-slate-200 bg-white p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-800">Ringkasan Produk</h3>
                        <a href="/simedic/list-product"
                            class="text-sm font-medium text-cyan-600 hover:text-cyan-700">Lihat produk</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">ID</th>
                                    <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">Nama</th>
                                    <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">Harga</th>
                                    <th class="pb-3 text-xs font-medium uppercase tracking-wide text-slate-400">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($produkDenganStok, 0, 5) as $item): ?>
                                    <tr class="border-t border-slate-100">
                                        <td class="py-3 font-medium text-slate-800">#<?= (int) ($item['id'] ?? 0) ?></td>
                                        <td class="py-3 text-slate-500">
                                            <?= htmlspecialchars((string) ($item['nama'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="py-3 text-slate-600">Rp
                                            <?= number_format((int) ($item['harga'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="py-3 text-slate-600"><?= (int) ($item['total_stok'] ?? 0) ?></td>
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