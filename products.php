<?php
/**
 * HTEC - Products Listing Page
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/ProductModel.php';

$activePage = 'products';
$productModel = new ProductModel();

// Filters
$search   = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$page     = max(1, sanitizeInt($_GET['page'] ?? 1));
$perPage  = 9;

$filters = [];
if ($search)   $filters['search']   = $search;
if ($category) $filters['category'] = $category;

$total      = $productModel->count($filters);
$pagination = paginate($total, $perPage, $page);
$products   = $productModel->getAll($filters, $perPage, $pagination['offset']);
$categories = $productModel->getCategories();

$pageTitle = 'Products — HTEC';
$activeCat = $category;

include __DIR__ . '/views/layouts/header.php';
?>

<!-- Page Header -->
<div class="pt-32 pb-16 bg-htec-gray border-b border-htec-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="section-label">Our Portfolio</div>
        <h1 class="text-4xl md:text-6xl font-display font-700 mt-2">Products</h1>
        <p class="text-htec-text mt-4 max-w-xl">Industrial-grade hardware and software solutions engineered for reliability.</p>
    </div>
</div>

<!-- Filters & Search -->
<div class="sticky top-20 z-30 bg-htec-dark/95 backdrop-blur border-b border-htec-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Category Filters -->
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn <?= !$activeCat ? 'active' : '' ?>" data-cat="all">All</button>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-btn <?= $activeCat === $cat['slug'] ? 'active' : '' ?>" data-cat="<?= htmlspecialchars($cat['slug']) ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Search -->
            <div class="relative w-full md:w-72 shrink-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-htec-text text-sm"></i>
                <input type="text" id="product-search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search products…"
                    class="form-input pl-9 py-2.5 text-sm">
            </div>
        </div>
    </div>
</div>

<!-- Products Grid -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
    <?php if ($products): ?>
        <!-- Result count -->
        <div class="text-htec-text text-sm mb-8">
            Showing <?= $pagination['offset'] + 1 ?>–<?= min($pagination['offset'] + $perPage, $total) ?> of <?= $total ?> products
            <?php if ($search): ?> matching "<strong class="text-white"><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $i => $product): ?>
            <a href="<?= url('product.php?id=' . $product['id']) ?>" class="product-card fade-up" style="transition-delay:<?= ($i % 3) * 0.08 ?>s">
                <?php if ($product['primary_image']): ?>
                    <img src="<?= url('uploads/' . $product['primary_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
                <?php else: ?>
                    <div class="product-img-placeholder"><i class="fas fa-microchip"></i></div>
                <?php endif; ?>
                <div class="p-6">
                    <?php if ($product['category_name']): ?>
                        <div class="badge mb-3"><?= htmlspecialchars($product['category_name']) ?></div>
                    <?php endif; ?>
                    <h2 class="font-display font-600 text-lg leading-snug mb-2"><?= htmlspecialchars($product['name']) ?></h2>
                    <p class="text-htec-text text-sm leading-relaxed"><?= htmlspecialchars(truncate($product['short_description'] ?? '', 110)) ?></p>
                    <div class="mt-5 flex items-center gap-2 text-htec-red text-sm font-500 border-t border-htec-border pt-4">
                        <span>View Details</span>
                        <i class="fas fa-arrow-right card-arrow transition-transform text-xs"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="mt-14 flex items-center justify-center gap-2">
            <?php
            $baseUrl = '?';
            $qp = [];
            if ($search) $qp[] = 'search=' . urlencode($search);
            if ($category) $qp[] = 'category=' . urlencode($category);
            $base = '?' . implode('&', $qp);
            $sep = $qp ? '&' : '';
            ?>
            <a href="<?= $base . $sep . 'page=' . $pagination['prev_page'] ?>" class="page-btn <?= !$pagination['has_prev'] ? 'disabled' : '' ?>"><i class="fas fa-chevron-left text-xs"></i></a>
            
            <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                <?php if ($p === 1 || $p === $pagination['total_pages'] || abs($p - $pagination['current']) <= 2): ?>
                    <a href="<?= $base . $sep . 'page=' . $p ?>" class="page-btn <?= $p === $pagination['current'] ? 'active' : '' ?>"><?= $p ?></a>
                <?php elseif (abs($p - $pagination['current']) === 3): ?>
                    <span class="page-btn" style="pointer-events:none">…</span>
                <?php endif; ?>
            <?php endfor; ?>

            <a href="<?= $base . $sep . 'page=' . $pagination['next_page'] ?>" class="page-btn <?= !$pagination['has_next'] ? 'disabled' : '' ?>"><i class="fas fa-chevron-right text-xs"></i></a>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty state -->
        <div class="text-center py-24">
            <i class="fas fa-search text-4xl text-htec-border mb-6"></i>
            <h3 class="font-display font-600 text-xl mb-3">No products found</h3>
            <p class="text-htec-text mb-6">Try adjusting your search or filter criteria.</p>
            <a href="<?= url('products.php') ?>" class="btn-outline">Clear Filters</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
