<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Session.php');
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');

class Admin {
    private $db;
    private $fm;

    public function __construct() {
        $this->db = new Database();
        $this->fm = new Format();
    }

    public function getAdminData($data) {
        $adminUserRaw = trim((string)($data['adminUser'] ?? ''));
        $adminPassRaw = trim((string)($data['adminPass'] ?? ''));

        $adminUser = $this->fm->validation($adminUserRaw);
        $adminPass = $this->fm->validation($adminPassRaw);

        if ($adminUser === '' || $adminPass === '') {
            return "ইউজারনেম এবং পাসওয়ার্ড অবশ্যই পূরণ করতে হবে!";
        }

        $safeUser = mysqli_real_escape_string($this->db->link, $adminUser);
        $safePass = mysqli_real_escape_string($this->db->link, md5($adminPass));

        $query = "SELECT * FROM tbl_admin WHERE adminUser = '$safeUser' AND adminPass = '$safePass' LIMIT 1";
        $result = $this->db->select($query);

        if ($result && $result->num_rows > 0) {
            $value = $result->fetch_assoc();
            if (is_array($value)) {
                $dbUser = (string)($value['adminUser'] ?? $adminUser);
                $dbId   = (int)($value['adminId'] ?? 0);
                $role   = strtolower(trim($dbUser));
                if (!in_array($role, array('admin', 'co-admin'), true)) {
                    $role = strtolower(trim($adminUser));
                }

                Session::init();
                Session::set("adminLogin", true);
                Session::set("adminUser", $dbUser);
                Session::set("adminId", $dbId);
                Session::set("adminRole", $role);

                $_SESSION['adminRole'] = $role;
                $_SESSION['adminUser'] = $dbUser;
                $_SESSION['adminId']   = $dbId;
				$filepath = realpath(dirname(__FILE__));
                include_once ($filepath.'/../helpers/AuditLog.php');
                $auditLog = new AuditLog($this->db);
                $auditLog->log('login', "ইউজার ({$dbUser}) সফলভাবে লগইন করেছেন।", array(
                    'adminId'   => $dbId,
                    'adminUser' => $dbUser,
                    'adminRole' => $role,
                    'page_name' => 'login.php'
                ));
                header("Location: index.php");
                exit();
            }
        }

        return "ইউজারনেম অথবা পাসওয়ার্ড সঠিক নয়! আবার চেষ্টা করুন।";
    }
}
?>