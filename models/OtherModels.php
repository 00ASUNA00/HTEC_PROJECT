<?php
/**
 * HTEC - Portfolio Model
 */
require_once __DIR__ . '/../config/helpers.php';

class PortfolioModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getAll(bool $adminMode = false): array {
        $where = $adminMode ? '' : "WHERE status = 'active'";
        return $this->db->query("SELECT * FROM portfolio {$where} ORDER BY sort_order ASC, created_at DESC")->fetchAll();
    }

    public function getOne(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM portfolio WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO portfolio (title, description, client, project_url, image, technologies, status, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'], $data['description'] ?? '', $data['client'] ?? '',
            $data['project_url'] ?? '', $data['image'] ?? '', $data['technologies'] ?? '',
            $data['status'] ?? 'active', $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = []; $params = [];
        $allowed = ['title', 'description', 'client', 'project_url', 'image', 'technologies', 'status', 'sort_order'];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "{$f} = ?"; $params[] = $data[$f]; }
        }
        if (!$fields) return false;
        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE portfolio SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $item = $this->getOne($id);
        if ($item && $item['image']) deleteFile($item['image']);
        $stmt = $this->db->prepare("DELETE FROM portfolio WHERE id = ?");
        return $stmt->execute([$id]);
    }
}


/**
 * HTEC - Contact Model
 */
class ContactModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function save(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO contacts (name, email, phone, subject, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'], $data['email'], $data['phone'] ?? '',
            $data['subject'] ?? '', $data['message'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
    }

    public function markRead(int $id): bool {
        $stmt = $this->db->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countUnread(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();
    }
}


/**
 * HTEC - User Model
 */
class UserModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
    }

    public function logout(): void {
        session_destroy();
    }
}


/**
 * HTEC - Services Model
 */
class ServiceModel {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM services WHERE status = 'active' ORDER BY sort_order")->fetchAll();
    }
}
