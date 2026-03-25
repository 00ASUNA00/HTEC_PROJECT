<?php
/**
 * HTEC - Product Model
 */

require_once __DIR__ . '/../config/helpers.php';

class ProductModel {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all products with optional filters and pagination */
    public function getAll(array $filters = [], int $limit = 12, int $offset = 0): array {
        $where = ['p.status = ?'];
        $params = ['active'];

        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            array_push($params, $s, $s, $s);
        }

        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = $filters['category'];
        }

        if (!empty($filters['featured'])) {
            $where[] = 'p.featured = 1';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $sql = "
            SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                   pi.image_path AS primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            {$whereClause}
            ORDER BY p.featured DESC, p.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Count products with filters */
    public function count(array $filters = []): int {
        $where = ['p.status = ?'];
        $params = ['active'];

        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            array_push($params, $s, $s, $s);
        }

        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = $filters['category'];
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            {$whereClause}
        ");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Get single product by ID or slug */
    public function getOne(int|string $id, bool $bySlug = false): ?array {
        $field = $bySlug ? 'p.slug' : 'p.id';
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE {$field} = ?
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) return null;

        // Get images
        $product['images'] = $this->getImages($product['id']);

        // Set primary image
        $primary = array_filter($product['images'], fn($i) => $i['is_primary']);
        $product['primary_image'] = $primary ? reset($primary)['image_path'] : null;
        if (!$product['primary_image'] && $product['images']) {
            $product['primary_image'] = $product['images'][0]['image_path'];
        }

        return $product;
    }

    /** Get product images */
    public function getImages(int $productId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /** Create product */
    public function create(array $data): int {
        $data['slug'] = $this->uniqueSlug($data['name']);
        $stmt = $this->db->prepare("
            INSERT INTO products (name, slug, description, short_description, category_id, datasheet, featured, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'] ?? '',
            $data['category_id'] ?? null,
            $data['datasheet'] ?? null,
            $data['featured'] ?? 0,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    /** Update product */
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        $allowed = ['name', 'description', 'short_description', 'category_id', 'datasheet', 'featured', 'status'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        if (!$fields) return false;
        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    /** Delete product */
    public function delete(int $id): bool {
        // Images cascade via FK; delete files
        foreach ($this->getImages($id) as $img) deleteFile($img['image_path']);
        $product = $this->getOne($id);
        if ($product && $product['datasheet']) deleteFile($product['datasheet']);
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Add image */
    public function addImage(int $productId, string $path, bool $isPrimary = false): int {
        if ($isPrimary) {
            $this->db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?")->execute([$productId]);
        }
        $stmt = $this->db->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, ?)");
        $stmt->execute([$productId, $path, $isPrimary ? 1 : 0]);
        return (int) $this->db->lastInsertId();
    }

    /** Delete image */
    public function deleteImage(int $imageId): bool {
        $stmt = $this->db->prepare("SELECT image_path FROM product_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $img = $stmt->fetch();
        if ($img) deleteFile($img['image_path']);
        $stmt = $this->db->prepare("DELETE FROM product_images WHERE id = ?");
        return $stmt->execute([$imageId]);
    }

    /** All categories */
    public function getCategories(): array {
        return $this->db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    }

    /** Generate unique slug */
    private function uniqueSlug(string $name, int $excludeId = 0): string {
        $base = slug($name);
        $s = $base;
        $i = 1;
        while (true) {
            $stmt = $this->db->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
            $stmt->execute([$s, $excludeId]);
            if (!$stmt->fetch()) break;
            $s = $base . '-' . $i++;
        }
        return $s;
    }

    /** Admin: all products regardless of status */
    public function adminGetAll(string $search = ''): array {
        $params = [];
        $where = '';
        if ($search) {
            $where = "WHERE p.name LIKE ? OR p.short_description LIKE ?";
            $s = '%' . $search . '%';
            $params = [$s, $s];
        }
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            {$where}
            ORDER BY p.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
