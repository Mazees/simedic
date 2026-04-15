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
        header('Location: ' . $_SERVER['PHP_SELF']);
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

<body class="min-h-full bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-bold">Daftar Obat</h3>
                        <input type="text" placeholder="Cari obat..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600 sm:w-72" />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <?php foreach ($items as $no => $item): ?>
                            <article
                                class="rounded-xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:shadow-sm">
                                <p class="font-semibold"><?= $item['nama'] ?></p>
                                <p class="mt-1 text-sm text-slate-500">Stok: <?= $item['total_stok'] ?></p>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="font-bold text-cyan-700">Rp<?= $item['harga'] ?></p>
                                    <form method="post">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="nama" value="<?= $item['nama'] ?>">
                                        <input type="hidden" name="harga" value="<?= $item['harga'] ?>">
                                        <button type="submit" name="add-cart"
                                            class="rounded-lg bg-cyan-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-cyan-700">
                                            Tambah
                                        </button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="rounded-2xl border border-cyan-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold">Keranjang</h3>
                    <?php if ($submitError): ?>
                        <p class="mt-2 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                            Keranjang masih kosong.
                        </p>
                    <?php endif; ?>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($carts as $no => $cart): ?>
                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-3">
                                <p class="font-medium"><?= $cart['nama'] ?></p>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $cart['id'] ?>">
                                            <input type="hidden" name="nama" value="<?= $cart['nama'] ?>">
                                            <input type="hidden" name="harga"
                                                value="<?= $cart['subtotal'] / $cart['jumlah'] ?>">
                                            <button type="submit" name="reduce-cart"
                                                class="rounded border border-cyan-200 bg-white px-2">-</button>
                                        </form>
                                        <span><?= $cart['jumlah'] ?></span>
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $cart['id'] ?>">
                                            <input type="hidden" name="nama" value="<?= $cart['nama'] ?>">
                                            <input type="hidden" name="harga"
                                                value="<?= $cart['subtotal'] / $cart['jumlah'] ?>">
                                            <button type="submit" name="add-cart"
                                                class="rounded border border-cyan-200 bg-white px-2">+</button>
                                        </form>
                                    </div>
                                    <p>Rp<?= $cart['subtotal'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    if (count($carts) <= 0) {
                        echo '<p class="mt-4 text-sm text-slate-500">Belum ada item dipilih.</p>';
                    }
                    ?>

                    <div class="mt-6 space-y-2 border-t border-slate-200 pt-4 text-sm">
                        <div class="flex justify-between text-base font-bold">
                            <span>Total</span>
                            <span>Rp <?= $trs->getTotalHarga() ?></span>
                        </div>
                    </div>

                    <form method="post">
                        <button type="submit" name="submit-transaksi"
                            class="mt-5 w-full rounded-xl bg-cyan-600 px-4 py-3 font-bold text-white transition hover:bg-cyan-700">
                            Submit Transaksi
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </div>
</body>

</html>