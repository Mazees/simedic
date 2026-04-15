<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/product.php';
require_once __DIR__ . '/../models/stok.php';

if (!$user->canAccess('stok-obat')) {
    header('Location: /simedic/error?code=403');
    exit;
}

$productOptions = $product->getAllProduct();

if (isset($_POST['add'])) {
    $idProduct = (int) ($_POST['id_product'] ?? 0);
    $kodeBatch = trim((string) ($_POST['batch'] ?? ''));
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $tglExp = trim((string) ($_POST['tgl_exp'] ?? ''));

    if ($idProduct > 0 && $kodeBatch !== '' && $jumlah >= 0 && $tglExp !== '') {
        $stok->addStok($idProduct, $kodeBatch, $jumlah, $tglExp);
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['increase'])) {
    $id = (int) ($_POST['stok_id'] ?? 0);
    if ($id > 0) {
        $stok->increaseStok($id);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['decrease'])) {
    $id = (int) ($_POST['stok_id'] ?? 0);
    if ($id > 0) {
        $stok->decreaseStok($id);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['delete'])) {
    $id = (int) ($_POST['stok_id'] ?? 0);
    if ($id > 0) {
        $stok->deleteStok($id);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$listStok = $stok->getAllStok();
$totalBatch = count($listStok);
$lowStockCount = 0;
$totalUnits = 0;

foreach ($listStok as $stokItem) {
    $jumlah = (int) $stokItem['jumlah'];
    $totalUnits += $jumlah;
    if ($jumlah <= 20) {
        $lowStockCount++;
    }
}

$activePage = 'stok';
$pageTitle = 'Manajemen Stok Obat';
$pageSubtitle = 'Kelola item obat, stok masuk/keluar, dan batch obat.';
?>
<!doctype html>
<html lang="id" x-data="stokPage()" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Stok Obat - SIMEDIC</title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-full bg-slate-50 font-sans text-slate-800">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="lg:col-span-2 space-y-6">
                    <article class="rounded-lg border border-slate-200 bg-white p-6">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-base font-semibold text-slate-800">Data Stok Obat</h2>
                            <input x-model="query" type="text" placeholder="Cari nama obat atau kode batch..."
                                class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 sm:w-80" />
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-lg bg-cyan-600 p-4 text-white">
                                <p class="text-xs font-medium uppercase tracking-wide text-cyan-100">Total Batch</p>
                                <p class="mt-1 text-2xl font-bold">
                                    <?= $totalBatch ?>
                                </p>
                            </div>
                            <div class="rounded-lg bg-slate-800 p-4 text-white">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Stok Rendah</p>
                                <p class="mt-1 text-2xl font-bold text-cyan-400">
                                    <?= $lowStockCount ?>
                                </p>
                            </div>
                            <div class="rounded-lg bg-cyan-700 p-4 text-white">
                                <p class="text-xs font-medium uppercase tracking-wide text-cyan-100">Total Unit</p>
                                <p class="mt-1 text-2xl font-bold">
                                    <?= $totalUnits ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[900px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Obat</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Batch</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Stok</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Harga</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Tgl Masuk</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Tgl Exp</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listStok as $stokItem): ?>
                                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                                            <td class="px-4 py-3 font-medium text-slate-700">
                                                <?= htmlspecialchars($stokItem['nama'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">
                                                <?= htmlspecialchars($stokItem['batch'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="rounded-md px-2.5 py-1 text-xs font-medium <?= ((int) $stokItem['jumlah'] <= 20) ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-600' ?>">
                                                    <?= (int) $stokItem['jumlah'] ?> pcs
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">
                                                Rp
                                                <?= number_format((int) $stokItem['harga'], 0, ',', '.') ?>
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">
                                                <?= htmlspecialchars($stokItem['tgl_masuk'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-4 py-3 text-slate-500">
                                                <?= htmlspecialchars($stokItem['tgl_exp'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="inline-flex items-center gap-2">
                                                    <form method="post">
                                                        <input type="hidden" name="stok_id"
                                                            value="<?= (int) $stokItem['id'] ?>" />
                                                        <button type="submit" name="decrease"
                                                            class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-cyan-500 hover:bg-cyan-50">
                                                            Kurangi
                                                        </button>
                                                    </form>
                                                    <form method="post">
                                                        <input type="hidden" name="stok_id"
                                                            value="<?= (int) $stokItem['id'] ?>" />
                                                        <button type="submit" name="increase"
                                                            class="rounded-lg bg-cyan-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-cyan-700">
                                                            Tambah
                                                        </button>
                                                    </form>
                                                    <form method="post" onsubmit="return confirm('Hapus batch ini?')">
                                                        <input type="hidden" name="stok_id"
                                                            value="<?= (int) $stokItem['id'] ?>" />
                                                        <button type="submit" name="delete"
                                                            class="rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-600">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>

                <article class="rounded-lg border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-slate-800">Tambah Batch Stok</h3>
                        <span class="rounded-md bg-cyan-50 px-3 py-1 text-xs font-medium text-cyan-700">Mode
                            Tambah</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Pilih obat dari list product lalu input data batch.</p>

                    <form class="mt-4 space-y-3" method="post">
                        <select x-model="addForm.id_product" name="id_product"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                            required>
                            <option value="">Pilih obat</option>
                            <?php foreach ($productOptions as $productItem): ?>
                                <option value="<?= (int) $productItem['id'] ?>">
                                    <?= $productItem['nama'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input x-model="addForm.batch" type="text" placeholder="Kode batch" name="batch"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                            required />
                        <input x-model.number="addForm.jumlah" type="number" min="0" placeholder="Stok awal"
                            name="jumlah"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                            required />
                        <div class="flex flex-col">
                            <label for="tgl_exp" class="text-sm text-slate-500">Tanggal Expired:</label>
                            <input x-model="addForm.tgl_exp" type="date" id="tgl_exp" name="tgl_exp"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                                required />
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="add"
                                class="w-full rounded-lg bg-cyan-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-cyan-700">
                                Simpan Batch
                            </button>
                            <button type="button"
                                class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-50"
                                @click="resetAddForm()">
                                Reset
                            </button>
                        </div>
                    </form>
                </article>
            </div>
        </main>
    </div>

    <script>
        function stokPage() {
            return {
                sidebarOpen: false,
                query: "",
                addForm: {
                    id_product: "",
                    batch: "",
                    jumlah: 0,
                    tgl_masuk: "",
                    tgl_exp: "",
                },
                resetAddForm() {
                    this.addForm = {
                        id_product: "",
                        batch: "",
                        jumlah: 0,
                        tgl_masuk: "",
                        tgl_exp: "",
                    };
                },
            };
        }
    </script>
</body>

</html>