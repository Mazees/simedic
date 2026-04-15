<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/transaction.php';

if (!$user->canAccess('pos-obat')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'pos';
$pageTitle = 'POS Obat';
$pageSubtitle = 'Input transaksi penjualan obat dengan cepat.';

$items = $product->getProductWithStock();
$carts = $trs->getCarts();
$submitError = false;

if (isset($_POST['add-cart'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $harga = (int) ($_POST['harga'] ?? 0);
    $trs->addCart($id, $nama, $harga);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['reduce-cart'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $harga = (int) ($_POST['harga'] ?? 0);
    $trs->reduceCart($id, $harga);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['submit-transaksi'])) {
    $insertId = $trs->pushTransaction();
    if ($insertId !== false) {
        $trs->clearCarts();
        header('Location: ../invoice?id=' . $insertId);
        exit();
    }
    $submitError = true;
}

?>
<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS Obat - SIMEDIC</title>
    <link rel="icon" type="image/svg+xml" href="/simedic/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
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

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="lg:col-span-2 rounded-lg border border-slate-200 bg-white p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-slate-800">Daftar Obat Yang Tersedia Saat Ini</h3>

                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <?php foreach ($items as $no => $item): ?>
                            <article
                                class="rounded-lg border border-slate-200 p-4 transition hover:border-cyan-400 hover:bg-cyan-50">
                                <p class="font-medium text-slate-700"><?= $item['nama'] ?></p>
                                <p class="mt-1 text-sm text-slate-400">Stok: <?= $item['total_stok'] ?></p>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="font-semibold text-cyan-600">Rp<?= $item['harga'] ?></p>
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="nama" value="<?= $item['nama'] ?>">
                                        <input type="hidden" name="harga" value="<?= $item['harga'] ?>">
                                        <button type="submit" name="add-cart"
                                            class="rounded-lg bg-cyan-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-cyan-700">
                                            Tambah
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-6">
                    <h3 class="text-base font-semibold text-slate-800">Keranjang</h3>
                    <?php if ($submitError): ?>
                        <p class="mt-2 rounded-lg bg-rose-50 border border-rose-200 px-3 py-2 text-sm text-rose-600">
                            Keranjang masih kosong.
                        </p>
                    <?php endif; ?>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($carts as $no => $cart): ?>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <p class="font-medium text-slate-700"><?= $cart['nama'] ?></p>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $cart['id'] ?>">
                                            <input type="hidden" name="nama" value="<?= $cart['nama'] ?>">
                                            <input type="hidden" name="harga"
                                                value="<?= $cart['subtotal'] / $cart['jumlah'] ?>">
                                            <button type="submit" name="reduce-cart"
                                                class="flex h-7 w-7 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-100">-</button>
                                        </form>
                                        <span class="font-medium text-slate-700"><?= $cart['jumlah'] ?></span>
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $cart['id'] ?>">
                                            <input type="hidden" name="nama" value="<?= $cart['nama'] ?>">
                                            <input type="hidden" name="harga"
                                                value="<?= $cart['subtotal'] / $cart['jumlah'] ?>">
                                            <button type="submit" name="add-cart"
                                                class="flex h-7 w-7 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-100">+</button>
                                        </form>
                                    </div>
                                    <p class="font-medium text-slate-600">Rp<?= $cart['subtotal'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    if (count($carts) <= 0) {
                        echo '<p class="mt-4 text-sm text-slate-400">Belum ada item dipilih.</p>';
                    }
                    ?>

                    <div class="mt-6 space-y-2 border-t border-slate-200 pt-4 text-sm">
                        <div class="flex justify-between text-base font-bold">
                            <span class="text-slate-700">Total</span>
                            <span class="text-cyan-600">Rp <?= $trs->getTotalHarga() ?></span>
                        </div>
                    </div>

                    <form method="post">
                        <button type="submit" name="submit-transaksi"
                            class="mt-5 w-full rounded-lg bg-cyan-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">
                            Submit Transaksi
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>

</html>