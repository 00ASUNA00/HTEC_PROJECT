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
                    <a href="https://line.me/ti/p/b_00000000000000000000000000000000" class="social-icon"><i class="fab fa-line"></i></a>
                    <a href="https://mail.google.com/mail/" class="social-icon"><i class="fab fa-google"></i></a>
                    <a href="https://www.facebook.com/htecCM/" class="social-icon"><i class="fab fa-facebook"></i></a>
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
                        <a href="https://www.google.com/maps/place/%E0%B8%9A%E0%B8%A3%E0%B8%B4%E0%B8%A9%E0%B8%B1%E0%B8%97+%E0%B9%82%E0%B8%AE%E0%B8%A1%E0%B9%80%E0%B8%97%E0%B8%84+%E0%B9%80%E0%B8%AD%E0%B9%87%E0%B8%99%E0%B8%88%E0%B8%B4%E0%B8%99%E0%B8%B5%E0%B9%82%E0%B8%AD%E0%B9%89+%E0%B8%84%E0%B8%AD%E0%B8%99%E0%B9%82%E0%B8%97%E0%B8%A3%E0%B8%A5+%E0%B8%88%E0%B8%B3%E0%B8%81%E0%B8%B1%E0%B8%94/@18.8060231,98.9836874,17z/data=!4m14!1m7!3m6!1s0x30da3aeca3b80b61:0x9610c5ec446c31fe!2z4Lia4Lij4Li04Lip4Lix4LiXIOC5guC4ruC4oeC5gOC4l-C4hCDguYDguK3guYfguJnguIjguLTguJnguLXguYLguK3guYkg4LiE4Lit4LiZ4LmC4LiX4Lij4LilIOC4iOC4s-C4geC4seC4lA!8m2!3d18.8059216!4d98.9847496!16s%2Fg%2F11csrwxgw5!3m5!1s0x30da3aeca3b80b61:0x9610c5ec446c31fe!8m2!3d18.8059216!4d98.9847496!16s%2Fg%2F11csrwxgw5?entry=ttu&g_ep=EgoyMDI2MDMzMS4wIKXMDSoASAFQAw%3D%3D" class="hover:text-white transition-colors">2/24 ถนนเวียงบัว ตำบลช้างเผือก<br>อำเภอเมือง จังหวัดเชียงใหม่ <br>50300</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-htec-red w-4 shrink-0"></i>
                        <a href="0864945979" class="hover:text-white transition-colors">086-494-5979</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-htec-red w-4 shrink-0"></i>
                        <a href="https://mail.google.com/mail/" class="hover:text-white transition-colors">htec2553@hotmail.com</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-htec-border mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-htec-text text-sm">© <?= date('Y') ?> HTEC Industrial Technology. All rights reserved.</p>
            <div class="flex gap-6 text-htec-text text-sm">
                
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="<?= url('assets/js/main.js') ?>"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
