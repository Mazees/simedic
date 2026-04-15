<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/product.php';
if (!$user->canAccess('list-product')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'list-product';
$pageTitle = 'List Product';
$pageSubtitle = 'Tambah, edit, dan hapus data produk dalam satu halaman (frontend demo).';
$totalProduct = $product->getJumlahProduct();
$rataRataHarga = $product->getRataRataHargaProduct();
$listProduct = $product->getAllProduct();
$searchQuery = '';

if (isset($_POST['add'])) {
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $harga = (int) ($_POST['harga'] ?? 0);
    if ($nama !== '' && $harga >= 0) {
        $product->addProduct($nama, $harga);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['edit'])) {
    $id = (int) ($_POST['product_id'] ?? 0);
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $harga = (int) ($_POST['harga'] ?? 0);
    if ($id > 0 && $nama !== '' && $harga >= 0) {
        $product->updateProduct($nama, $harga, $id);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_POST['delete'])) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $product->deleteProduct($id);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
if (isset($_GET['search'])) {
    $searchQuery = trim((string) ($_GET['search'] ?? ''));
    $listProduct = $product->searchProduct($searchQuery);
}
?>
<!doctype html>
<html lang="id" x-data="productListPage()" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>List Product - SIMEDIC</title>
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
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-base font-semibold text-slate-800">Data Product</h3>
                            <form method="get" class="w-full flex gap-2 sm:w-auto">
                                <input type="text" value="<?= $_GET['search'] ?? '' ?>"
                                    placeholder="Cari nama product..." name="search"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 sm:w-72" />

                                <button type="submit"
                                    class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-700">
                                    Cari
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg bg-cyan-600 p-4 text-white">
                                <p class="text-xs font-medium uppercase tracking-wide text-cyan-100">Total Product</p>
                                <p class="mt-1 text-2xl font-bold"><?= $totalProduct ?></p>
                            </div>
                            <div class="rounded-lg bg-slate-800 p-4 text-white">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Harga Rata-Rata</p>
                                <p class="mt-1 text-2xl font-bold text-cyan-400">Rp <?= $rataRataHarga ?></p>
                            </div>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">ID</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Nama Product</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400">Harga</th>
                                        <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-slate-400 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listProduct as $key => $product): ?>
                                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                                            <td class="px-4 py-3 text-slate-400">#OB<?= $product['id'] ?></td>
                                            <td class="px-4 py-3 font-medium text-slate-700"><?= $product['nama'] ?></td>
                                            <td class="px-4 py-3 text-slate-500">
                                                Rp <?= $product['harga'] ?>
                                            </td>
                                            <td class="px-4 py-3 text-left">
                                                <div class="inline-flex items-center gap-2">
                                                    <button type="button"
                                                        @click='setEditForm(<?= (int) $product['id'] ?>, <?= json_encode($product['nama']) ?>, <?= (int) $product['harga'] ?>)'
                                                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-cyan-500 hover:bg-cyan-50">
                                                        Edit
                                                    </button>
                                                    <form method="POST"
                                                        onsubmit="return confirm('Hapus obat <?= $product['nama'] ?>?')">
                                                        <input type="hidden" name="id"
                                                            value="<?php echo $product['id']; ?>" />
                                                        <button type="submit" name="delete" class="rounded-lg bg-rose-500 px-3 py-1.5
                                                        text-xs font-medium text-white hover:bg-rose-600">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>

                <article x-show="!isEditMode" class="rounded-lg border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-slate-800">Tambah Product</h3>
                        <span class="rounded-md bg-cyan-50 px-3 py-1 text-xs font-medium text-cyan-700">Mode
                            Tambah</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-400">Form khusus tambah product baru.</p>

                    <form class="mt-4 space-y-3" method="post">
                        <input x-model="addForm.name" type="text" placeholder="Nama product" name="nama"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                            required />
                        <input x-model.number="addForm.price" type="number" min="0" placeholder="Harga" name="harga"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
                            required />
                        <div class="flex gap-2">
                            <button type="submit" name="add"
                                class="w-full rounded-lg bg-cyan-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-cyan-700">
                                Tambah Product
                            </button>
                            <button type="button"
                                class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-50"
                                @click="resetAddForm()">
                                Reset
                            </button>
                        </div>
                    </form>
                </article>

                <article x-show="isEditMode" class="rounded-lg border border-amber-300 bg-amber-50 p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-amber-800">Edit Product</h3>
                    </div>
                    <p class="mt-1 text-sm text-amber-600">Form khusus edit product yang dipilih.</p>

                    <form class="mt-4 space-y-3" method="post">
                        <input x-model="editForm.name" type="text" placeholder="Nama product" name="nama"
                            class="w-full rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            required />
                        <input x-model.number="editForm.price" type="number" min="0" placeholder="Harga" name="harga"
                            class="w-full rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm outline-none transition focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            required />
                        <input type="hidden" name="product_id" x-model="editForm.id" />
                        <div class="flex gap-2">
                            <button type="submit" name="edit"
                                class="w-full rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-amber-600">
                                Update Product
                            </button>
                            <button type="button"
                                class="rounded-lg border border-amber-300 bg-white px-4 py-2.5 text-sm font-medium text-amber-600 hover:bg-amber-100"
                                @click="cancelEdit()">
                                Batal
                            </button>
                        </div>
                    </form>
                </article>
            </div>
        </main>
    </div>

    <script>
        function productListPage() {
            return {
                sidebarOpen: false,
                query: "",
                editForm: {
                    id: null,
                    name: "",
                    price: 0,

                },
                setEditForm(id, name, price) {
                    this.editForm = {
                        id: id,
                        name: name,
                        price: price,

                    }
                },
                cancelEdit() {
                    this.editForm = {
                        id: null,
                        name: "",
                        price: 0,

                    }
                },
                addForm: {
                    name: "",
                    price: 0,

                },
                get isEditMode() {
                    return this.editForm.id !== null;
                },
                resetAddForm() {
                    this.addForm = {
                        name: "",
                        price: 0,

                    };
                },
            };
        }
    </script>
</body>

</html>