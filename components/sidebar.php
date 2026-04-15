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
    class="border-b-2 border-slate-900 flex flex-col bg-slate-900 p-4 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto lg:border-b-0 lg:border-r-4 lg:border-cyan-500 lg:p-6">
    <div class="flex items-center justify-between lg:block">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-cyan-400">
                SIMEDIC
            </p>
            <h1 class="mt-1 text-xl font-bold text-white">Sistem Apotek</h1>
        </div>
        <button class="border-2 border-cyan-500 bg-slate-800 px-3 py-2 text-sm font-semibold text-white lg:hidden"
            @click="sidebarOpen = !sidebarOpen">
            Menu
        </button>
    </div>

    <nav class="mt-4 h-full flex flex-col gap-1" :class="sidebarOpen ? 'flex' : 'hidden lg:flex'">
        <a href="../dashboard/"
            class="w-full border-l-4 px-4 py-3 text-left text-sm font-semibold transition-colors <?php echo $activePage === 'dashboard' ? 'border-cyan-400 bg-cyan-500 text-white' : 'border-transparent text-slate-300 hover:border-slate-500 hover:bg-slate-800'; ?>">
            Dashboard
        </a>
        <a href="../pos-obat/"
            class="w-full border-l-4 px-4 py-3 text-left text-sm font-semibold transition-colors <?php echo $activePage === 'pos' ? 'border-cyan-400 bg-cyan-500 text-white' : 'border-transparent text-slate-300 hover:border-slate-500 hover:bg-slate-800'; ?>">
            Transaksi
        </a>
        <a href="../histori-transaksi/"
            class="w-full border-l-4 px-4 py-3 text-left text-sm font-semibold transition-colors <?php echo $activePage === 'histori' ? 'border-cyan-400 bg-cyan-500 text-white' : 'border-transparent text-slate-300 hover:border-slate-500 hover:bg-slate-800'; ?>">
            Histori Transaksi
        </a>
        <a href="../stok-obat/"
            class="w-full border-l-4 px-4 py-3 text-left text-sm font-semibold transition-colors <?php echo $activePage === 'stok' ? 'border-cyan-400 bg-cyan-500 text-white' : 'border-transparent text-slate-300 hover:border-slate-500 hover:bg-slate-800'; ?>">
            Stok Obat
        </a>
        <a href="../list-product/"
            class="w-full border-l-4 px-4 py-3 text-left text-sm font-semibold transition-colors <?php echo $activePage === 'list-product' ? 'border-cyan-400 bg-cyan-500 text-white' : 'border-transparent text-slate-300 hover:border-slate-500 hover:bg-slate-800'; ?>">
            List Obat
        </a>
        <?php if ($user instanceof SuperAdmin): ?>
            <a href="../manajemen-user/"
                class="w-full border-l-4 px-4 py-3 text-left text-sm font-semibold transition-colors <?php echo $activePage === 'manajemen-user' ? 'border-cyan-400 bg-cyan-500 text-white' : 'border-transparent text-slate-300 hover:border-slate-500 hover:bg-slate-800'; ?>">
                Manajemen User
            </a>
        <?php endif; ?>
        <form method="post" class="mt-auto">
            <button type="submit" name="logout" href="../login/"
                class="w-full border-2 border-cyan-500 bg-cyan-600 px-4 py-3 text-left text-sm font-bold text-white hover:bg-cyan-700">
                Logout
            </button>
        </form>
    </nav>
</aside>