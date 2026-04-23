<?php
/**
 * HTEC - Admin Portfolio Management
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/OtherModels.php';

requireAdmin();

$adminTitle      = 'Portfolio';
$adminActivePage = 'portfolio';
$portfolioModel  = new PortfolioModel();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        setFlash('error', 'Invalid CSRF token.');
    } else {
        $id = sanitizeInt($_POST['id'] ?? 0);
        if ($id && $portfolioModel->delete($id)) {
            setFlash('success', 'Portfolio item deleted.');
        } else {
            setFlash('error', 'Failed to delete item.');
        }
    }
    redirect(url('admin/portfolio.php'));
}

$page    = max(1, sanitizeInt($_GET['page'] ?? 1));
$perPage = 20;
$total   = $portfolioModel->countAll();
$pager   = paginate($total, $perPage, $page);
$items   = $portfolioModel->getAdminPaginated($perPage, $pager['offset']);

include __DIR__ . '/../views/admin/header.php';
?>

<div class="admin-page-head flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <div class="breadcrumb mb-1">
            <a href="<?= url('admin/') ?>">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="text-white">Portfolio</span>
        </div>
        <p class="text-htec-text text-sm"><?= $total ?> item(s)</p>
    </div>
    <a href="<?= url('admin/portfolio-edit.php') ?>" class="btn-primary btn-sm">
        <i class="fas fa-plus mr-2"></i> Add Item
    </a>
</div>

<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Client</th>
                    <th>Technologies</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items): ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <?php if ($item['image']): ?>
                                    <img src="<?= url('uploads/' . $item['image']) ?>" class="w-12 h-10 object-cover border border-htec-border" alt="">
                                <?php else: ?>
                                    <div class="w-12 h-10 bg-htec-mid border border-htec-border flex items-center justify-center text-htec-border"><i class="fas fa-industry text-sm"></i></div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-500 text-white text-sm"><?= htmlspecialchars($item['title']) ?></div>
                                    <div class="text-htec-text text-xs"><?= htmlspecialchars(truncate($item['description'] ?? '', 60)) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-sm"><?= htmlspecialchars($item['client'] ?? '—') ?></td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <?php foreach (array_slice(explode(',', $item['technologies'] ?? ''), 0, 3) as $tech): ?>
                                    <span class="text-xs px-2 py-0.5 bg-htec-mid border border-htec-border text-htec-text"><?= htmlspecialchars(trim($tech)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td><span class="status-<?= $item['status'] ?>"><?= ucfirst($item['status']) ?></span></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="<?= url('admin/portfolio-edit.php?id=' . $item['id']) ?>" class="action-btn action-btn-edit"><i class="fas fa-edit"></i></a>
                                <form method="POST" style="display:inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="action-btn action-btn-delete"
                                        data-confirm="Delete '<?= htmlspecialchars($item['title']) ?>'?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-12 text-htec-text">
                        No portfolio items. <a href="<?= url('admin/portfolio-edit.php') ?>" class="text-htec-red hover:underline">Add first item →</a>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pager['total_pages'] > 1): ?>
<div class="mt-6 flex items-center justify-center gap-2">
    <a href="?page=<?= $pager['prev_page'] ?>" class="page-btn <?= !$pager['has_prev'] ? 'disabled' : '' ?>">
        <i class="fas fa-chevron-left text-xs"></i>
    </a>
    <?php for ($p = 1; $p <= $pager['total_pages']; $p++): ?>
        <?php if ($p === 1 || $p === $pager['total_pages'] || abs($p - $pager['current']) <= 2): ?>
            <a href="?page=<?= $p ?>" class="page-btn <?= $p === $pager['current'] ? 'active' : '' ?>"><?= $p ?></a>
        <?php elseif (abs($p - $pager['current']) === 3): ?>
            <span class="page-btn" style="pointer-events:none">…</span>
        <?php endif; ?>
    <?php endfor; ?>
    <a href="?page=<?= $pager['next_page'] ?>" class="page-btn <?= !$pager['has_next'] ? 'disabled' : '' ?>">
        <i class="fas fa-chevron-right text-xs"></i>
    </a>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../views/admin/footer.php'; ?>
