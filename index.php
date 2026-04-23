<?php
/**
 * HTEC - Home Page
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/ProductModel.php';
require_once __DIR__ . '/models/OtherModels.php';

$pageTitle = 'HTEC — Industrial Technology Solutions';
$pageDescription = 'HTEC delivers cutting-edge industrial automation, power systems, and IIoT solutions for a smarter future.';
$activePage = 'home';

$productModel = new ProductModel();
$serviceModel = new ServiceModel();

$featuredProducts = $productModel->getAll(['featured' => true], 4);
$services = $serviceModel->getAll();

include __DIR__ . '/views/layouts/header.php';
?>

<!-- ===== HERO ===== -->
<section class="relative min-h-screen flex items-center pt-20 overflow-hidden hero-gradient">
    <!-- Grid background -->
    <div class="absolute inset-0 hero-grid opacity-30 pointer-events-none"></div>
    <!-- Red glow -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-96 h-96 bg-htec-red opacity-5 blur-3xl rounded-full pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-24">
        <div class="max-w-4xl">
            <div class="section-label fade-up">Welcome! Let us serve you!</div>
            
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-display font-800 leading-none tracking-tight mb-8 fade-up" style="transition-delay:0.1s">
                Hometech<br>
                <span class="text-htec-red">Engineo</span><br>
                Control Co.
            </h1>
            
            <p class="text-xl text-htec-text max-w-2xl leading-relaxed mb-10 fade-up" style="transition-delay:0.2s">
            Hometech provides security systems and internal communication solutions
            for businesses and organizations seeking safer, smarter, and more efficient spaces.
            </p>
            
            <div class="flex flex-wrap gap-4 fade-up" style="transition-delay:0.3s">
                <a href="<?= url('products.php') ?>" class="btn-primary text-base px-8 py-4">
                    Explore Products <i class="fas fa-arrow-right ml-1"></i>
                </a>
                <a href="<?= url('portfolio.php') ?>" class="btn-outline text-base px-8 py-4">
                    View Our Work
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-40">
        <span class="text-xs tracking-widest uppercase font-display">Scroll</span>
        <div class="w-px h-12 bg-gradient-to-b from-white to-transparent"></div>
    </div>
</section>

<!-- ===== STATS ===== -->
<section class="border-t border-b border-htec-border bg-htec-gray py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-0">
            <div class="counter-block text-center">
                <div class="font-display font-700 text-4xl text-white" data-counter="30" data-suffix="+">0</div>
                <div class="text-htec-text text-sm mt-2 tracking-wide">Years Experience</div>
            </div>
            <div class="counter-block text-center">
                <div class="font-display font-700 text-4xl text-white" data-counter="450" data-suffix="+">0</div>
                <div class="text-htec-text text-sm mt-2 tracking-wide">Projects Delivered</div>
            </div>
            <div class="counter-block text-center">
                <div class="font-display font-700 text-4xl text-white" data-counter="38" data-suffix="">0</div>
                <div class="text-htec-text text-sm mt-2 tracking-wide">Cities Served</div>
            </div>
            <div class="counter-block text-center">
                <div class="font-display font-700 text-4xl text-white" data-counter="99" data-suffix="%">0</div>
                <div class="text-htec-text text-sm mt-2 tracking-wide">Client Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<?php if ($featuredProducts): ?>
<section class="py-24 max-w-7xl mx-auto px-6 lg:px-8">
    <div class="flex items-end justify-between mb-14">
        <div>
            <div class="section-label fade-up">Our Sollutions</div>
            <h2 class="text-4xl md:text-5xl font-display font-700 leading-tight fade-up">Recommended<br>Products</h2>
        </div>
        <a href="<?= url('products.php') ?>" class="btn-outline btn-sm hidden md:inline-flex">
            All Products <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($featuredProducts as $i => $product): ?>
        <a href="<?= url('product.php?id=' . $product['id']) ?>" class="product-card fade-up" style="transition-delay:<?= $i * 0.08 ?>s">
            <?php if ($product['primary_image']): ?>
                <img src="<?= url('uploads/' . $product['primary_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
            <?php else: ?>
                <div class="product-img-placeholder"><i class="fas fa-microchip"></i></div>
            <?php endif; ?>
            <div class="p-5">
                <?php if ($product['category_name']): ?>
                    <div class="badge mb-3"><?= htmlspecialchars($product['category_name']) ?></div>
                <?php endif; ?>
                <h3 class="font-display font-600 text-base leading-snug mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-htec-text text-sm leading-relaxed"><?= htmlspecialchars(truncate($product['short_description'] ?? '', 90)) ?></p>
                <div class="mt-4 flex items-center gap-2 text-htec-red text-sm font-500">
                    <span>Learn more</span>
                    <i class="fas fa-arrow-right card-arrow transition-transform text-xs"></i>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="mt-8 text-center md:hidden">
        <a href="<?= url('products.php') ?>" class="btn-outline">View All Products</a>
    </div>
</section>
<?php endif; ?>

<!-- ===== SERVICES ===== -->
<?php if ($services): ?>
<section class="py-24 bg-htec-gray border-t border-htec-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="section-label justify-center fade-up">What We Do?</div>
            <h2 class="text-4xl md:text-5xl font-display font-700 mt-2 fade-up">Engineering Services</h2>
            <p class="text-htec-text mt-4 max-w-xl mx-auto fade-up">End-to-end solutions from concept through commissioning, backed by three decades of deep industry expertise.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($services as $i => $service): ?>
            <div class="service-card fade-up" style="transition-delay:<?= $i * 0.07 ?>s">
                <div class="service-icon"><i class="<?= htmlspecialchars($service['icon']) ?>"></i></div>
                <h3 class="font-display font-600 text-lg mb-3"><?= htmlspecialchars($service['title']) ?></h3>
                <p class="text-htec-text text-sm leading-relaxed"><?= htmlspecialchars($service['description']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 text-center">
            <a href="<?= url('services.php') ?>" class="btn-primary">All Services <i class="fas fa-arrow-right ml-2"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CTA BAND ===== -->
<section class="py-20 border-t border-htec-border relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-htec-red/10 to-transparent pointer-events-none"></div>
    <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <h2 class="text-3xl md:text-4xl font-display font-700 leading-tight fade-up">Ready to modernize<br>your operations?</h2>
                <p class="text-htec-text mt-3 fade-up">Talk to our engineers about your project requirements.</p>
            </div>
            <div class="flex gap-4 shrink-0 fade-up">
                <a href="<?= url('contact.php') ?>" class="btn-primary px-8 py-4">Contact Us</a>
                <a href="<?= url('portfolio.php') ?>" class="btn-outline px-8 py-4">Case Studies</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
