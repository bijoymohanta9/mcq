<?php include 'inc/header.php'; ?>
<?php
Session::checkSession();
$userid = Session::get("userid");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updateUser = $usr->updateUserData($userid, $_POST);
}
?>

<div class="max-w-xl mx-auto my-10 px-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
        
        <!-- Profile Header -->
        <div class="bg-slate-50 border-b border-slate-100 p-6 text-center">
            <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full mx-auto flex items-center justify-center text-2xl font-bold mb-3 border-2 border-indigo-200">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Your Profile</h1>
            <p class="text-slate-500 text-xs mt-1">Update and manage your account information</p>
        </div>

        <!-- Form Body -->
        <div class="p-6 sm:p-8">

            <?php 
                if (isset($updateUser)) {
                    echo "<div class='mb-6 p-4 rounded-xl text-xs font-semibold border bg-indigo-50 border-indigo-200 text-indigo-800 flex items-center gap-2'>
                            <i class='fa-solid fa-circle-info text-base text-indigo-600'></i>
                            <div>".$updateUser."</div>
                          </div>";
                }
            ?>

            <?php
                $getData = $usr->getUserData($userid);
                if ($getData) {
                    $result = $getData->fetch_assoc();
            ?>
            
            <form action="" method="post" class="space-y-5">
                
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-id-card text-indigo-500 mr-1"></i> Full Name
                    </label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($result['name']); ?>" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold" placeholder="Enter full name" />
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-at text-indigo-500 mr-1"></i> Username
                    </label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($result['username']); ?>" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold" placeholder="Enter username" />
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-envelope text-indigo-500 mr-1"></i> Email Address
                    </label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($result['email']); ?>" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold" placeholder="Enter email address" />
                </div>

                <!-- Action Button -->
                <div class="pt-3">
                    <button type="submit" name="update" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-6 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Update Profile</span>
                    </button>
                </div>

            </form>

            <?php } ?>

        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>