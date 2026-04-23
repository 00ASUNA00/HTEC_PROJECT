<?php
/**
 * HTEC - Admin Product Add / Edit
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/ProductModel.php';

requireAdmin();

$productModel = new ProductModel();
$categories   = $productModel->getCategories();

$id      = sanitizeInt($_GET['id'] ?? 0);
$product = $id ? $productModel->getOne($id) : null;
$isEdit  = (bool) $product;

$adminTitle      = $isEdit ? 'Edit Product' : 'Add Product';
$adminActivePage = $isEdit ? 'products' : 'product-add';

$errors = [];
$data   = $product ?? [
    'name' => '', 'short_description' => '', 'description' => '',
    'category_id' => '', 'datasheet' => '', 'featured' => 0, 'status' => 'active',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $data['name']              = sanitize($_POST['name'] ?? '');
        $data['short_description'] = sanitize($_POST['short_description'] ?? '');
        $data['description']       = sanitize($_POST['description'] ?? '');
        $data['category_id']       = sanitizeInt($_POST['category_id'] ?? 0) ?: null;
        $data['featured']          = isset($_POST['featured']) ? 1 : 0;
        $data['status']            = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

        // Validation
        if (!$data['name'])              $errors[] = 'Product name is required.';
        if (!$data['short_description']) $errors[] = 'Short description is required.';
        if (!$data['description'])       $errors[] = 'Full description is required.';

        // Handle datasheet upload
        if (!empty($_FILES['datasheet']['name'])) {
            $result = uploadFile($_FILES['datasheet'], 'datasheets', ALLOWED_PDF_TYPES);
            if (!$result['success']) {
                $errors[] = 'Datasheet: ' . $result['error'];
            } else {
                // Delete old
                if ($isEdit && $product['datasheet']) deleteFile($product['datasheet']);
                $data['datasheet'] = $result['path'];
            }
        } elseif (isset($_POST['datasheet_url']) && $_POST['datasheet_url']) {
            $candidateUrl = trim($_POST['datasheet_url']);
            if (filter_var($candidateUrl, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $candidateUrl)) {
                $data['datasheet'] = $candidateUrl;
            } else {
                $errors[] = 'External datasheet URL must be a valid HTTP/HTTPS URL.';
            }
        } elseif ($isEdit) {
            $data['datasheet'] = $product['datasheet']; // keep existing
        }

        if (!$errors) {
            if ($isEdit) {
                $productModel->update($id, $data);
                $savedId = $id;
                setFlash('success', 'Product updated successfully.');
            } else {
                $savedId = $productModel->create($data);
                setFlash('success', 'Product created successfully.');
            }

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $isPrimary = !$isEdit; // first upload is primary for new products
                foreach ($_FILES['images']['tmp_name'] as $k => $tmp) {
                    if ($_FILES['images']['error'][$k] === UPLOAD_ERR_OK) {
                        $file = [
                            'tmp_name' => $tmp,
                            'name'     => $_FILES['images']['name'][$k],
                            'size'     => $_FILES['images']['size'][$k],
                            'error'    => $_FILES['images']['error'][$k],
                        ];
                        $res = uploadFile($file, 'products', ALLOWED_IMAGE_TYPES);
                        if ($res['success']) {
                            $productModel->addImage($savedId, $res['path'], $isPrimary && $k === 0);
                        }
                    }
                }
            }

            redirect(url('admin/products.php'));
        }
    }
}

include __DIR__ . '/../views/admin/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb mb-6">
    <a href="<?= url('admin/') ?>">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <a href="<?= url('admin/products.php') ?>">Products</a>
    <span class="breadcrumb-sep">/</span>
    <span class="text-white"><?= $isEdit ? 'Edit' : 'Add' ?></span>
</div>

<?php if ($errors): ?>
<div class="flash-message flash-error mb-6">
    <span class="flash-icon">✕</span>
    <div><?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?></div>
</div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- Main Fields -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Basic Info -->
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Product Information</span></div>
                <div class="admin-card-body space-y-5">
                    <div class="admin-form-group">
                        <label class="admin-label">Product Name <span class="text-htec-red">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" class="admin-input" placeholder="e.g. HT-5000 PLC Controller" required>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Short Description <span class="text-htec-red">*</span></label>
                        <input type="text" name="short_description" value="<?= htmlspecialchars($data['short_description']) ?>" class="admin-input" placeholder="One-line summary shown in product cards" maxlength="500" required>
                        <p class="text-htec-text text-xs mt-1">Max 500 characters. Shown in product listings.</p>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-label">Full Description <span class="text-htec-red">*</span></label>
                        <textarea name="description" rows="10" class="admin-input" placeholder="Full product description. Separate paragraphs with a blank line." required><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Product Images</span></div>
                <div class="admin-card-body">
                    <?php if ($isEdit): ?>
                    <!-- Existing images -->
                    <?php $images = $productModel->getImages($id); ?>
                    <?php if ($images): ?>
                    <div class="flex flex-wrap gap-3 mb-6">
                        <?php foreach ($images as $img): ?>
                        <div class="relative group">
                            <img src="<?= url('uploads/' . $img['image_path']) ?>" class="w-24 h-24 object-cover border border-htec-border" alt="">
                            <?php if ($img['is_primary']): ?>
                                <span class="absolute top-1 left-1 text-xs bg-htec-red text-white px-1">Primary</span>
                            <?php endif; ?>
                            <form method="POST" action="<?= url('admin/product-image-delete.php') ?>" class="absolute top-1 right-1 hidden group-hover:block">
                                <?= csrfField() ?>
                                <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                <button type="submit" class="w-6 h-6 bg-red-600 text-white text-xs flex items-center justify-center"
                                    data-confirm="Remove this image?">&times;</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>

                    <label class="upload-zone block cursor-pointer" for="img-upload">
                        <i class="fas fa-cloud-upload-alt text-3xl text-htec-text mb-3 block"></i>
                        <p class="text-htec-text text-sm mb-1">Click or drag images here</p>
                        <p class="text-htec-text text-xs">JPEG, PNG, WebP — Max 10MB each. First image becomes primary.</p>
                        <input id="img-upload" type="file" name="images[]" multiple accept="image/*" class="hidden" data-preview="img-preview">
                    </label>
                    <div id="img-preview-container" class="mt-4 flex flex-wrap gap-3"></div>
                </div>
            </div>

            <!-- Datasheet -->
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Datasheet</span></div>
                <div class="admin-card-body space-y-5">
                    <?php if ($isEdit && $data['datasheet'] && !str_starts_with($data['datasheet'], 'http')): ?>
                    <div class="flex items-center gap-3 p-3 bg-htec-mid border border-htec-border">
                        <i class="fas fa-file-pdf text-red-400 text-lg"></i>
                        <span class="text-sm text-white flex-1"><?= htmlspecialchars(basename($data['datasheet'])) ?></span>
                        <a href="<?= url('uploads/' . $data['datasheet']) ?>" target="_blank" class="action-btn action-btn-view btn-sm"><i class="fas fa-eye"></i></a>
                    </div>
                    <p class="text-htec-text text-xs">Upload a new file below to replace the existing datasheet.</p>
                    <?php endif; ?>
                    <div>
                        <label class="admin-label">Upload PDF</label>
                        <input type="file" name="datasheet" accept="application/pdf" class="admin-input py-2 text-htec-text">
                        <p class="text-htec-text text-xs mt-1">PDF only — Max 10MB</p>
                    </div>
                    <div class="relative flex items-center gap-4">
                        <div class="flex-1 h-px bg-htec-border"></div>
                        <span class="text-htec-text text-xs uppercase tracking-widest shrink-0">or</span>
                        <div class="flex-1 h-px bg-htec-border"></div>
                    </div>
                    <div>
                        <label class="admin-label">External Datasheet URL</label>
                        <input type="url" name="datasheet_url" value="<?= str_starts_with($data['datasheet'] ?? '', 'http') ? htmlspecialchars($data['datasheet']) : '' ?>" class="admin-input" placeholder="https://manufacturer.com/datasheet.pdf">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="space-y-6">
            <!-- Publish -->
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Publish</span></div>
                <div class="admin-card-body space-y-4">
                    <div class="admin-form-group">
                        <label class="admin-label">Status</label>
                        <select name="status" class="admin-input">
                            <option value="active"   <?= ($data['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($data['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="featured" id="featured" value="1" <?= ($data['featured'] ?? 0) ? 'checked' : '' ?> class="w-4 h-4 accent-red-600">
                        <label for="featured" class="text-sm text-white cursor-pointer">Featured on homepage</label>
                    </div>
                    <div class="admin-form-actions border-t border-htec-border pt-4 flex gap-3">
                        <button type="submit" class="btn-primary btn-sm flex-1 justify-center">
                            <?= $isEdit ? 'Update' : 'Create' ?> Product
                        </button>
                        <a href="<?= url('admin/products.php') ?>" class="btn-outline btn-sm px-4">Cancel</a>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Category</span></div>
                <div class="admin-card-body">
                    <select name="category_id" class="admin-input">
                        <option value="">— No Category —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($data['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <!-- Danger Zone -->
            <div class="admin-card border-red-900/40">
                <div class="admin-card-header"><span class="admin-card-title text-red-400">Danger Zone</span></div>
                <div class="admin-card-body">
                    <form method="POST" action="<?= url('admin/products.php') ?>">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" class="action-btn action-btn-delete w-full justify-center"
                            data-confirm="Permanently delete this product and all its images? This cannot be undone.">
                            <i class="fas fa-trash mr-2"></i> Delete Product
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
// Multi-image preview
document.getElementById('img-upload').addEventListener('change', function() {
    const container = document.getElementById('img-preview-container');
    container.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-20 h-20 object-cover border border-htec-border';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>

<?php include __DIR__ . '/../views/admin/footer.php'; ?>
