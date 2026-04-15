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
                <div class="grid gap-0 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="border-l-4 border-cyan-500 bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Omzet Hari Ini</p>
                        <p class="mt-2 text-3xl font-bold">Rp <?= number_format($omzetHariIni, 0, ',', '.') ?></p>
                        <p class="mt-2 text-sm text-cyan-700"><?= $jumlahTransaksiHariIni ?> transaksi hari ini</p>
                    </article>
                    <article class="border-l-4 border-slate-900 bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Transaksi Hari Ini</p>
                        <p class="mt-2 text-3xl font-bold"><?= $jumlahTransaksiHariIni ?></p>
                        <p class="mt-2 text-sm text-slate-600">Rata-rata Rp
                            <?= number_format($rataRataTransaksi, 0, ',', '.') ?></p>
                    </article>
                    <article class="border-l-4 border-cyan-500 bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Total Produk</p>
                        <p class="mt-2 text-3xl font-bold"><?= $jumlahProduk ?></p>
                        <p class="mt-2 text-sm text-cyan-700">Harga rata-rata Rp
                            <?= number_format($rataRataHargaProduk, 0, ',', '.') ?>
                        </p>
                    </article>
                    <article class="border-l-4 border-slate-900 bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Transaksi Tercatat</p>
                        <p class="mt-2 text-3xl font-bold"><?= count($history) ?></p>
                        <p class="mt-2 text-sm text-slate-600">Total riwayat transaksi</p>
                    </article>
                </div>

                <div class="grid gap-6 xl:grid-cols-3">
                    <article class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold">Transaksi Terbaru</h3>
                            <a href="/simedic/histori-transaksi"
                                class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Lihat semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead class="text-xs uppercase tracking-[0.12em] text-slate-500">
                                    <tr>
                                        <th class="pb-3">ID</th>
                                        <th class="pb-3">Waktu</th>
                                        <th class="pb-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($history, 0, 5) as $row): ?>
                                        <tr class="border-t border-slate-100">
                                            <td class="py-3 font-semibold">#TRX-<?= (int) ($row['id'] ?? 0) ?></td>
                                            <td class="py-3 text-slate-600">
                                                <?= htmlspecialchars((string) ($row['tgl_pembelian'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="py-3 text-slate-700">Rp
                                                <?= number_format((int) ($row['total_harga'] ?? 0), 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg font-bold">Stok Produk</h3>
                            <a href="/simedic/stok-obat"
                                class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Lihat stok</a>
                        </div>
                        <div class="space-y-3">
                            <?php foreach (array_slice($produkDenganStok, 0, 4) as $item): ?>
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                    <p class="font-semibold">
                                        <?= htmlspecialchars((string) ($item['nama'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mt-1 text-sm text-slate-600">Stok: <?= (int) ($item['total_stok'] ?? 0) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </div>

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold">Ringkasan Produk</h3>
                        <a href="/simedic/list-product"
                            class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Lihat produk</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="text-xs uppercase tracking-[0.12em] text-slate-500">
                                <tr>
                                    <th class="pb-3">ID</th>
                                    <th class="pb-3">Nama</th>
                                    <th class="pb-3">Harga</th>
                                    <th class="pb-3">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($produkDenganStok, 0, 5) as $item): ?>
                                    <tr class="border-t border-slate-100">
                                        <td class="py-3 font-semibold">#<?= (int) ($item['id'] ?? 0) ?></td>
                                        <td class="py-3 text-slate-600">
                                            <?= htmlspecialchars((string) ($item['nama'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="py-3 text-slate-700">Rp
                                            <?= number_format((int) ($item['harga'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="py-3 text-slate-700"><?= (int) ($item['total_stok'] ?? 0) ?></td>
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