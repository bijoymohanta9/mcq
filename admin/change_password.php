
<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/inc/header.php');

$msg     = '';
$msgType = 'success';

// বর্তমান লগইন করা admin বা co-admin এর তথ্য আনা
$adminRecord = false;
if ($sessionAdminId > 0) {
    $res = $db->select("SELECT * FROM tbl_admin WHERE adminId = '$sessionAdminId' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $adminRecord = $res->fetch_assoc();
    }
}
if (!$adminRecord && $sessionAdminUser !== '') {
    $safeU = mysqli_real_escape_string($db->link, $sessionAdminUser);
    $res = $db->select("SELECT * FROM tbl_admin WHERE adminUser = '$safeU' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $adminRecord = $res->fetch_assoc();
    }
}
if (!$adminRecord) {
    $safeR = mysqli_real_escape_string($db->link, $adminRole);
    $res = $db->select("SELECT * FROM tbl_admin WHERE adminUser = '$safeR' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $adminRecord = $res->fetch_assoc();
    }
}

$targetAdminId  = is_array($adminRecord) ? (int)($adminRecord['adminId'] ?? 0) : 0;
$currentDbPass  = is_array($adminRecord) ? (string)($adminRecord['adminPass'] ?? '') : '';
$profileUsername = is_array($adminRecord) ? (string)($adminRecord['adminUser'] ?? $adminRole) : $adminRole;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPass = (string)($_POST['current_password'] ?? '');
    $newPass     = (string)($_POST['new_password'] ?? '');
    $confirmPass = (string)($_POST['confirm_password'] ?? '');

    if ($currentPass === '' || $newPass === '' || $confirmPass === '') {
        $msg = "সবগুলো পাসওয়ার্ড ফিল্ড পূরণ করুন!";
        $msgType = 'error';
    } elseif (md5($currentPass) !== $currentDbPass) {
        $msg = "বর্তমান পাসওয়ার্ড সঠিক নয়!";
        $msgType = 'error';
    } elseif (strlen($newPass) < 6) {
        $msg = "নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে!";
        $msgType = 'error';
    } elseif ($newPass !== $confirmPass) {
        $msg = "নতুন পাসওয়ার্ড এবং কনফার্ম পাসওয়ার্ড মিলছে না!";
        $msgType = 'error';
    } elseif (md5($newPass) === $currentDbPass) {
        $msg = "নতুন পাসওয়ার্ডটি পুরনো পাসওয়ার্ড থেকে ভিন্ন হতে হবে!";
        $msgType = 'error';
    } elseif ($targetAdminId <= 0) {
        $msg = "অ্যাডমিন রেকর্ড খুঁজে পাওয়া যায়নি!";
        $msgType = 'error';
    } else {
        $newHashedPass = mysqli_real_escape_string($db->link, md5($newPass));
        $updated = $db->update("UPDATE tbl_admin SET adminPass = '$newHashedPass' WHERE adminId = '$targetAdminId' LIMIT 1");

        if ($updated) {
			$audit->log('password_change', "অ্যাকাউন্টের পাসওয়ার্ড পরিবর্তন করা হয়েছে।");
            $currentDbPass = md5($newPass);
            $msg = "পাসওয়ার্ড সফলভাবে পরিবর্তন হয়েছে!";
            $msgType = 'success';
        } else {
            $msg = "পাসওয়ার্ড পরিবর্তন করা যায়নি! আবার চেষ্টা করুন।";
            $msgType = 'error';
        }
    }
}
?>

<div class="w-full max-w-2xl mx-auto my-8 px-4 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-key text-amber-500"></i>
                <span>Change Password</span>
            </h1>
            <p class="text-slate-500 text-xs mt-1">
                লগইন করা অ্যাকাউন্ট (<strong class="text-slate-700"><?php echo htmlspecialchars($profileUsername, ENT_QUOTES, 'UTF-8'); ?></strong>) এর পাসওয়ার্ড পরিবর্তন করুন
            </p>
        </div>
        <a href="admin_profile.php" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-user-pen"></i><span>Update Profile</span>
        </a>
    </div>

    <!-- Alert Message -->
    <?php if ($msg !== ''): ?>
        <div class="p-4 rounded-xl text-xs font-bold border flex items-center gap-2 <?php echo ($msgType === 'success') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'; ?>">
            <i class="fa-solid <?php echo ($msgType === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> text-sm"></i>
            <span><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    <?php endif; ?>

    <!-- Change Password Form Card -->
    <form action="" method="post" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">

        <div>
            <label for="current_password" class="block text-xs font-bold text-slate-700 mb-2">বর্তমান পাসওয়ার্ড <span class="text-rose-500">*</span></label>
            <input type="password" name="current_password" id="current_password" required placeholder="বর্তমান পাসওয়ার্ড লিখুন" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>

        <div>
            <label for="new_password" class="block text-xs font-bold text-slate-700 mb-2">নতুন পাসওয়ার্ড <span class="text-rose-500">*</span></label>
            <input type="password" name="new_password" id="new_password" required minlength="6" placeholder="কমপক্ষে ৬ অক্ষরের নতুন পাসওয়ার্ড লিখুন" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>

        <div>
            <label for="confirm_password" class="block text-xs font-bold text-slate-700 mb-2">নতুন পাসওয়ার্ড নিশ্চিত করুন <span class="text-rose-500">*</span></label>
            <input type="password" name="confirm_password" id="confirm_password" required minlength="6" placeholder="নতুন পাসওয়ার্ডটি আবার লিখুন" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" name="change_password" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                <i class="fa-solid fa-lock"></i>
                <span>পাসওয়ার্ড পরিবর্তন করুন</span>
            </button>
        </div>

    </form>

</div>

<?php include 'inc/footer.php'; ?>