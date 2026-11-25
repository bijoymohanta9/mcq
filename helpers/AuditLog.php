<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Database.php');

class AuditLog {
    private $db;

    public function __construct($existingDb = null) {
        if ($existingDb instanceof Database) {
            $this->db = $existingDb;
        } else {
            $this->db = new Database();
        }
        $this->ensureTable();
    }

    private function ensureTable() {
        if (!$this->db || !$this->db->link) {
            return;
        }
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_audit_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `adminId` int(11) NOT NULL DEFAULT 0,
            `adminUser` varchar(100) NOT NULL DEFAULT '',
            `adminRole` varchar(50) NOT NULL DEFAULT 'admin',
            `action_type` varchar(50) NOT NULL DEFAULT 'info',
            `description` text NOT NULL,
            `page_name` varchar(120) NOT NULL DEFAULT '',
            `ip_address` varchar(64) NOT NULL DEFAULT '',
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_role_action` (`adminRole`, `action_type`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        @mysqli_query($this->db->link, $sql);
    }

    private function getClientIp() {
        $ipRaw = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        if (strpos($ipRaw, ',') !== false) {
            $parts = explode(',', $ipRaw);
            $ipRaw = trim($parts[0]);
        }
        return trim((string)$ipRaw);
    }

    public function log($actionType, $description, $overrideMeta = array()) {
        if (!$this->db || !$this->db->link) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        $adminId = isset($overrideMeta['adminId'])
            ? (int)$overrideMeta['adminId']
            : (int)($_SESSION['adminId'] ?? 0);

        $adminUser = isset($overrideMeta['adminUser'])
            ? trim((string)$overrideMeta['adminUser'])
            : trim((string)($_SESSION['adminUser'] ?? ''));

        $rawRole = $overrideMeta['adminRole']
            ?? $_SESSION['adminRole']
            ?? ($adminUser !== '' ? $adminUser : 'admin');
        $adminRole = strtolower(trim((string)$rawRole));
        if (!in_array($adminRole, array('admin', 'co-admin', 'subscriber'), true)) {
            $adminRole = ($adminUser !== '') ? $adminUser : 'admin';
        }

        $typeClean = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower(trim((string)$actionType)));
        if ($typeClean === '') {
            $typeClean = 'info';
        }

        $descClean = trim((string)$description);
        if ($descClean === '') {
            return false;
        }

        $pageName = isset($overrideMeta['page_name'])
            ? basename((string)$overrideMeta['page_name'])
            : basename((string)($_SERVER['PHP_SELF'] ?? ''));

        $ipClean = $this->getClientIp();

        $safeUser = mysqli_real_escape_string($this->db->link, $adminUser);
        $safeRole = mysqli_real_escape_string($this->db->link, $adminRole);
        $safeType = mysqli_real_escape_string($this->db->link, $typeClean);
        $safeDesc = mysqli_real_escape_string($this->db->link, $descClean);
        $safePage = mysqli_real_escape_string($this->db->link, $pageName);
        $safeIp   = mysqli_real_escape_string($this->db->link, $ipClean);

        $query = "INSERT INTO tbl_audit_log (adminId, adminUser, adminRole, action_type, description, page_name, ip_address)
                  VALUES ('$adminId', '$safeUser', '$safeRole', '$safeType', '$safeDesc', '$safePage', '$safeIp')";

        return (bool)$this->db->insert($query);
    }

    public function logSubscriptionActivity($subAction, $description, $meta = array()) {
        if (!$this->db || !$this->db->link) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        $actorRole = strtolower(trim((string)($meta['actor_role'] ?? 'subscriber')));
        if (!in_array($actorRole, array('subscriber', 'admin', 'co-admin'), true)) {
            $actorRole = 'subscriber';
        }

        $userId   = isset($meta['user_id']) ? (int)$meta['user_id'] : (int)($_SESSION['userid'] ?? 0);
        $userName = trim((string)($meta['user_name'] ?? ''));
        $username = trim((string)($meta['username'] ?? ''));
        $email    = trim((string)($meta['email'] ?? ''));

        $actorName = trim((string)($meta['actor_name'] ?? ''));
        if ($actorName === '') {
            if ($actorRole === 'subscriber') {
                $actorName = $userName !== '' ? $userName : ($username !== '' ? $username : ($email !== '' ? $email : 'User #' . $userId));
            } else {
                $actorName = trim((string)($_SESSION['adminUser'] ?? $_SESSION['adminRole'] ?? $actorRole));
            }
        }

        $cleanAct = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower(trim((string)$subAction)));
        if ($cleanAct === '') {
            $cleanAct = 'activity';
        }
        $actionType = 'sub_' . $cleanAct;

        $parts = array();
        if ($userName !== '' || $userId > 0) {
            $uLabel = ($userName !== '' ? $userName : 'User');
            if ($username !== '') {
                $uLabel .= " (@{$username})";
            }
            if ($userId > 0) {
                $uLabel .= " [ID: {$userId}]";
            }
            $parts[] = "সাবস্ক্রাইবার: {$uLabel}";
        }
        if ($email !== '') {
            $parts[] = "ইমেইল: {$email}";
        }
        if (!empty($meta['exam_type'])) {
            $parts[] = "প্যাকেজ: " . trim((string)$meta['exam_type']);
        }
        if (!empty($meta['duration'])) {
            $parts[] = "মেয়াদ: " . trim((string)$meta['duration']);
        }
        if (isset($meta['amount']) && $meta['amount'] !== '') {
            $parts[] = "টাকা: ৳" . number_format((float)$meta['amount'], 2);
        }
        if (!empty($meta['payment_method'])) {
            $methodInfo = trim((string)$meta['payment_method']);
            if (!empty($meta['trx_id'])) {
                $methodInfo .= " | TrxID: " . trim((string)$meta['trx_id']);
            }
            if (!empty($meta['sender_number'])) {
                $methodInfo .= " | Sender: " . trim((string)$meta['sender_number']);
            }
            $parts[] = "পেমেন্ট: {$methodInfo}";
        }

        $descText = trim((string)$description);
        if ($descText !== '') {
            $parts[] = $descText;
        }

        $fullDesc = implode(' • ', $parts);
        if ($fullDesc === '') {
            return false;
        }

        $pageName = isset($meta['page_name'])
            ? basename((string)$meta['page_name'])
            : basename((string)($_SERVER['PHP_SELF'] ?? ''));

        $ipClean = $this->getClientIp();

        $actorIdVal = ($actorRole === 'subscriber')
            ? $userId
            : (int)($meta['adminId'] ?? ($_SESSION['adminId'] ?? 0));

        $safeUser = mysqli_real_escape_string($this->db->link, $actorName);
        $safeRole = mysqli_real_escape_string($this->db->link, $actorRole);
        $safeType = mysqli_real_escape_string($this->db->link, $actionType);
        $safeDesc = mysqli_real_escape_string($this->db->link, $fullDesc);
        $safePage = mysqli_real_escape_string($this->db->link, $pageName);
        $safeIp   = mysqli_real_escape_string($this->db->link, $ipClean);

        $query = "INSERT INTO tbl_audit_log (adminId, adminUser, adminRole, action_type, description, page_name, ip_address)
                  VALUES ('$actorIdVal', '$safeUser', '$safeRole', '$safeType', '$safeDesc', '$safePage', '$safeIp')";

        return (bool)$this->db->insert($query);
    }
}
?>