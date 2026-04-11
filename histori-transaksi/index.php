<?php
require_once __DIR__ . '/../models/user.php';
if (!$user->canAccess('histori-transaksi')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'histori';
$pageTitle = 'Histori Transaksi';
$pageSubtitle = 'Pantau riwayat transaksi penjualan obat.';
?>
<!doctype html>
<html lang="id" x-data="historiPage()" class="h-full">

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-full bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <section class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-3">
                    <article class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Total Transaksi Hari Ini</p>
                        <p class="mt-2 text-2xl font-bold" x-text="summary.totalToday"></p>
                    </article>
                    <article class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Omzet Hari Ini</p>
                        <p class="mt-2 text-2xl font-bold text-cyan-700" x-text="formatCurrency(summary.revenueToday)">
                        </p>
                    </article>
                    <article class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Rata-rata per Transaksi</p>
                        <p class="mt-2 text-2xl font-bold" x-text="formatCurrency(summary.average)"></p>
                    </article>
                </div>

                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-bold">Daftar Histori</h3>
                        <div class="flex flex-wrap gap-2">
                            <input x-model="query" type="text" placeholder="Cari kode transaksi atau kasir..."
                                class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none focus:border-cyan-600 sm:w-72" />
                            <select x-model="statusFilter"
                                class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-600">
                                <option value="all">Semua Status</option>
                                <option value="Lunas">Lunas</option>
                                <option value="Refund">Refund</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] text-left text-sm">
                            <thead class="text-xs uppercase tracking-[0.12em] text-slate-500">
                                <tr>
                                    <th class="pb-3">Kode</th>
                                    <th class="pb-3">Waktu</th>
                                    <th class="pb-3">Kasir</th>
                                    <th class="pb-3">Total Item</th>
                                    <th class="pb-3">Total Bayar</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="tx in filteredTransactions" :key="tx.code">
                                    <tr class="border-t border-slate-100">
                                        <td class="py-3 font-semibold" x-text="tx.code"></td>
                                        <td class="py-3 text-slate-600" x-text="tx.time"></td>
                                        <td class="py-3 text-slate-700" x-text="tx.cashier"></td>
                                        <td class="py-3 text-slate-700" x-text="tx.items + ' item'"></td>
                                        <td class="py-3 text-slate-700" x-text="formatCurrency(tx.total)"></td>
                                        <td class="py-3">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold"
                                                :class="tx.status === 'Lunas' ? 'bg-cyan-100 text-cyan-800' : tx.status === 'Pending' ? 'bg-slate-100 text-slate-700' : 'bg-rose-100 text-rose-700'"
                                                x-text="tx.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <p x-show="filteredTransactions.length === 0" class="mt-4 text-sm text-slate-500">
                        Tidak ada transaksi yang cocok dengan filter.
                    </p>
                </article>
            </section>
        </main>
    </div>

    <script>
        function historiPage() {
            return {
                sidebarOpen: false,
                query: "",
                statusFilter: "all",
                transactions: [
                    { code: "TRX-240401", time: "09 Apr 2026 08:15", cashier: "Nadia", items: 3, total: 98000, status: "Lunas" },
                    { code: "TRX-240402", time: "09 Apr 2026 08:31", cashier: "Rizal", items: 2, total: 54000, status: "Pending" },
                    { code: "TRX-240403", time: "09 Apr 2026 09:02", cashier: "Nadia", items: 6, total: 176000, status: "Lunas" },
                    { code: "TRX-240404", time: "09 Apr 2026 09:21", cashier: "Dewi", items: 1, total: 24000, status: "Refund" },
                    { code: "TRX-240405", time: "09 Apr 2026 09:44", cashier: "Rizal", items: 4, total: 112000, status: "Lunas" },
                    { code: "TRX-240406", time: "09 Apr 2026 10:03", cashier: "Dewi", items: 2, total: 69000, status: "Lunas" },
                ],
                get filteredTransactions() {
                    return this.transactions.filter((tx) => {
                        const textMatched =
                            tx.code.toLowerCase().includes(this.query.toLowerCase()) ||
                            tx.cashier.toLowerCase().includes(this.query.toLowerCase());
                        const statusMatched = this.statusFilter === "all" || tx.status === this.statusFilter;
                        return textMatched && statusMatched;
                    });
                },
                get summary() {
                    const todayList = this.transactions;
                    const revenueToday = todayList
                        .filter((tx) => tx.status === "Lunas")
                        .reduce((sum, tx) => sum + tx.total, 0);
                    const totalToday = todayList.length;
                    return {
                        totalToday,
                        revenueToday,
                        average: totalToday ? Math.round(revenueToday / totalToday) : 0,
                    };
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