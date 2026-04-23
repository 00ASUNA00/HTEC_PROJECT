<?php
/**
 * HTEC - Admin Contacts / Messages
 */
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/OtherModels.php';
requireAdmin();

$adminTitle      = 'Messages';
$adminActivePage = 'contacts';
$contactModel    = new ContactModel();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        setFlash('error', 'Invalid CSRF token.');
    } else {
        $action = $_POST['action'] ?? '';
        $id     = sanitizeInt($_POST['id'] ?? 0);
        if ($id) {
            if ($action === 'delete') {
                $contactModel->delete($id);
                setFlash('success', 'Message deleted.');
            } elseif ($action === 'read') {
                $contactModel->markRead($id);
                setFlash('success', 'Marked as read.');
            }
        }
    }
    redirect(url('admin/contacts.php'));
}

$messages = $contactModel->getAll();

include __DIR__ . '/../views/admin/header.php';
?>

<div class="admin-page-head flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <div class="breadcrumb mb-1">
            <a href="<?= url('admin/') ?>">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="text-white">Messages</span>
        </div>
        <p class="text-htec-text text-sm"><?= count($messages) ?> message(s)</p>
    </div>
</div>

<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message Preview</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($messages): ?>
                    <?php foreach ($messages as $m): ?>
                    <tr class="<?= $m['status'] === 'unread' ? 'bg-htec-red/5' : '' ?>">
                        <td>
                            <div class="font-500 text-white text-sm"><?= htmlspecialchars($m['name']) ?></div>
                            <div class="text-htec-text text-xs"><?= htmlspecialchars($m['email']) ?></div>
                            <?php if ($m['phone']): ?>
                                <div class="text-htec-text text-xs"><?= htmlspecialchars($m['phone']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-sm"><?= htmlspecialchars($m['subject'] ?: '(No subject)') ?></td>
                        <td class="text-htec-text text-sm max-w-xs">
                            <div class="truncate" style="max-width:220px" title="<?= htmlspecialchars($m['message']) ?>">
                                <?= htmlspecialchars(truncate($m['message'], 80)) ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($m['status'] === 'unread'): ?>
                                <span class="status-active" style="background:rgba(227,24,55,0.1);color:#fca5a5;border-color:rgba(227,24,55,0.2)">Unread</span>
                            <?php elseif ($m['status'] === 'read'): ?>
                                <span class="status-inactive">Read</span>
                            <?php else: ?>
                                <span class="status-active">Replied</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-xs text-htec-text">
                            <?= date('M j, Y', strtotime($m['created_at'])) ?><br>
                            <?= date('H:i', strtotime($m['created_at'])) ?>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=Re: <?= urlencode($m['subject'] ?: 'Your enquiry') ?>"
                                   class="action-btn action-btn-view" title="Reply"><i class="fas fa-reply"></i></a>
                                <?php if ($m['status'] === 'unread'): ?>
                                <form method="POST" style="display:inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="read">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="action-btn action-btn-edit" title="Mark as read"><i class="fas fa-check"></i></button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="action-btn action-btn-delete" title="Delete"
                                        data-confirm="Delete this message from <?= htmlspecialchars($m['name']) ?>?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-14 text-htec-text">
                        <i class="fas fa-inbox text-3xl text-htec-border mb-4 block"></i>
                        No messages yet.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../views/admin/footer.php'; ?>
