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

$targetAdminId = is_array($adminRecord) ? (int)($adminRecord['adminId'] ?? 0) : 0;

// প্রোফাইল আপডেট হ্যান্ডলার
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nameInput  = trim((string)$fm->validation($_POST['name'] ?? ''));
    $mobInput   = trim((string)$fm->validation($_POST['mob_no'] ?? ''));
    $emailInput = trim((string)$fm->validation($_POST['email'] ?? ''));

    if ($nameInput === '') {
        $msg = "নাম অবশ্যই লিখতে হবে!";
        $msgType = 'error';
    } elseif ($mobInput !== '' && $mobInput !== '0' && !preg_match('/^01[3-9][0-9]{8}$/', $mobInput)) {
        $msg = "সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (যেমন: 017XXXXXXXX)!";
        $msgType = 'error';
    } elseif ($emailInput !== '' && $emailInput !== '0' && !filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        $msg = "সঠিক ইমেইল ঠিকানা দিন!";
        $msgType = 'error';
    } else {
        $existingPicRaw = is_array($adminRecord) ? trim((string)($adminRecord['pic'] ?? '')) : '';
        $newPicName = ($existingPicRaw !== '' && $existingPicRaw !== '0') ? basename($existingPicRaw) : '';

        // নতুন ছবি আপলোড হলে uploads/admins/ ফোল্ডারে সেভ এবং tbl_admin.pic এ শুধু ফাইলের নাম রাখা
        if (isset($_FILES['pic']) && isset($_FILES['pic']['error']) && $_FILES['pic']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['pic']['error'] === UPLOAD_ERR_OK) {
                $allowedExt = array('jpg', 'jpeg', 'png', 'webp');
                $origName   = (string)$_FILES['pic']['name'];
                $ext        = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                $fileSize   = (int)$_FILES['pic']['size'];

                if (!in_array($ext, $allowedExt, true)) {
                    $msg = "শুধুমাত্র JPG, JPEG, PNG অথবা WEBP ছবি আপলোড করা যাবে!";
                    $msgType = 'error';
                } elseif ($fileSize > 2 * 1024 * 1024) {
                    $msg = "ছবির সাইজ ২ MB এর মধ্যে হতে হবে!";
                    $msgType = 'error';
                } else {
                    $uploadDir = __DIR__ . '/../uploads/admins';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $uniqueFileName = 'admin_' . ($targetAdminId > 0 ? $targetAdminId : $adminRole) . '_' . time() . '.' . $ext;
                    $destPath       = $uploadDir . '/' . $uniqueFileName;

                    if (move_uploaded_file($_FILES['pic']['tmp_name'], $destPath)) {
                        if ($newPicName !== '') {
                            $oldFile = $uploadDir . '/' . basename($newPicName);
                            if (file_exists($oldFile) && is_file($oldFile)) {
                                @unlink($oldFile);
                            }
                        }
                        $newPicName = $uniqueFileName;
                    } else {
                        $msg = "ছবি আপলোড করতে সমস্যা হয়েছে!";
                        $msgType = 'error';
                    }
                }
            } else {
                $msg = "ছবি আপলোডে ত্রুটি হয়েছে!";
                $msgType = 'error';
            }
        }

        if ($msgType !== 'error' && $targetAdminId > 0) {
            $safeName  = mysqli_real_escape_string($db->link, $nameInput);
            $safeMob   = mysqli_real_escape_string($db->link, $mobInput);
            $safeEmail = mysqli_real_escape_string($db->link, $emailInput);
            $safePic   = mysqli_real_escape_string($db->link, $newPicName);

            $updateQuery = "UPDATE tbl_admin 
                            SET name = '$safeName', 
                                mob_no = '$safeMob', 
                                email = '$safeEmail', 
                                pic = '$safePic' 
                            WHERE adminId = '$targetAdminId' LIMIT 1";

            $updated = $db->update($updateQuery);
            if ($updated) {
				$audit->log('profile_update', "প্রোফাইল তথ্য আপডেট করা হয়েছে (নাম: {$nameInput})।");
                $msg = "প্রোফাইল সফলভাবে আপডেট হয়েছে!";
                $msgType = 'success';

                // আপডেট হওয়া তথ্য আবার লোড করা
                $res = $db->select("SELECT * FROM tbl_admin WHERE adminId = '$targetAdminId' LIMIT 1");
                if ($res && $res->num_rows > 0) {
                    $adminRecord = $res->fetch_assoc();
                }
            } else {
                $msg = "প্রোফাইল আপডেট করা যায়নি! আবার চেষ্টা করুন।";
                $msgType = 'error';
            }
        } elseif ($msgType !== 'error') {
            $msg = "অ্যাডমিন রেকর্ড খুঁজে পাওয়া যায়নি!";
            $msgType = 'error';
        }
    }
}

$profileUsername = is_array($adminRecord) ? (string)($adminRecord['adminUser'] ?? $adminRole) : $adminRole;
$profileName     = is_array($adminRecord) ? (string)($adminRecord['name'] ?? '') : '';
$profileMob      = is_array($adminRecord) ? (string)($adminRecord['mob_no'] ?? '') : '';
if ($profileMob === '0') {
    $profileMob = '';
}
$profileEmail    = is_array($adminRecord) ? (string)($adminRecord['email'] ?? '') : '';
if ($profileEmail === '0') {
    $profileEmail = '';
}
$profilePicRaw   = is_array($adminRecord) ? trim((string)($adminRecord['pic'] ?? '')) : '';
$profilePicName  = ($profilePicRaw !== '' && $profilePicRaw !== '0') ? basename($profilePicRaw) : '';
$profilePicUrl   = ($profilePicName !== '' && file_exists(__DIR__ . '/../uploads/admins/' . $profilePicName))
    ? '../uploads/admins/' . rawurlencode($profilePicName)
    : '';
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4 space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-indigo-600"></i>
                <span>Update Profile</span>
            </h1>
            <p class="text-slate-500 text-xs mt-1">আপনার অ্যাডমিন / কো-অ্যাডমিন প্রোফাইল তথ্য ও ছবি আপডেট করুন</p>
        </div>
        <a href="index.php" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-sm transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left"></i><span>ড্যাশবোর্ডে ফিরুন</span>
        </a>
    </div>

    <!-- Alert Message -->
    <?php if ($msg !== ''): ?>
        <div class="p-4 rounded-xl text-xs font-bold border flex items-center gap-2 <?php echo ($msgType === 'success') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'; ?>">
            <i class="fa-solid <?php echo ($msgType === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> text-sm"></i>
            <span><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    <?php endif; ?>

    <!-- Profile Form Card -->
    <form action="" method="post" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">

        <!-- Top Avatar & Role Summary -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-5 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                <?php if ($profilePicUrl !== ''): ?>
                    <img src="<?php echo htmlspecialchars($profilePicUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Picture" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fa-solid fa-user-shield text-2xl text-indigo-600"></i>
                <?php endif; ?>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base font-extrabold text-slate-800">
                        <?php echo htmlspecialchars($profileName !== '' ? $profileName : $profileUsername, ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo ($adminRole === 'co-admin') ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-indigo-100 text-indigo-800 border border-indigo-200'; ?>">
                        <?php echo htmlspecialchars($profileUsername, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">ইউজারনেম পরিবর্তনযোগ্য নয়; নিচের তথ্যগুলো এডিট করে আপডেট করুন।</p>
            </div>
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">ইউজারনেম (Username)</label>
                <input type="text" value="<?php echo htmlspecialchars($profileUsername, ENT_QUOTES, 'UTF-8'); ?>" readonly class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-xs font-semibold text-slate-500 cursor-not-allowed">
            </div>

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-2">নাম <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($profileName, ENT_QUOTES, 'UTF-8'); ?>" placeholder="আপনার নাম লিখুন" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div>
                <label for="mob_no" class="block text-xs font-bold text-slate-700 mb-2">মোবাইল নম্বর</label>
                <input type="text" name="mob_no" id="mob_no" value="<?php echo htmlspecialchars($profileMob, ENT_QUOTES, 'UTF-8'); ?>" placeholder="017XXXXXXXX" pattern="01[3-9][0-9]{8}" maxlength="11" title="013 থেকে 019 এর মধ্যে ১১ ডিজিটের মোবাইল নম্বর দিন" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-2">ইমেইল ঠিকানা</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($profileEmail, ENT_QUOTES, 'UTF-8'); ?>" placeholder="example@mail.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200">
            </div>
        </div>

        <div>
            <label for="pic" class="block text-xs font-bold text-slate-700 mb-2">প্রোফাইল ছবি (JPG, JPEG, PNG, WEBP - সর্বোচ্চ ২ MB)</label>
            <input type="file" name="pic" id="pic" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <!-- Update Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" name="update_profile" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md transition">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>আপডেট করুন</span>
            </button>
        </div>

    </form>

</div>

<?php include 'inc/footer.php'; ?>