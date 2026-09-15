<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
    include_once ($filepath.'/../classes/Exam.php');
    $exm = new Exam();

    // ফর্ম সাবমিট হ্যান্ডলিং
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // ১. ক্যাটাগরি সেভ করার লজিক
        if (isset($_POST['add_cat'])) {
            $cat_name = mysqli_real_escape_string($db->link, $_POST['category_name']);
            if(!empty($cat_name)){
                $query = "INSERT INTO tbl_category(category_name) VALUES('$cat_name')";
                $msgCat = $db->insert($query) ? "ক্যাটাগরি সফলভাবে যুক্ত হয়েছে!" : "সমস্যা হয়েছে!";
            } else {
                $msgCat = "ক্যাটাগরির নাম ইনপুট দিন!";
            }
        }
        
        // ২. সাবজেক্ট সেভ করার লজিক
        if (isset($_POST['add_sub'])) {
            $sub_name = mysqli_real_escape_string($db->link, $_POST['subject_name']);
            if(!empty($sub_name)){
                $query = "INSERT INTO tbl_subject(subject_name) VALUES('$sub_name')";
                $msgSub = $db->insert($query) ? "বিষয় সফলভাবে যুক্ত হয়েছে!" : "সমস্যা হয়েছে!";
            } else {
                $msgSub = "বিষয়ের নাম ইনপুট দিন!";
            }
        }
    }
?>

<div class="w-full max-w-4xl mx-auto my-8 px-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">ক্যাটাগরি ও বিষয় যুক্ত করুন</h1>
        <p class="text-slate-500 text-xs mt-1">এখানে নতুন ক্যাটাগরি ও বিষয় এড করলে তা প্রশ্ন যুক্ত করার সময় অপশনে পাওয়া যাবে</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Category Form -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-folder-plus text-indigo-600"></i>
                <span>পরীক্ষার ধরন (Category)</span>
            </h2>

            <?php if (isset($msgCat)): ?>
                <div class="mb-4 text-xs font-semibold text-indigo-700 bg-indigo-50 p-3 rounded-xl border border-indigo-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span><?php echo $msgCat; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">ক্যাটাগরির নাম <span class="text-rose-500">*</span></label>
                    <input type="text" name="category_name" placeholder="যেমন: বিসিএস প্রিলিমিনারি" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>
                <button type="submit" name="add_cat" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>ক্যাটাগরি সেভ করুন</span>
                </button>
            </form>
        </div>

        <!-- Subject Form -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-book text-indigo-600"></i>
                <span>বিষয় (Subject)</span>
            </h2>

            <?php if (isset($msgSub)): ?>
                <div class="mb-4 text-xs font-semibold text-indigo-700 bg-indigo-50 p-3 rounded-xl border border-indigo-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    <span><?php echo $msgSub; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">বিষয়ের নাম <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject_name" placeholder="যেমন: বাংলা ভাষা ও সাহিত্য" required class="w-full bg-slate-50 border border-slate-300 text-slate-800 text-xs rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>
                <button type="submit" name="add_sub" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>বিষয় সেভ করুন</span>
                </button>
            </form>
        </div>

    </div>
</div>

<?php include 'inc/footer.php'; ?>