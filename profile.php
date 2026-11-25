<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/inc/header.php');

if (Session::get("login") != true) {
    echo "<div class='max-w-xl mx-auto my-10 p-6 bg-white rounded-2xl border border-slate-200 text-center'><p class='text-sm font-semibold text-slate-700 mb-3'>প্রোফাইল দেখতে আগে লগইন করুন।</p><a href='index.php' class='inline-block px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold'>Login</a></div>";
    include_once($filepath . '/inc/footer.php');
    exit;
}

// সেশন থেকে User ID অথবা Email দিয়ে tbl_user থেকে বর্তমান ইউজার বের করা
$userid = (int)(Session::get("userid") ?? Session::get("user_id") ?? Session::get("id") ?? $_SESSION['userid'] ?? $_SESSION['user_id'] ?? 0);
$userData = false;

if ($userid > 0) {
    $userData = $exm->getUserProfileById($userid);
}

if (!$userData && !empty(Session::get('email'))) {
    $safeEmail = mysqli_real_escape_string($db->link, Session::get('email'));
    $uRes = $db->select("SELECT * FROM tbl_user WHERE email = '$safeEmail' LIMIT 1");
    if ($uRes && $uRes->num_rows > 0) {
        $userData = $uRes->fetch_assoc();
        $userid = (int)($userData['userid'] ?? $userData['id'] ?? 0);
    }
}

$profileMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && $userid > 0) {
    $profileMsg = $exm->updateUserProfileWithExtras($userid, $_POST, $_FILES);
    $userData = $exm->getUserProfileById($userid);
}

$subSummary = $exm->getUserSubscriptionSummary($userid);

$fullName   = $userData['name'] ?? Session::get('name') ?? '';
$userName   = $userData['username'] ?? '';
$emailAddr  = $userData['email'] ?? Session::get('email') ?? '';
$mobNo      = $userData['mob_no'] ?? '';
$genderVal  = $userData['gender'] ?? '';
$pictureVal = $userData['picture'] ?? '';
$picUrl     = !empty($pictureVal) ? 'uploads/users/' . htmlspecialchars(basename($pictureVal), ENT_QUOTES, 'UTF-8') : '';
$initial    = strtoupper(substr($fullName !== '' ? $fullName : 'U', 0, 1));
?>

<div class="w-full max-w-2xl mx-auto my-6 px-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <!-- Header -->
        <div class="flex flex-col items-center text-center mb-6">
            <?php if (!empty($picUrl)) { ?>
                <img src="<?php echo $picUrl; ?>" alt="Profile Picture" class="w-20 h-20 rounded-full object-cover border-2 border-indigo-100 shadow-sm mb-3">
            <?php } else { ?>
                <div class="w-20 h-20 rounded-full bg-indigo-100 text-indigo-700 font-black text-2xl flex items-center justify-center mb-3">
                    <?php echo htmlspecialchars($initial, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>
            <h1 class="text-2xl font-black text-slate-800">Your Profile</h1>
            <p class="text-xs text-slate-500 mt-1">Update and manage your account information</p>
        </div>

        <?php if (!empty($profileMsg)) { ?>
            <div class="mb-5"><?php echo $profileMsg; ?></div>
        <?php } ?>

        <!-- Subscription Status & Quantity (tbl_subscription) -->
        <div class="mb-6 p-4 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Subscription Status</span>
                <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                    <?php if ($subSummary['has_subscription']) { ?>
                        <span class="px-3 py-1 text-xs rounded-full font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                            Subscription Available (<?php echo htmlspecialchars($subSummary['status_label'], ENT_QUOTES, 'UTF-8'); ?>)
                        </span>
                    <?php } else { ?>
                        <span class="px-3 py-1 text-xs rounded-full font-bold bg-rose-100 text-rose-700 border border-rose-200">
                            No Subscription (<?php echo htmlspecialchars($subSummary['status_label'], ENT_QUOTES, 'UTF-8'); ?>)
                        </span>
                    <?php } ?>
                </div>
            </div>

            <div class="text-left sm:text-right text-xs text-slate-600 space-y-1">
                <div>Subscription Quantity: <strong class="text-slate-900"><?php echo (int)$subSummary['total_qty']; ?></strong></div>
                <div>Active Quantity: <strong class="text-emerald-700"><?php echo (int)$subSummary['active_qty']; ?></strong></div>
            </div>
        </div>

        <!-- Profile Form -->
        <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            <!-- Profile Picture -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Picture</label>
                <input 
                    type="file" 
                    name="picture" 
                    accept="image/*" 
                    class="block w-full text-xs text-slate-600 border border-slate-200 rounded-xl p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                >
            </div>

            <!-- Full Name -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Full Name</label>
                <input 
                    type="text" 
                    name="name" 
                    value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>" 
                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
            </div>

            <!-- Username -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    value="<?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>" 
                    readonly 
                    class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed"
                >
            </div>

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($emailAddr, ENT_QUOTES, 'UTF-8'); ?>" 
                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
            </div>

            <!-- Mobile No -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Mobile No</label>
                <input 	type="text" 
						name="mob_no" 
						value="<?php echo htmlspecialchars($mobNo, ENT_QUOTES, 'UTF-8'); ?>" 
						placeholder="017XXXXXXXX" 
						pattern="01[3-9][0-9]{8}" 
						maxlength="11" 
						title="013 থেকে 019 এর মধ্যে ১১ ডিজিটের মোবাইল নম্বর দিন" 
						class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-200"
					>
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Gender</label>
                <select 
                    name="gender" 
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo ($genderVal === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($genderVal === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($genderVal === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    name="update_profile" 
                    class="w-full py-3 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition"
                >
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once('inc/footer.php'); ?>