<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/User.php');
    $usr = new User();
?>

<?php 
    if (isset($_GET['dis'])) {
        $dblid = (int)$_GET['dis'];
        $dblUser = $usr->disableUser($dblid);
    }

    if (isset($_GET['ena'])) {
        $ebllid = (int)$_GET['ena'];
        $eblUser = $usr->enableUser($ebllid);
    }

    if (isset($_GET['del'])) {
        $delid = (int)$_GET['del'];
        $delUser = $usr->deleteUser($delid);
    }
?>

<div class="w-full max-w-6xl mx-auto my-8 px-4">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">ইউজার ম্যানেজমেন্ট</h1>
            <p class="text-slate-500 text-xs mt-1">সকল ব্যবহারকারীর তালিকা, স্ট্যাটাস নিয়ন্ত্রণ ও অ্যাকাউন্ট পরিচালনা করুন</p>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($dblUser) || isset($eblUser) || isset($delUser)): ?>
        <div class="mb-6 p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-circle-info text-indigo-600 text-base"></i>
            <div>
                <?php 
                    if (isset($dblUser)) echo $dblUser;
                    if (isset($eblUser)) echo $eblUser;
                    if (isset($delUser)) echo $delUser;
                ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- User Table Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase font-bold tracking-wider">
                        <th class="py-4 px-6">#</th>
                        <th class="py-4 px-6">নাম</th>
                        <th class="py-4 px-6">ইউজারনেম</th>
                        <th class="py-4 px-6">ইমেইল</th>
                        <th class="py-4 px-6 text-center">স্ট্যাটাস</th>
                        <th class="py-4 px-6 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <?php 
                    $userData = $usr->getAllUser();
                    if ($userData) {
                        $i = 0;
                        while ($result = $userData->fetch_assoc()) {
                            $i++;
                    ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-semibold text-slate-400">
                                <?php echo sprintf('%02d', $i); ?>
                            </td>
                            
                            <td class="py-4 px-6 font-bold text-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs">
                                        <?php echo strtoupper(substr($result['name'], 0, 1)); ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($result['name']); ?></span>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-slate-600 font-medium">
                                @<?php echo htmlspecialchars($result['username']); ?>
                            </td>

                            <td class="py-4 px-6 text-slate-600">
                                <?php echo htmlspecialchars($result['email']); ?>
                            </td>

                            <td class="py-4 px-6 text-center">
                                <?php if ($result['status'] == '0'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        সক্রিয় (Active)
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        নিষ্ক্রিয় (Disabled)
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if ($result['status'] == '0'): ?>
                                        <a onclick="return confirm('আপনি কি নিশ্চিত যে এই ইউজারকে ডিসেবল করবেন?')" 
                                           href="?dis=<?php echo $result['userid'];?>" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg font-semibold transition border border-amber-200" title="Disable User">
                                            <i class="fa-solid fa-user-slash text-[11px]"></i>
                                            <span>ডিসেবল</span>
                                        </a>
                                    <?php else: ?>
                                        <a onclick="return confirm('আপনি কি নিশ্চিত যে এই ইউজারকে এনেবল করবেন?')" 
                                           href="?ena=<?php echo $result['userid'];?>" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg font-semibold transition border border-emerald-200" title="Enable User">
                                            <i class="fa-solid fa-user-check text-[11px]"></i>
                                            <span>এনেবল</span>
                                        </a>
                                    <?php endif; ?>

                                    <a onclick="return confirm('আপনি কি নিশ্চিত যে এই ইউজারকে স্থায়ীভাবে মুছে ফেলবেন?')" 
                                       href="?del=<?php echo $result['userid'];?>" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold transition border border-rose-200" title="Remove User">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        <span>মুছুন</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                কোনো ব্যবহারকারী পাওয়া যায়নি।
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>