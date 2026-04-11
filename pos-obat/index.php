<?php
require_once __DIR__ . '/../models/user.php';
if (!$user->canAccess('pos-obat')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'pos';
$pageTitle = 'POS Obat';
$pageSubtitle = 'Input transaksi penjualan obat dengan cepat.';
?>
<!doctype html>
<html lang="id" x-data="posPage()" class="h-full">

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        <input x-model="query" type="text" placeholder="Cari obat..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600 sm:w-72" />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <template x-for="item in filteredMedicine" :key="item.id">
                            <article
                                class="rounded-xl border border-slate-200 p-4 transition hover:border-cyan-300 hover:shadow-sm">
                                <p class="font-semibold" x-text="item.name"></p>
                                <p class="mt-1 text-sm text-slate-500" x-text="'Stok: ' + item.stock + ' box'"></p>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="font-bold text-cyan-700" x-text="formatCurrency(item.price)"></p>
                                    <button
                                        class="rounded-lg bg-cyan-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-cyan-700"
                                        @click="addToCart(item)">
                                        Tambah
                                    </button>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>

                <section class="rounded-2xl border border-cyan-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold">Keranjang</h3>
                    <div class="mt-4 space-y-3" x-show="cart.length > 0">
                        <template x-for="item in cart" :key="item.id">
                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-3">
                                <p class="font-medium" x-text="item.name"></p>
                                <div class="mt-2 flex items-center justify-between text-sm">
                                    <div class="inline-flex items-center gap-2">
                                        <button class="rounded border border-cyan-200 bg-white px-2"
                                            @click="decreaseQty(item.id)">
                                            -
                                        </button>
                                        <span x-text="item.qty"></span>
                                        <button class="rounded border border-cyan-200 bg-white px-2"
                                            @click="increaseQty(item.id)">
                                            +
                                        </button>
                                    </div>
                                    <p x-text="formatCurrency(item.qty * item.price)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <p x-show="cart.length === 0" class="mt-4 text-sm text-slate-500">
                        Belum ada item dipilih.
                    </p>

                    <div class="mt-6 space-y-2 border-t border-slate-200 pt-4 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span x-text="formatCurrency(subtotal)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>PPN 11%</span>
                            <span x-text="formatCurrency(tax)"></span>
                        </div>
                        <div class="flex justify-between text-base font-bold">
                            <span>Total</span>
                            <span x-text="formatCurrency(total)"></span>
                        </div>
                    </div>

                    <button
                        class="mt-5 w-full rounded-xl bg-cyan-600 px-4 py-3 font-bold text-white transition hover:bg-cyan-700"
                        @click="checkout()">
                        Bayar
                    </button>
                </section>
            </div>
        </main>
    </div>

    <script>
        function posPage() {
            return {
                sidebarOpen: false,
                query: "",
                medicine: [
                    { id: 1, name: "Paracetamol 500mg", price: 12000, stock: 120 },
                    { id: 2, name: "Amoxicillin 500mg", price: 24000, stock: 42 },
                    { id: 3, name: "Cetirizine 10mg", price: 18000, stock: 60 },
                    { id: 4, name: "Omeprazole 20mg", price: 21000, stock: 30 },
                    { id: 5, name: "Aspirin 80mg", price: 15000, stock: 15 },
                    { id: 6, name: "Vitamin C 1000mg", price: 27000, stock: 80 },
                ],
                cart: [],
                get filteredMedicine() {
                    return this.medicine.filter((item) =>
                        item.name.toLowerCase().includes(this.query.toLowerCase())
                    );
                },
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + item.price * item.qty, 0);
                },
                get tax() {
                    return this.subtotal * 0.11;
                },
                get total() {
                    return this.subtotal + this.tax;
                },
                addToCart(item) {
                    const found = this.cart.find((cartItem) => cartItem.id === item.id);
                    if (found) {
                        found.qty += 1;
                    } else {
                        this.cart.push({ ...item, qty: 1 });
                    }
                },
                increaseQty(id) {
                    const found = this.cart.find((item) => item.id === id);
                    if (found) found.qty += 1;
                },
                decreaseQty(id) {
                    const found = this.cart.find((item) => item.id === id);
                    if (!found) return;
                    found.qty -= 1;
                    if (found.qty <= 0) {
                        this.cart = this.cart.filter((item) => item.id !== id);
                    }
                },
                checkout() {
                    if (!this.cart.length) {
                        alert("Keranjang masih kosong.");
                        return;
                    }
                    alert("Pembayaran berhasil diproses.");
                    this.cart = [];
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