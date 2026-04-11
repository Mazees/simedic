<?php
require_once __DIR__ . '/../models/user.php';
if (!$user->canAccess('stok-obat')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'stok';
$pageTitle = 'Manajemen Stok Obat';
$pageSubtitle = 'Kelola item obat, stok masuk/keluar, dan obat baru.';
?>
<!doctype html>
<html lang="id" x-data="stockManagerPage()" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Stok Obat - SIMEDIC</title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-full bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <div class="grid gap-6 lg:grid-cols-3">
                <section class="lg:col-span-2 space-y-6">
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-lg font-bold">Daftar Stok Obat</h2>
                            <input x-model="query" type="text" placeholder="Cari nama obat atau batch..."
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600 sm:w-80" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">
                                    Total Item
                                </p>
                                <p class="mt-1 text-2xl font-bold" x-text="items.length"></p>
                            </div>
                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">
                                    Stok Rendah
                                </p>
                                <p class="mt-1 text-2xl font-bold text-cyan-700" x-text="lowStockCount"></p>
                            </div>
                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">
                                    Total Unit
                                </p>
                                <p class="mt-1 text-2xl font-bold" x-text="totalUnits"></p>
                            </div>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="w-full min-w-[760px] text-left text-sm">
                                <thead class="text-xs uppercase tracking-[0.12em] text-slate-500">
                                    <tr>
                                        <th class="pb-3">Nama Obat</th>
                                        <th class="pb-3">Batch</th>
                                        <th class="pb-3">Stok</th>
                                        <th class="pb-3">Harga</th>
                                        <th class="pb-3">Aksi Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <tr class="border-t border-slate-100">
                                            <td class="py-3 font-semibold" x-text="item.name"></td>
                                            <td class="py-3 text-slate-600" x-text="item.batch"></td>
                                            <td class="py-3">
                                                <span class="rounded-full px-3 py-1 text-xs font-semibold"
                                                    :class="item.stock <= 20 ? 'bg-cyan-100 text-cyan-800' : 'bg-slate-100 text-slate-700'"
                                                    x-text="item.stock + ' box'"></span>
                                            </td>
                                            <td class="py-3 text-slate-600" x-text="formatCurrency(item.price)"></td>
                                            <td class="py-3">
                                                <div class="inline-flex items-center gap-2">
                                                    <button
                                                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold hover:border-cyan-500"
                                                        @click="adjustStock(item.id, -1)">
                                                        Kurangi
                                                    </button>
                                                    <button
                                                        class="rounded-lg border border-cyan-600 bg-cyan-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-700"
                                                        @click="adjustStock(item.id, 1)">
                                                        Tambah
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </article>
                </section>

                <section class="space-y-6">
                    <article class="rounded-2xl border border-cyan-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold">Tambah Obat Baru</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Input data obat untuk dimasukkan ke stok.
                        </p>

                        <form class="mt-5 space-y-3" @submit.prevent="addMedicine()">
                            <input x-model="newItem.name" type="text" placeholder="Nama obat"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600"
                                required />
                            <input x-model="newItem.batch" type="text" placeholder="Kode batch"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600"
                                required />
                            <div class="grid grid-cols-2 gap-2">
                                <input x-model.number="newItem.stock" type="number" min="0" placeholder="Stok awal"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600"
                                    required />
                                <input x-model.number="newItem.price" type="number" min="0" placeholder="Harga"
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600"
                                    required />
                            </div>
                            <button type="submit"
                                class="w-full rounded-xl bg-cyan-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-700">
                                Simpan Obat
                            </button>
                        </form>
                    </article>
                </section>
            </div>
        </main>
    </div>

    <script>
        function stockManagerPage() {
            return {
                sidebarOpen: false,
                query: "",
                items: [
                    {
                        id: 1,
                        name: "Paracetamol 500mg",
                        batch: "PCT-11A",
                        stock: 120,
                        price: 12000,
                    },
                    {
                        id: 2,
                        name: "Amoxicillin 500mg",
                        batch: "AMX-102",
                        stock: 18,
                        price: 24000,
                    },
                    {
                        id: 3,
                        name: "Cetirizine 10mg",
                        batch: "CTZ-81B",
                        stock: 30,
                        price: 18000,
                    },
                    {
                        id: 4,
                        name: "Omeprazole 20mg",
                        batch: "OMP-15D",
                        stock: 22,
                        price: 21000,
                    },
                ],
                history: [],
                newItem: {
                    name: "",
                    batch: "",
                    stock: 0,
                    price: 0,
                },
                get filteredItems() {
                    return this.items.filter(
                        (item) =>
                            item.name.toLowerCase().includes(this.query.toLowerCase()) ||
                            item.batch.toLowerCase().includes(this.query.toLowerCase())
                    );
                },
                get lowStockCount() {
                    return this.items.filter((item) => item.stock <= 20).length;
                },
                get totalUnits() {
                    return this.items.reduce((sum, item) => sum + item.stock, 0);
                },
                adjustStock(id, delta) {
                    const item = this.items.find((row) => row.id === id);
                    if (!item) return;
                    if (delta < 0 && item.stock === 0) return;
                    item.stock += delta;
                    this.history.unshift({
                        id: Date.now() + Math.random(),
                        text: `${delta > 0 ? "Tambah" : "Kurangi"} stok ${item.name} (${delta > 0 ? "+1" : "-1"})`,
                        time: new Date().toLocaleString("id-ID"),
                    });
                    if (this.history.length > 20) this.history.pop();
                },
                addMedicine() {
                    const name = this.newItem.name.trim();
                    const batch = this.newItem.batch.trim();
                    if (!name || !batch) return;
                    const item = {
                        id: Date.now(),
                        name,
                        batch,
                        stock: Number(this.newItem.stock) || 0,
                        price: Number(this.newItem.price) || 0,
                    };
                    this.items.unshift(item);
                    this.history.unshift({
                        id: Date.now() + Math.random(),
                        text: `Tambah obat baru ${item.name} (stok awal ${item.stock} box)`,
                        time: new Date().toLocaleString("id-ID"),
                    });
                    this.newItem = { name: "", batch: "", stock: 0, price: 0 };
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        maximumFractionDigits: 0,
                    }).format(value);
                },
            };
        }
    </script>
</body>

</html>