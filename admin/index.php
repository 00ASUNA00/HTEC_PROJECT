<?php
/**
 * HTEC - Admin Dashboard
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/OtherModels.php';

requireAdmin();

$adminTitle      = 'Dashboard';
$adminActivePage = 'dashboard';

$productModel   = new ProductModel();
$portfolioModel = new PortfolioModel();
$contactModel   = new ContactModel();

// Stats
$db = getDB();
$totalProducts  = (int) $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalPortfolio = (int) $db->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
$totalMessages  = (int) $db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$unreadMessages = $contactModel->countUnread();

$recentProducts = $productModel->adminGetAll();
$recentProducts = array_slice($recentProducts, 0, 5);

$recentMessages = $contactModel->getAll();
$recentMessages = array_slice($recentMessages, 0, 5);

include __DIR__ . '/../views/admin/header.php';
?>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <?php
    $stats = [
        ['fas fa-box',          'Total Products',   $totalProducts,  url('admin/products.php')],
        ['fas fa-briefcase',    'Portfolio Items',  $totalPortfolio, url('admin/portfolio.php')],
        ['fas fa-envelope',     'Messages',         $totalMessages,  url('admin/contacts.php')],
        ['fas fa-bell',         'Unread Messages',  $unreadMessages, url('admin/contacts.php')],
    ];
    foreach ($stats as $s): ?>
    <a href="<?= $s[3] ?>" class="stat-card group">
        <div class="stat-card-icon group-hover:bg-htec-red/20 transition-colors"><i class="<?= $s[0] ?>"></i></div>
        <div>
            <div class="stat-card-val"><?= $s[2] ?></div>
            <div class="stat-card-label"><?= $s[1] ?></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <!-- Recent Products -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">Recent Products</span>
            <a href="<?= url('admin/products.php') ?>" class="text-htec-red text-sm hover:text-red-300 transition-colors">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentProducts): ?>
                        <?php foreach ($recentProducts as $p): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <?php if ($p['primary_image']): ?>
                                        <img src="<?= url('uploads/' . $p['primary_image']) ?>" class="w-8 h-8 object-cover" alt="">
                                    <?php else: ?>
                                        <div class="w-8 h-8 bg-htec-mid flex items-center justify-center text-htec-border text-xs"><i class="fas fa-microchip"></i></div>
                                    <?php endif; ?>
                                    <span class="font-500 text-white text-sm"><?= htmlspecialchars($p['name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                            <td>
                                <span class="status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
                            </td>
                            <td>
                                <a href="<?= url('admin/product-edit.php?id=' . $p['id']) ?>" class="action-btn action-btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-htec-text py-8">No products yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">Recent Messages</span>
            <a href="<?= url('admin/contacts.php') ?>" class="text-htec-red text-sm hover:text-red-300 transition-colors">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentMessages): ?>
                        <?php foreach ($recentMessages as $m): ?>
                        <tr class="<?= $m['status'] === 'unread' ? 'bg-htec-red/5' : '' ?>">
                            <td>
                                <div class="text-white text-sm font-500"><?= htmlspecialchars($m['name']) ?></div>
                                <div class="text-htec-text text-xs"><?= htmlspecialchars($m['email']) ?></div>
                            </td>
                            <td class="text-sm"><?= htmlspecialchars(truncate($m['subject'] ?? 'No subject', 35)) ?></td>
                            <td>
                                <?php if ($m['status'] === 'unread'): ?>
                                    <span class="status-active" style="background:rgba(227,24,55,0.1);color:#f87171;border-color:rgba(227,24,55,0.2)">Unread</span>
                                <?php else: ?>
                                    <span class="status-inactive">Read</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-xs"><?= timeAgo($m['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-htec-text py-8">No messages yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6">
    <h3 class="font-display font-600 text-base mb-4 text-htec-text uppercase tracking-widest text-xs">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="<?= url('admin/product-edit.php') ?>" class="btn-primary btn-sm"><i class="fas fa-plus mr-2"></i>Add Product</a>
        <a href="<?= url('admin/portfolio-edit.php') ?>" class="btn-outline btn-sm"><i class="fas fa-plus mr-2"></i>Add Portfolio Item</a>
        <a href="<?= url() ?>" target="_blank" class="btn-outline btn-sm"><i class="fas fa-external-link-alt mr-2"></i>View Site</a>
    </div>
</div>

<?php include __DIR__ . '/../views/admin/footer.php'; ?>
