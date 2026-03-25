    </div><!-- /#admin-content -->
    <footer class="px-8 py-4 border-t border-htec-border text-htec-text text-xs flex items-center justify-between">
        <span>© <?= date('Y') ?> HTEC Industrial Technology — Admin Panel</span>
        <span>v<?= APP_VERSION ?></span>
    </footer>
</div><!-- /#admin-main -->
<script src="<?= url('assets/js/main.js') ?>"></script>
<?= $adminExtraScripts ?? '' ?>
</body>
</html>
