<?php
require_once __DIR__ . '/../models/user.php';
if (!$user->canAccess('dashboard')) {
    header('Location: /simedic/error?code=403');
    exit;
}
$activePage = 'dashboard';
$pageTitle = 'Dashboard';
$pageSubtitle = 'Ringkasan performa operasional apotek hari ini.';
?>
<!doctype html>
<html lang="id" x-data="appPage()" class="h-full">

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-full bg-slate-50 font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <section class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <template x-for="card in stats" :key="card.label">
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500" x-text="card.label"></p>
                            <p class="mt-2 text-3xl font-bold" x-text="card.value"></p>
                            <p class="mt-2 text-sm"
                                :class="card.trend.startsWith('+') ? 'text-cyan-700' : 'text-slate-600'"
                                x-text="card.trend"></p>
                        </article>
                    </template>
                </div>

                <div class="grid gap-6 xl:grid-cols-3">
                    <article class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-bold">Ringkasan Penjualan 7 Hari</h3>
                            <span
                                class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">Realtime</span>
                        </div>
                        <div class="grid grid-cols-7 gap-3">
                            <template x-for="day in sales" :key="day.name">
                                <div class="space-y-2 text-center">
                                    <div class="mx-auto flex h-36 w-8 items-end rounded-full bg-slate-100">
                                        <div class="w-full rounded-full bg-cyan-600" :style="`height: ${day.amount}%;`">
                                        </div>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600" x-text="day.name"></p>
                                </div>
                            </template>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-cyan-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-bold">Peringatan Cepat</h3>
                        <div class="mt-4 space-y-3">
                            <template x-for="alert in alerts" :key="alert.title">
                                <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                                    <p class="font-semibold" x-text="alert.title"></p>
                                    <p class="mt-1 text-sm text-slate-600" x-text="alert.message"></p>
                                </div>
                            </template>
                        </div>
                    </article>
                </div>
            </section>
        </main>
    </div>

    <script>
        function appPage() {
            return {
                sidebarOpen: false,
                stats: [
                    {
                        label: "Omzet Hari Ini",
                        value: "Rp 8.450.000",
                        trend: "+12.4% dari kemarin",
                    },
                    { label: "Transaksi", value: "128", trend: "+9 transaksi/jam" },
                    {
                        label: "Item Kritis",
                        value: "17 Item",
                        trend: "-3 item teratasi",
                    },
                    { label: "Retur Pending", value: "6", trend: "+1 hari ini" },
                ],
                sales: [
                    { name: "Sen", amount: 45 },
                    { name: "Sel", amount: 60 },
                    { name: "Rab", amount: 53 },
                    { name: "Kam", amount: 74 },
                    { name: "Jum", amount: 92 },
                    { name: "Sab", amount: 68 },
                    { name: "Min", amount: 81 },
                ],
                alerts: [
                    {
                        title: "Aspirin 80mg",
                        message: "Tersisa 5 strip. Rekomendasi restok hari ini.",
                    },
                    {
                        title: "Antibiotik",
                        message: "Perlu verifikasi resep untuk 3 transaksi terakhir.",
                    },
                    {
                        title: "Supplier Sinar Farma",
                        message: "Pengiriman tertunda 1 hari dari jadwal.",
                    },
                ],
            };
        }
    </script>
</body>

</html>