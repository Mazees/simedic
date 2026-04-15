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
    class="flex flex-col bg-white border-b border-slate-200 p-4 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto lg:border-b-0 lg:border-r lg:border-slate-200 lg:p-5">
    <div class="flex items-center justify-between lg:block">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-600 p-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" class="h-full w-full">
                    <rect width="32" height="32" rx="7" fill="#0891b2"/>
                    <g transform="translate(16,16) rotate(-45) translate(-16,-16)">
                        <rect x="11" y="6" width="10" height="10" rx="5" fill="white"/>
                        <rect x="11" y="11" width="10" height="5" fill="white"/>
                        <rect x="11" y="16" width="10" height="5" fill="rgba(255,255,255,0.5)"/>
                        <rect x="11" y="16" width="10" height="10" rx="5" fill="rgba(255,255,255,0.5)"/>
                    </g>
                </svg>
            </div>
            <div>
                <h1 class="text-base font-bold text-slate-800">SIMEDIC</h1>
                <p class="text-xs text-slate-400">Sistem Apotek</p>
            </div>
        </div>
        <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 lg:hidden hover:bg-slate-50"
            @click="sidebarOpen = !sidebarOpen">
            Menu
        </button>
    </div>

    <nav class="mt-6 h-full flex flex-col gap-1" :class="sidebarOpen ? 'flex' : 'hidden lg:flex'">
        <a href="../dashboard/"
            class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors <?php echo $activePage === 'dashboard' ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
            Dashboard
        </a>
        <a href="../pos-obat/"
            class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors <?php echo $activePage === 'pos' ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
            Transaksi
        </a>
        <a href="../histori-transaksi/"
            class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors <?php echo $activePage === 'histori' ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
            Histori Transaksi
        </a>
        <a href="../stok-obat/"
            class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors <?php echo $activePage === 'stok' ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
            Stok Obat
        </a>
        <a href="../list-product/"
            class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors <?php echo $activePage === 'list-product' ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
            List Obat
        </a>
        <?php if ($user instanceof SuperAdmin): ?>
            <a href="../manajemen-user/"
                class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium transition-colors <?php echo $activePage === 'manajemen-user' ? 'bg-cyan-50 text-cyan-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
                Manajemen User
            </a>
        <?php endif; ?>
        <form method="post" class="mt-auto">
            <button type="submit" name="logout"
                class="w-full rounded-lg bg-slate-100 px-3 py-2.5 text-left text-sm font-medium text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600">
                Logout
            </button>
        </form>
    </nav>
</aside>