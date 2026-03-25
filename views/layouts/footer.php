</main>

<!-- ===== FOOTER ===== -->
<footer class="bg-htec-gray border-t border-htec-border mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Brand -->
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-htec-red flex items-center justify-center font-display font-800 text-white text-sm">HT</div>
                    <span class="font-display font-700 text-xl tracking-widest">HTEC</span>
                </div>
                <p class="text-htec-text text-sm leading-relaxed mb-6">
                    Engineering the future of industrial automation and smart infrastructure since 2002.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <!-- Products -->
            <div>
                <h4 class="font-display font-600 text-sm tracking-widest uppercase mb-6 text-white">Products</h4>
                <ul class="space-y-3">
                    <li><a href="<?= url('products.php?category=industrial-automation') ?>" class="footer-link">Industrial Automation</a></li>
                    <li><a href="<?= url('products.php?category=power-systems') ?>" class="footer-link">Power Systems</a></li>
                    <li><a href="<?= url('products.php?category=sensors-iot') ?>" class="footer-link">Sensors &amp; IIoT</a></li>
                    <li><a href="<?= url('products.php?category=software-solutions') ?>" class="footer-link">Software Solutions</a></li>
                    <li><a href="<?= url('products.php') ?>" class="footer-link">All Products</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="font-display font-600 text-sm tracking-widest uppercase mb-6 text-white">Company</h4>
                <ul class="space-y-3">
                    <li><a href="<?= url('services.php') ?>" class="footer-link">Services</a></li>
                    <li><a href="<?= url('portfolio.php') ?>" class="footer-link">Portfolio</a></li>
                    <li><a href="<?= url('contact.php') ?>" class="footer-link">Contact Us</a></li>
                    <li><a href="<?= url('admin/') ?>" class="footer-link">Admin Panel</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-display font-600 text-sm tracking-widest uppercase mb-6 text-white">Get in Touch</h4>
                <ul class="space-y-4 text-htec-text text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-htec-red mt-1 w-4 shrink-0"></i>
                        <span>1200 Technology Drive,<br>Industrial Park, TX 77001</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-htec-red w-4 shrink-0"></i>
                        <a href="tel:+12145550100" class="hover:text-white transition-colors">+1 (214) 555-0100</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-htec-red w-4 shrink-0"></i>
                        <a href="mailto:info@htec.com" class="hover:text-white transition-colors">info@htec.com</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-htec-border mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-htec-text text-sm">© <?= date('Y') ?> HTEC Industrial Technology. All rights reserved.</p>
            <div class="flex gap-6 text-htec-text text-sm">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Use</a>
                <a href="#" class="hover:text-white transition-colors">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="<?= url('assets/js/main.js') ?>"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
