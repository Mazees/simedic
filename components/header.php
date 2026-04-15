<?php
$pageTitle = $pageTitle ?? 'SIMEDIC';
$pageSubtitle = $pageSubtitle ?? '';
?>
<header class="mb-6 border-b-4 border-cyan-500 bg-slate-900 px-4 py-4 sm:px-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.3em] text-cyan-400">
                SIMEDIC
            </p>
            <h2 class="text-2xl font-bold text-white"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($pageSubtitle !== ''): ?>
                <p class="mt-1 text-sm text-slate-400"><?php echo htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</header>