<?php
/**
 * HTEC - Portfolio Page
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/OtherModels.php';

$activePage = 'portfolio';
$pageTitle  = 'Portfolio — HTEC';
$portfolioModel = new PortfolioModel();
$items = $portfolioModel->getAll();

include __DIR__ . '/views/layouts/header.php';
?>

<!-- Header -->
<div class="pt-32 pb-16 bg-htec-gray border-b border-htec-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="section-label">Proven Results</div>
        <h1 class="text-4xl md:text-6xl font-display font-700 mt-2">Our Work</h1>
        <p class="text-htec-text mt-4 max-w-xl text-lg">Real-world projects that demonstrate the breadth and depth of HTEC's engineering capabilities.</p>
    </div>
</div>

<!-- Portfolio Grid -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
    <?php if ($items): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($items as $i => $item): ?>
        <div class="card fade-up" style="transition-delay:<?= ($i % 3) * 0.08 ?>s">
            <?php if ($item['image']): ?>
            <div class="overflow-hidden" style="height:240px">
                <img src="<?= url('uploads/' . $item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
            </div>
            <?php else: ?>
            <div class="flex items-center justify-center text-htec-border text-5xl bg-htec-mid" style="height:240px">
                <i class="fas fa-industry"></i>
            </div>
            <?php endif; ?>
            
            <div class="p-6">
                <h2 class="font-display font-600 text-lg mb-2 leading-snug"><?= htmlspecialchars($item['title']) ?></h2>
                
                <?php if ($item['client']): ?>
                <div class="flex items-center gap-2 text-htec-text text-xs mb-3">
                    <i class="fas fa-building text-htec-red"></i>
                    <span><?= htmlspecialchars($item['client']) ?></span>
                </div>
                <?php endif; ?>
                
                <p class="text-htec-text text-sm leading-relaxed mb-4"><?= htmlspecialchars(truncate($item['description'] ?? '', 130)) ?></p>
                
                <?php if ($item['technologies']): ?>
                <div class="flex flex-wrap gap-2 mt-4">
                    <?php foreach (explode(',', $item['technologies']) as $tech): ?>
                        <span class="text-xs px-2 py-1 bg-htec-mid border border-htec-border text-htec-text"><?= htmlspecialchars(trim($tech)) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-24">
        <i class="fas fa-briefcase text-4xl text-htec-border mb-6"></i>
        <h3 class="font-display font-600 text-xl mb-3">Portfolio Coming Soon</h3>
        <p class="text-htec-text">Check back for case studies and project showcases.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Stats Band -->
<section class="bg-htec-gray border-t border-htec-border py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php
            $stats = [
                ['450+', 'Projects Completed'],
                ['38',   'Countries'],
                ['$2B+', 'Assets Monitored'],
                ['99%',  'On-Time Delivery'],
            ];
            foreach ($stats as $s): ?>
            <div class="text-center fade-up">
                <div class="font-display font-700 text-3xl text-white"><?= $s[0] ?></div>
                <div class="text-htec-text text-sm mt-2"><?= $s[1] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<div class="py-16 text-center">
    <div class="max-w-2xl mx-auto px-6 fade-up">
        <h2 class="text-3xl font-display font-700 mb-4">Let's Build Something Great</h2>
        <p class="text-htec-text mb-8">Ready to start your next industrial technology project?</p>
        <a href="<?= url('contact.php') ?>" class="btn-primary px-10 py-4">Get in Touch</a>
    </div>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
