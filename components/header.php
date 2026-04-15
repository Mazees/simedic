<?php
$pageTitle = $pageTitle ?? 'SIMEDIC';
$pageSubtitle = $pageSubtitle ?? '';
?>
<header class="mb-6 rounded-lg bg-white border border-slate-200 px-5 py-4">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($pageSubtitle !== ''): ?>
                <p class="mt-1 text-sm text-slate-400"><?php echo htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</header>