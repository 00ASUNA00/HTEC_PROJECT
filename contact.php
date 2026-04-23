<?php
/**
 * HTEC - Contact Page
 */
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/models/OtherModels.php';

$activePage = 'contact';
$pageTitle  = 'Contact — HTEC';

$errors = [];
$formData = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

// Pre-fill product from URL
if (!empty($_GET['product'])) {
    $formData['subject'] = 'Quote Request: ' . sanitize($_GET['product']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $formData['name']    = sanitize($_POST['name'] ?? '');
        $formData['email']   = sanitize($_POST['email'] ?? '');
        $formData['phone']   = sanitize($_POST['phone'] ?? '');
        $formData['subject'] = sanitize($_POST['subject'] ?? '');
        $formData['message'] = sanitize($_POST['message'] ?? '');

        if (!$formData['name'])                          $errors[] = 'Name is required.';
        if (!validateEmail($formData['email']))          $errors[] = 'A valid email address is required.';
        if (!$formData['message'] || strlen($formData['message']) < 10) $errors[] = 'Message must be at least 10 characters.';

        if (!$errors) {
            $contactModel = new ContactModel();
            $contactModel->save($formData);
            setFlash('success', 'Thank you! Your message has been received. We\'ll be in touch shortly.');
            redirect(url('contact.php'));
        }
    }
}

include __DIR__ . '/views/layouts/header.php';
?>

<!-- Header -->
<div class="pt-32 pb-16 bg-htec-gray border-b border-htec-border">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="section-label">Get In Touch</div>
        <h1 class="text-4xl md:text-6xl font-display font-700 mt-2">Contact Us</h1>
        <p class="text-htec-text mt-4 max-w-xl text-lg">Our engineering team responds within one business day.</p>
    </div>
</div>

<!-- Contact Content -->
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-16">

        <!-- Contact Info -->
        <div class="lg:col-span-2 fade-up">
            <h2 class="font-display font-600 text-2xl mb-8">Let's Connect</h2>

            <?php
            $contactItems = [
                ['fas fa-map-marker-alt', 'Headquarters', '<a href="https://www.google.com/maps/place/%E0%B8%9A%E0%B8%A3%E0%B8%B4%E0%B8%A9%E0%B8%B1%E0%B8%97+%E0%B9%82%E0%B8%AE%E0%B8%A1%E0%B9%80%E0%B8%97%E0%B8%84+%E0%B9%80%E0%B8%AD%E0%B9%87%E0%B8%99%E0%B8%88%E0%B8%B4%E0%B8%99%E0%B8%B5%E0%B9%82%E0%B8%AD%E0%B9%89+%E0%B8%84%E0%B8%AD%E0%B8%99%E0%B9%82%E0%B8%97%E0%B8%A3%E0%B8%A5+%E0%B8%88%E0%B8%B3%E0%B8%81%E0%B8%B1%E0%B8%94/@18.8060231,98.9836874,17z/data=!4m14!1m7!3m6!1s0x30da3aeca3b80b61:0x9610c5ec446c31fe!2z4Lia4Lij4Li04Lip4Lix4LiXIOC5guC4ruC4oeC5gOC4l-C4hCDguYDguK3guYfguJnguIjguLTguJnguLXguYLguK3guYkg4LiE4Lit4LiZ4LmC4LiX4Lij4LilIOC4iOC4s-C4geC4seC4lA!8m2!3d18.8059216!4d98.9847496!16s%2Fg%2F11csrwxgw5!3m5!1s0x30da3aeca3b80b61:0x9610c5ec446c31fe!8m2!3d18.8059216!4d98.9847496!16s%2Fg%2F11csrwxgw5?entry=ttu&g_ep=EgoyMDI2MDMzMS4wIKXMDSoASAFQAw%3D%3D" class="hover:text-white transition-colors" class="hover:text-white">2/24 ถนนเวียงบัว ตำบลช้างเผือก<br>อำเภอเมือง จังหวัดเชียงใหม่ <br>50300</a>'],
                ['fas fa-phone',          'Phone',        '<a href="tel:086-494-5979" class="hover:text-white">086-494-5979</a>'],
                ['fas fa-envelope',       'Email',        '<a href="mailto:htec2553@hotmail.com" class="hover:text-white">htec2553@hotmail.com</a>'],
                ['fas fa-clock',          'Business Hours','Mon–Fri: 9:00 AM – 4:30 PM CT<br>24/7 Emergency Support Available'],
            ];
            foreach ($contactItems as $ci): ?>
            <div class="flex gap-5 mb-8">
                <div class="w-10 h-10 bg-htec-mid border border-htec-border flex items-center justify-center text-htec-red shrink-0">
                    <i class="<?= $ci[0] ?>"></i>
                </div>
                <div>
                    <div class="text-xs font-500 uppercase tracking-widest text-htec-text mb-1"><?= $ci[1] ?></div>
                    <div class="text-htec-text-light text-sm leading-relaxed"><?= $ci[2] ?></div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Social Links -->
            <div class="border-t border-htec-border pt-8 mt-4">
                <div class="text-xs font-500 uppercase tracking-widest text-htec-text mb-4">Follow HTEC</div>
                <div class="flex gap-3">
                    <a href="https://line.me/ti/p/b_00000000000000000000000000000000" class="social-icon"><i class="fab fa-line"></i></a>
                    <a href="https://mail.google.com/mail/" class="social-icon"><i class="fab fa-google"></i></a>
                    <a href="https://www.facebook.com/htecCM/" class="social-icon"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-3 fade-up" style="transition-delay:0.1s">
            <div class="bg-htec-gray border border-htec-border p-8 md:p-10">
                <h2 class="font-display font-600 text-xl mb-8">Send Us a Message</h2>

                <?php if ($errors): ?>
                <div class="flash-message flash-error mb-6">
                    <span class="flash-icon">✕</span>
                    <div>
                        <?php foreach ($errors as $e): ?>
                            <div><?= htmlspecialchars($e) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <form id="contact-form" method="POST" action="">
                    <?= csrfField() ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="form-group mb-0">
                            <label class="form-label">Full Name <span class="text-htec-red">*</span></label>
                            <input type="text" name="name" value="<?= htmlspecialchars($formData['name']) ?>" placeholder="John Smith" class="form-input" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Email Address <span class="text-htec-red">*</span></label>
                            <input type="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" placeholder="john@company.com" class="form-input" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div class="form-group mb-0">
                            <label class="form-label">Phone (Optional)</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($formData['phone']) ?>" placeholder="+1 (555) 000-0000" class="form-input">
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" value="<?= htmlspecialchars($formData['subject']) ?>" placeholder="How can we help?" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Message <span class="text-htec-red">*</span></label>
                        <textarea name="message" placeholder="Describe your project or inquiry…" class="form-input" rows="6" required><?= htmlspecialchars($formData['message']) ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-4 text-base">
                        Send Message <i class="fas fa-paper-plane ml-2"></i>
                    </button>

                    <p class="text-htec-text text-xs text-center mt-4">By submitting, you agree to our Privacy Policy. We never share your information.</p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
