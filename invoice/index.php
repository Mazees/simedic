<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/transaction.php';

if (!$user->canAccess('histori-transaksi')) {
    header('Location: /simedic/error?code=403');
    exit;
}

$idTransaksi = (int) ($_GET['id'] ?? 0);
$detailTransaksi = [];
$totalBayar = 0;

if ($idTransaksi > 0) {
    $detailTransaksi = $trs->getTransactionDetails($idTransaksi);
    foreach ($detailTransaksi as $row) {
        $harga = (int) ($row['harga_product'] ?? 0);
        $qty = (int) ($row['qty'] ?? 0);
        $totalBayar += ($harga * $qty);
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Struk Transaksi - SIMEDIC</title>
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
                            900: "#164e63"
                        }
                    }
                }
            }
        };
    </script>
</head>

<body class="min-h-screen bg-slate-100 font-sans text-slate-900">
    <main class="mx-auto max-w-3xl p-4 sm:p-8">
        <div class="mb-4">
            <a href="/simedic/histori-transaksi"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="border-b border-dashed border-slate-300 pb-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Struk Pembelian</p>
                <h1 class="mt-2 text-2xl font-bold">SIMEDIC Pharmacy</h1>
            </div>

            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <p><span class="text-slate-500">ID Transaksi:</span> #TRX-<?= $idTransaksi > 0 ? $idTransaksi : '-' ?>
                </p>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.12em] text-slate-500">
                        <tr>
                            <th class="pb-3">Nama Product</th>
                            <th class="pb-3">Harga</th>
                            <th class="pb-3">Qty</th>
                            <th class="pb-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($detailTransaksi) > 0): ?>
                            <?php foreach ($detailTransaksi as $row): ?>
                                <?php
                                $harga = (int) ($row['harga_product'] ?? 0);
                                $qty = (int) ($row['qty'] ?? 0);
                                $subtotal = $harga * $qty;
                                ?>
                                <tr class="border-t border-slate-100">
                                    <td class="py-3">
                                        <?= htmlspecialchars((string) ($row['nama_product'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="py-3">Rp <?= number_format($harga, 0, ',', '.') ?></td>
                                    <td class="py-3"><?= $qty ?></td>
                                    <td class="py-3 text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="border-t border-slate-100">
                                <td colspan="5" class="py-6 text-center text-slate-500">Detail transaksi tidak ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 border-t border-dashed border-slate-300 pt-4 text-sm">
                <div class="flex items-center justify-between py-2 text-lg font-bold">
                    <span>Total Bayar</span>
                    <span class="text-cyan-700">Rp <?= number_format($totalBayar, 0, ',', '.') ?></span>
                </div>
            </div>
        </section>
    </main>
</body>

</html>