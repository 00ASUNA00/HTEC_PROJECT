<?php
/**
 * HTEC - Services Page
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/OtherModels.php';

$activePage = 'services';
$pageTitle  = 'Services — HTEC';
$serviceModel = new ServiceModel();
$services = $serviceModel->getAll();

include __DIR__ . '/views/layouts/header.php';
?>

<!-- Header -->
<div class="pt-32 pb-16 bg-htec-gray border-b border-htec-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="section-label">What We Offer</div>
        <h1 class="text-4xl md:text-6xl font-display font-700 mt-2">Engineering<br>Services</h1>
        <p class="text-htec-text mt-4 max-w-2xl text-lg">From initial feasibility to final commissioning — we partner with you at every stage of your industrial transformation.</p>
    </div>
</div>

<!-- Services Grid -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($services as $i => $service): ?>
        <div class="service-card fade-up" style="transition-delay:<?= ($i % 3) * 0.08 ?>s">
            <div class="service-icon"><i class="<?= htmlspecialchars($service['icon']) ?>"></i></div>
            <h2 class="font-display font-600 text-xl mb-4"><?= htmlspecialchars($service['title']) ?></h2>
            <p class="text-htec-text leading-relaxed"><?= htmlspecialchars($service['description']) ?></p>
            <a href="<?= url('contact.php') ?>" class="mt-6 inline-flex items-center gap-2 text-htec-red text-sm font-500 hover:gap-3 transition-all">
                Get in touch <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Process Section -->
<section class="bg-htec-gray border-t border-htec-border py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="section-label justify-center">Our Methodology</div>
            <h2 class="text-3xl md:text-4xl font-display font-700 mt-2">How We Work</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php
            $steps = [
                ['01', 'Discovery', 'Deep-dive into your operational challenges and project requirements.', 'fas fa-search'],
                ['02', 'Engineering', 'System design, specification, and simulation using industry-standard tools.', 'fas fa-drafting-compass'],
                ['03', 'Integration', 'Factory acceptance testing, site installation, and commissioning.', 'fas fa-cogs'],
                ['04', 'Support', 'Ongoing maintenance, upgrades, and 24/7 technical support.', 'fas fa-headset'],
            ];
            foreach ($steps as $i => $step): ?>
            <div class="bg-htec-dark border border-htec-border p-8 relative fade-up" style="transition-delay:<?= $i * 0.08 ?>s">
                <div class="text-6xl font-display font-800 text-htec-border leading-none mb-4"><?= $step[0] ?></div>
                <div class="text-htec-red mb-3 text-2xl"><i class="<?= $step[3] ?>"></i></div>
                <h3 class="font-display font-600 text-lg mb-2"><?= $step[1] ?></h3>
                <p class="text-htec-text text-sm leading-relaxed"><?= $step[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<div class="py-16 text-center fade-up">
    <div class="max-w-2xl mx-auto px-6">
        <h2 class="text-3xl font-display font-700 mb-4">Start Your Project</h2>
        <p class="text-htec-text mb-8">Tell us about your requirements and our team will respond within one business day.</p>
        <a href="<?= url('contact.php') ?>" class="btn-primary px-10 py-4">Contact Our Engineers</a>
    </div>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
