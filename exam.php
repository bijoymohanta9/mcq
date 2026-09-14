<?php 
include 'inc/header.php';
Session::checkSession();

$userId = Session::get("userid");

// ইউজার অ্যাকাউন্ট সক্রিয় সাবস্ক্রিপশন চেক এবং মেয়াদের তথ্য সংগ্রহ
$subQuery = "SELECT * FROM tbl_subscription 
             WHERE user_id = '$userId' 
             AND status = 'active' 
             AND expire_date >= NOW() 
             ORDER BY id DESC LIMIT 1";

$subData = $db->select($subQuery);

if (!$subData) {
    header("Location: subscription.php");
    exit();
}

$subInfo = $subData->fetch_assoc();
?>

<div class="w-full max-w-4xl mx-auto my-10 px-4">
    
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-3xl p-8 text-white shadow-xl mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <span class="inline-block px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                অনলাইন পরীক্ষা পোর্টালে স্বাগতম
            </span>
            <h2 class="text-3xl font-extrabold">আপনার মডেল টেস্ট শুরু করতে প্রস্তুত?</h2>
            <p class="text-indigo-100 text-sm mt-2 max-w-md">
                নিচে আপনার সাবস্ক্রিপশন প্ল্যানের বিবরণ দেখতে পাচ্ছেন। এখনই পরীক্ষা শুরু করুন এবং আপনার প্রস্তুতি যাচাই করুন।
            </p>
        </div>
        <a href="starttest.php" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-7 py-4 rounded-2xl shadow-lg hover:bg-indigo-50 hover:shadow-xl transition-all whitespace-nowrap text-base">
            <span>পরীক্ষা শুরু করুন</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <!-- Active Subscription Details Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-base">অ্যাক্টিভ সাবস্ক্রিপশন</h3>
                    <p class="text-xs text-slate-500">আপনার বর্তমান প্যাকেজের মেয়াদ ও বিবরণ</p>
                </div>
            </div>
            <span class="bg-emerald-100 text-emerald-700 font-semibold text-xs px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                একটিভ
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center md:text-left">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-medium block uppercase">কোর্স/পরীক্ষা</span>
                <span class="font-bold text-slate-800 uppercase text-sm"><?php echo htmlspecialchars($subInfo['exam_type']); ?></span>
            </div>
            
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-medium block uppercase">প্যাকেজ</span>
                <span class="font-bold text-slate-800 text-sm"><?php echo str_replace('_', ' ', htmlspecialchars($subInfo['duration'])); ?></span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-medium block uppercase">শুরুর তারিখ</span>
                <span class="font-semibold text-slate-700 text-xs"><?php echo date('d M Y', strtotime($subInfo['start_date'])); ?></span>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[11px] text-slate-400 font-medium block uppercase">মেয়াদ শেষ</span>
                <span class="font-semibold text-indigo-600 text-xs"><?php echo date('d M Y', strtotime($subInfo['expire_date'])); ?></span>
            </div>
        </div>
    </div>

</div>

<?php include 'inc/footer.php'; ?>