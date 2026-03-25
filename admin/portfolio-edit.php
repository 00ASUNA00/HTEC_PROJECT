<?php
/**
 * HTEC - Admin Portfolio Add / Edit
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/OtherModels.php';

requireAdmin();

$portfolioModel = new PortfolioModel();
$id   = sanitizeInt($_GET['id'] ?? 0);
$item = $id ? $portfolioModel->getOne($id) : null;
$isEdit = (bool) $item;

$adminTitle      = $isEdit ? 'Edit Portfolio Item' : 'Add Portfolio Item';
$adminActivePage = 'portfolio';

$errors = [];
$data   = $item ?? [
    'title' => '', 'description' => '', 'client' => '',
    'project_url' => '', 'image' => '', 'technologies' => '', 'status' => 'active', 'sort_order' => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $data['title']       = sanitize($_POST['title'] ?? '');
        $data['description'] = sanitize($_POST['description'] ?? '');
        $data['client']      = sanitize($_POST['client'] ?? '');
        $data['project_url'] = sanitize($_POST['project_url'] ?? '');
        $data['technologies']= sanitize($_POST['technologies'] ?? '');
        $data['sort_order']  = sanitizeInt($_POST['sort_order'] ?? 0);
        $data['status']      = in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active';

        if (!$data['title']) $errors[] = 'Title is required.';

        // Image upload
        if (!empty($_FILES['image']['name'])) {
            $res = uploadFile($_FILES['image'], 'portfolio', ALLOWED_IMAGE_TYPES);
            if (!$res['success']) {
                $errors[] = 'Image: ' . $res['error'];
            } else {
                if ($isEdit && $item['image']) deleteFile($item['image']);
                $data['image'] = $res['path'];
            }
        } elseif ($isEdit) {
            $data['image'] = $item['image'];
        }

        if (!$errors) {
            if ($isEdit) {
                $portfolioModel->update($id, $data);
                setFlash('success', 'Portfolio item updated.');
            } else {
                $portfolioModel->create($data);
                setFlash('success', 'Portfolio item created.');
            }
            redirect(url('admin/portfolio.php'));
        }
    }
}

include __DIR__ . '/../views/admin/header.php';
?>

<div class="breadcrumb mb-6">
    <a href="<?= url('admin/') ?>">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="<?= url('admin/portfolio.php') ?>">Portfolio</a>
    <span class="breadcrumb-sep">/</span>
    <span class="text-white"><?= $isEdit ? 'Edit' : 'Add' ?></span>
</div>

<?php if ($errors): ?>
<div class="flash-message flash-error mb-6">
    <span class="flash-icon">✕</span>
    <div><?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?></div>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Project Details</span></div>
                <div class="admin-card-body space-y-5">
                    <div class="admin-form-group">
                        <label class="admin-label">Title <span class="text-htec-red">*</span></label>
                        <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" class="admin-input" placeholder="e.g. Automotive Assembly Line Automation" required>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Description</label>
                        <textarea name="description" rows="5" class="admin-input" placeholder="Project overview and outcomes…"><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="admin-form-group">
                            <label class="admin-label">Client</label>
                            <input type="text" name="client" value="<?= htmlspecialchars($data['client']) ?>" class="admin-input" placeholder="Client name">
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-label">Project URL</label>
                            <input type="url" name="project_url" value="<?= htmlspecialchars($data['project_url']) ?>" class="admin-input" placeholder="https://...">
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Technologies Used</label>
                        <input type="text" name="technologies" value="<?= htmlspecialchars($data['technologies']) ?>" class="admin-input" placeholder="HT-5000 PLC, SCADA Suite, EtherCAT">
                        <p class="text-htec-text text-xs mt-1">Comma-separated list</p>
                    </div>
                </div>
            </div>

            <!-- Image -->
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Project Image</span></div>
                <div class="admin-card-body">
                    <?php if ($isEdit && $item['image']): ?>
                    <div class="mb-4">
                        <img src="<?= url('uploads/' . $item['image']) ?>" class="max-h-48 border border-htec-border" id="current-img" alt="">
                        <p class="text-htec-text text-xs mt-2">Current image — upload a new one to replace.</p>
                    </div>
                    <?php endif; ?>
                    <label class="upload-zone block cursor-pointer" for="port-img">
                        <i class="fas fa-image text-3xl text-htec-text mb-3 block"></i>
                        <p class="text-htec-text text-sm">Click to upload project image</p>
                        <p class="text-htec-text text-xs mt-1">JPEG, PNG, WebP — Max 10MB</p>
                        <input id="port-img" type="file" name="image" accept="image/*" class="hidden" data-preview="port-preview">
                    </label>
                    <img id="port-preview" class="mt-4 max-h-40 border border-htec-border hidden" alt="">
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Settings</span></div>
                <div class="admin-card-body space-y-4">
                    <div class="admin-form-group">
                        <label class="admin-label">Status</label>
                        <select name="status" class="admin-input">
                            <option value="active"   <?= ($data['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($data['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Sort Order</label>
                        <input type="number" name="sort_order" value="<?= (int)($data['sort_order'] ?? 0) ?>" class="admin-input" min="0">
                        <p class="text-htec-text text-xs mt-1">Lower = appears first</p>
                    </div>
                    <div class="border-t border-htec-border pt-4 flex gap-3">
                        <button type="submit" class="btn-primary btn-sm flex-1 justify-center">
                            <?= $isEdit ? 'Update' : 'Create' ?>
                        </button>
                        <a href="<?= url('admin/portfolio.php') ?>" class="btn-outline btn-sm px-4">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/../views/admin/footer.php'; ?>
