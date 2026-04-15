<?php
require_once __DIR__ . '/../models/user.php';
if (!($user instanceof SuperAdmin)) {
    header('Location: /simedic/error?code=403');
    exit;
}

if (!$user->canAccess('manajemen-user')) {
    header('Location: /simedic/error?code=403');
    exit;
}

if (isset($_POST['action'])) {
    $action = trim((string) ($_POST['action'] ?? ''));
    $id = (int) ($_POST['id'] ?? 0);
    if ($action === 'add') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if ($username && $password)
            $user->addUser($username, $password);
    } elseif ($action === 'delete' && $id)
        $user->deleteUser($id);
    elseif ($action === 'promote' && $id)
        $user->changeUserToSuperAdmin($id);
    elseif ($action === 'demote' && $id)
        $user->changeUserToUser($id);

    header('Location: /simedic/manajemen-user/');
    exit;
}

$users = $user->getAllUsers();
$activePage = 'manajemen-user';
$pageTitle = 'Manajemen User';
$pageSubtitle = 'Kelola akun pengguna dan hak akses sistem SIMEDIC.';
?>
<!doctype html>
<html lang="id" x-data="{ sidebarOpen: false }" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen User - SIMEDIC</title>
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
                    colors: { brand: { 50: "#ecfeff", 100: "#cffafe", 500: "#06b6d4", 600: "#0891b2", 700: "#0e7490", 900: "#164e63" } },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="min-h-full bg-slate-100 font-sans text-slate-900">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <main class="p-4 sm:p-6 lg:p-8">
            <?php include __DIR__ . '/../components/header.php'; ?>

            <section class="space-y-6">

                <!-- Form Tambah User -->
                <article class="bg-slate-900 p-6 text-white">
                    <h3 class="mb-5 text-lg font-bold">Tambah User Baru</h3>
                    <form method="POST" class="grid gap-4 sm:grid-cols-[1fr_1fr_auto]">
                        <input type="hidden" name="action" value="add" />
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-400">Username</label>
                            <input name="username" type="text" required placeholder="contoh: budi_apotek"
                                class="w-full border-2 border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white outline-none focus:border-cyan-500 placeholder-slate-500" />
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-slate-400">Password</label>
                            <input name="password" type="password" required placeholder="Min. 8 karakter"
                                class="w-full border-2 border-slate-600 bg-slate-800 px-4 py-2.5 text-sm text-white outline-none focus:border-cyan-500 placeholder-slate-500" />
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-cyan-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-cyan-700">
                                + Tambah
                            </button>
                        </div>
                    </form>
                </article>

                <!-- Tabel User -->
                <article class="bg-white border-t-4 border-cyan-500">
                    <div class="flex items-center justify-between border-b-2 border-slate-200 px-6 py-4">
                        <h3 class="text-lg font-bold">Daftar Pengguna</h3>
                        <span class="bg-cyan-600 px-3 py-1 text-xs font-bold text-white uppercase tracking-wider">
                            <?php echo count($users); ?> Akun
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr
                                    class="bg-slate-900 text-xs uppercase tracking-widest text-slate-300">
                                    <th class="px-6 py-3 text-left">#</th>
                                    <th class="px-6 py-3 text-left">Username</th>
                                    <th class="px-6 py-3 text-left">Role</th>
                                    <th class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y-2 divide-slate-100">
                                <?php foreach ($users as $i => $u): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 text-slate-400"><?php echo $i + 1; ?></td>
                                        <td class="px-6 py-4 font-semibold">
                                            <?php echo htmlspecialchars($u['username']); ?>
                                            <?php if ($u['id'] == $_SESSION['user_id']): ?><span
                                                    class="ml-2 bg-cyan-600 px-2 py-0.5 text-[10px] font-bold text-white">Anda</span><?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($u['is_super_admin']): ?>
                                                <span
                                                    class="bg-amber-500 px-3 py-1 text-xs font-bold text-white">Super
                                                    Admin</span>
                                            <?php else: ?>
                                                <span
                                                    class="bg-slate-200 px-3 py-1 text-xs font-bold text-slate-700">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                                    <!-- Promote / Demote -->
                                                    <form method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>" />
                                                        <?php if ($u['is_super_admin']): ?>
                                                            <input type="hidden" name="action" value="demote" />
                                                            <button
                                                                class="border-2 border-slate-300 bg-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-300">↓
                                                                Demote</button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="action" value="promote" />
                                                            <button
                                                                class="bg-amber-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-600">↑
                                                                Promote</button>
                                                        <?php endif; ?>
                                                    </form>
                                                    <!-- Hapus -->
                                                    <form method="POST"
                                                        onsubmit="return confirm('Hapus user <?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?>?')">
                                                        <input type="hidden" name="action" value="delete" />
                                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>" />
                                                        <button
                                                            class="bg-rose-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-600">Hapus</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

            </section>
        </main>
    </div>
</body>

</html>