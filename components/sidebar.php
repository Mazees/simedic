<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['logout'])) {
    $user->logout();
}
$activePage = $activePage ?? '';
?>
<aside
    class="border-b border-cyan-200 flex flex-col bg-cyan-50 p-4 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto lg:border-b-0 lg:border-r lg:p-6">
    <div class="flex items-center justify-between lg:block">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-700">
                SIMEDIC
            </p>
            <h1 class="mt-1 text-xl font-bold">Sistem Apotek</h1>
        </div>
        <button class="rounded-lg border border-cyan-200 bg-white px-3 py-2 text-sm font-semibold lg:hidden"
            @click="sidebarOpen = !sidebarOpen">
            Menu
        </button>
    </div>

    <nav class="mt-4 h-full flex flex-col gap-2" :class="sidebarOpen ? 'flex' : 'hidden lg:flex'">
        <a href="../dashboard/"
            class=" w-full rounded-xl border px-4 py-3 text-left text-sm font-semibold <?php echo $activePage === 'dashboard' ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-200 bg-white text-slate-700'; ?>">
            Dashboard
        </a>
        <a href="../pos-obat/"
            class=" w-full rounded-xl border px-4 py-3 text-left text-sm font-semibold <?php echo $activePage === 'pos' ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-200 bg-white text-slate-700'; ?>">
            Transaksi
        </a>
        <a href="../histori-transaksi/"
            class=" w-full rounded-xl border px-4 py-3 text-left text-sm font-semibold <?php echo $activePage === 'histori' ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-200 bg-white text-slate-700'; ?>">
            Histori Transaksi
        </a>
        <a href="../stok-obat/"
            class=" w-full rounded-xl border px-4 py-3 text-left text-sm font-semibold <?php echo $activePage === 'stok' ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-200 bg-white text-slate-700'; ?>">
            Stok Obat
        </a>
        <a href="../list-product/"
            class=" w-full rounded-xl border px-4 py-3 text-left text-sm font-semibold <?php echo $activePage === 'list-product' ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-200 bg-white text-slate-700'; ?>">
            List Obat
        </a>
        <?php if ($user instanceof SuperAdmin): ?>
            <a href="../manajemen-user/"
                class=" w-full rounded-xl border px-4 py-3 text-left text-sm font-semibold <?php echo $activePage === 'manajemen-user' ? 'border-cyan-600 bg-cyan-600 text-white' : 'border-slate-200 bg-white text-slate-700'; ?>">
                Manajemen User
            </a>
        <?php endif; ?>
        <form method="post" class=" mt-auto">
            <button type="submit" name="logout" href="../login/"
                class="w-full rounded-xl border hover:bg-slate-600 bg-cyan-500 px-4 py-3 text-left text-sm font-semibold text-white">
                Logout
            </button>
        </form>
    </nav>
</aside>