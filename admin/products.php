<?php
/**
 * HTEC - Admin Products List
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/ProductModel.php';

requireAdmin();

$adminTitle      = 'Products';
$adminActivePage = 'products';

$productModel = new ProductModel();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        setFlash('error', 'Invalid CSRF token.');
    } else {
        $id = sanitizeInt($_POST['id'] ?? 0);
        if ($id && $productModel->delete($id)) {
            setFlash('success', 'Product deleted successfully.');
        } else {
            setFlash('error', 'Failed to delete product.');
        }
    }
    redirect(url('admin/products.php'));
}

$search   = sanitize($_GET['search'] ?? '');
$products = $productModel->adminGetAll($search);

include __DIR__ . '/../views/admin/header.php';
?>

<!-- Header Row -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <div class="breadcrumb mb-1">
            <a href="<?= url('admin/') ?>">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="text-white">Products</span>
        </div>
        <p class="text-htec-text text-sm"><?= count($products) ?> product(s) total</p>
    </div>
    <a href="<?= url('admin/product-edit.php') ?>" class="btn-primary btn-sm shrink-0">
        <i class="fas fa-plus mr-2"></i> Add Product
    </a>
</div>

<!-- Search -->
<form method="GET" class="mb-6 flex gap-3">
    <div class="relative flex-1 max-w-md">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-htec-text text-sm"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search products…" class="admin-input pl-9">
    </div>
    <button type="submit" class="btn-outline btn-sm px-5">Search</button>
    <?php if ($search): ?>
        <a href="<?= url('admin/products.php') ?>" class="btn-outline btn-sm px-4">Clear</a>
    <?php endif; ?>
</form>

<!-- Table -->
<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Datasheet</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products): ?>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <?php if ($p['primary_image']): ?>
                                    <img src="<?= url('uploads/' . $p['primary_image']) ?>" class="w-10 h-10 object-cover border border-htec-border" alt="">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-htec-mid border border-htec-border flex items-center justify-center text-htec-border"><i class="fas fa-microchip text-sm"></i></div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-500 text-white text-sm"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-xs text-htec-text font-mono"><?= htmlspecialchars($p['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($p['featured']): ?>
                                <span class="text-yellow-400 text-xs"><i class="fas fa-star mr-1"></i>Featured</span>
                            <?php else: ?>
                                <span class="text-htec-text text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['datasheet']): ?>
                                <span class="text-green-400 text-xs"><i class="fas fa-file-pdf mr-1"></i>Yes</span>
                            <?php else: ?>
                                <span class="text-htec-text text-xs">No</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
                        <td class="text-xs"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="<?= url('product.php?id=' . $p['id']) ?>" target="_blank" class="action-btn action-btn-view" title="View"><i class="fas fa-eye"></i></a>
                                <a href="<?= url('admin/product-edit.php?id=' . $p['id']) ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="" style="display:inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="action-btn action-btn-delete" title="Delete"
                                        data-confirm="Delete '<?= htmlspecialchars($p['name']) ?>'? This cannot be undone.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-16 text-htec-text">
                            <i class="fas fa-box text-3xl mb-4 block text-htec-border"></i>
                            No products found. <a href="<?= url('admin/product-edit.php') ?>" class="text-htec-red hover:underline">Add your first product →</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../views/admin/footer.php'; ?>
