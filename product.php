<?php
/**
 * HTEC - Product Detail Page
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/ProductModel.php';

$id = sanitizeInt($_GET['id'] ?? 0);
if (!$id) redirect(url('products.php'));

$productModel = new ProductModel();
$product = $productModel->getOne($id);

if (!$product || $product['status'] !== 'active') {
    http_response_code(404);
    $pageTitle = 'Product Not Found — HTEC';
    $activePage = 'products';
    include __DIR__ . '/views/layouts/header.php';
    echo '<div class="min-h-screen flex items-center justify-center text-center px-6 pt-20"><div><h1 class="text-4xl font-display font-700 mb-4">Product Not Found</h1><p class="text-htec-text mb-8">The product you\'re looking for doesn\'t exist.</p><a href="' . url('products.php') . '" class="btn-primary">Browse Products</a></div></div>';
    include __DIR__ . '/views/layouts/footer.php';
    exit;
}

// Related products (same category)
$related = [];
if ($product['category_id']) {
    $allCat = $productModel->getAll(['category' => $product['category_slug']], 4);
    $related = array_filter($allCat, fn($p) => $p['id'] !== $product['id']);
    $related = array_values(array_slice($related, 0, 3));
}

$pageTitle = htmlspecialchars($product['name']) . ' — HTEC';
$pageDescription = htmlspecialchars($product['short_description'] ?? '');
$activePage = 'products';

include __DIR__ . '/views/layouts/header.php';
?>

<!-- Breadcrumb -->
<div class="pt-28 pb-4">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <nav class="breadcrumb">
            <a href="<?= url() ?>">Home</a>
            <span class="breadcrumb-sep">/</span>
            <a href="<?= url('products.php') ?>">Products</a>
            <?php if ($product['category_name']): ?>
            <span class="breadcrumb-sep">/</span>
            <a href="<?= url('products.php?category=' . $product['category_slug']) ?>"><?= htmlspecialchars($product['category_name']) ?></a>
            <?php endif; ?>
            <span class="breadcrumb-sep">/</span>
            <span class="text-white"><?= htmlspecialchars($product['name']) ?></span>
        </nav>
    </div>
</div>

<!-- Product Detail -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 pb-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

        <!-- Image Gallery -->
        <div class="fade-up">
            <!-- Main Image -->
            <div class="bg-htec-gray border border-htec-border overflow-hidden mb-4" style="height: 420px">
                <?php if ($product['primary_image']): ?>
                    <img id="main-product-image" src="<?= url('uploads/' . $product['primary_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-htec-border text-6xl"><i class="fas fa-microchip"></i></div>
                <?php endif; ?>
            </div>

            <!-- Thumbnails -->
            <?php if (count($product['images']) > 1): ?>
            <div class="flex gap-3 flex-wrap">
                <?php foreach ($product['images'] as $i => $img): ?>
                <button class="product-thumb thumb-btn <?= $img['is_primary'] ? 'active' : '' ?> border border-htec-border hover:border-htec-red"
                    data-src="<?= url('uploads/' . $img['image_path']) ?>"
                    style="width:80px;height:60px;overflow:hidden;padding:0;background:var(--gray)">
                    <img src="<?= url('uploads/' . $img['image_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div>
            <?php if ($product['category_name']): ?>
                <div class="badge mb-4"><?= htmlspecialchars($product['category_name']) ?></div>
            <?php endif; ?>
            
            <h1 class="text-3xl md:text-4xl font-display font-700 leading-tight mb-4 fade-up">
                <?= htmlspecialchars($product['name']) ?>
            </h1>
            
            <?php if ($product['short_description']): ?>
            <p class="text-xl text-htec-text leading-relaxed mb-8 fade-up">
                <?= htmlspecialchars($product['short_description']) ?>
            </p>
            <?php endif; ?>

            <div class="accent-line mb-8"></div>

            <!-- Full Description -->
            <div class="text-htec-text leading-relaxed text-base space-y-4 mb-10 fade-up">
                <?php foreach (explode("\n\n", $product['description'] ?? '') as $para): ?>
                    <?php if (trim($para)): ?>
                        <p><?= nl2br(htmlspecialchars(trim($para))) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 fade-up">
                <a href="<?= url('contact.php?product=' . urlencode($product['name'])) ?>" class="btn-primary px-8 py-4">
                    <i class="fas fa-envelope mr-2"></i> Contact Us
                </a>
                
                <?php if ($product['datasheet']): ?>
                    <?php $isExternal = str_starts_with($product['datasheet'], 'http'); ?>
                    <?php $dsUrl = $isExternal ? $product['datasheet'] : url('uploads/' . $product['datasheet']); ?>
                    <a href="<?= htmlspecialchars($dsUrl) ?>" <?= $isExternal ? 'target="_blank"' : 'download' ?> class="btn-outline px-8 py-4">
                        <i class="fas fa-file-pdf mr-2 text-red-400"></i> Download Datasheet
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Datasheet Viewer -->
    <?php if ($product['datasheet'] && !str_starts_with($product['datasheet'], 'http')): ?>
    <div class="mt-20 fade-up">
        <div class="section-label mb-6">Technical Documentation</div>
        <h2 class="text-2xl font-display font-600 mb-6">Product Datasheet</h2>
        <div class="bg-htec-gray border border-htec-border overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-htec-border">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-pdf text-red-400 text-xl"></i>
                    <span class="font-500 text-sm"><?= htmlspecialchars($product['name']) ?> — Datasheet</span>
                </div>
                <a href="<?= url('uploads/' . $product['datasheet']) ?>" download class="btn-primary btn-sm">
                    <i class="fas fa-download mr-1"></i> Download PDF
                </a>
            </div>
            <iframe src="<?= url('uploads/' . $product['datasheet']) ?>#toolbar=1" class="w-full" style="height:600px; border:none;" title="Product Datasheet"></iframe>
        </div>
    </div>
    <?php endif; ?>

    <!-- Related Products -->
    <?php if ($related): ?>
    <div class="mt-24">
        <div class="section-label mb-6">Related Products</div>
        <h2 class="text-2xl font-display font-600 mb-8">You May Also Like</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <?php foreach ($related as $rp): ?>
            <a href="<?= url('product.php?id=' . $rp['id']) ?>" class="product-card fade-up">
                <?php if ($rp['primary_image']): ?>
                    <img src="<?= url('uploads/' . $rp['primary_image']) ?>" alt="" class="product-img" style="height:180px">
                <?php else: ?>
                    <div class="product-img-placeholder" style="height:180px"><i class="fas fa-microchip"></i></div>
                <?php endif; ?>
                <div class="p-5">
                    <h3 class="font-display font-600 text-base mb-1"><?= htmlspecialchars($rp['name']) ?></h3>
                    <p class="text-htec-text text-sm"><?= htmlspecialchars(truncate($rp['short_description'] ?? '', 80)) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
