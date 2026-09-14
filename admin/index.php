<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/inc/header.php');
?>
<!-- Tailwind CSS & FontAwesome CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Admin Area Clean Reset */
    body { font-family: 'Hind Siliguri', sans-serif !important; background-color: #f8fafc !important; }
    .main { width: 100% !important; max-width: 1000px !important; margin: 0 auto !important; padding: 20px 10px !important; background: transparent !important; border: none !important; box-shadow: none !important; }
    .main h1 { display: none !important; } /* Hide old duplicate title */
    a { text-decoration: none !important; }
</style>

<div class="main">
    
    <!-- Welcome Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-8 text-white shadow-xl mb-8 border border-slate-800 relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-block px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-semibold uppercase tracking-wider mb-3 border border-indigo-500/30">
                অ্যাডমিন কন্ট্রোল প্যানেল
            </span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white !block !m-0">স্বাগতম অ্যাডমিন প্যানেলে</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-2 max-w-2xl leading-relaxed">
                এখানে আপনি অনলাইন এক্সাম সিস্টেমে ব্যবহারকারী, প্রশ্নব্যাংক এবং পরীক্ষা সম্পর্কিত সমস্ত কিছু সহজে পরিচালনা করতে পারবেন।
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Dashboard Quick Access Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Manage Users Card -->
        <a href="users.php" class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:border-indigo-500 transition-all duration-300 block">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-base mb-1 group-hover:text-indigo-600 transition-colors">ইউজার পরিচালনা</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-4">নিবন্ধিত ব্যবহারকারীদের অ্যাকাউন্ট নিয়ন্ত্রণ ও অ্যাক্টিভেশন স্টেটাস দেখুন।</p>
            <div class="flex items-center text-xs font-semibold text-indigo-600 gap-1.5">
                <span>ম্যানেজ করুন</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Add Question Card -->
        <a href="quesadd.php" class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:border-emerald-500 transition-all duration-300 block">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-file-circle-plus text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-base mb-1 group-hover:text-emerald-600 transition-colors">প্রশ্ন যুক্ত করুন</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-4">পরীক্ষার জন্য নতুন বিষয়ভিত্তিক বা ক্যাটাগরি অনুযায়ী প্রশ্ন এন্ট্রি দিন।</p>
            <div class="flex items-center text-xs font-semibold text-emerald-600 gap-1.5">
                <span>প্রশ্ন যোগ করুন</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Question List Card -->
        <a href="queslist.php" class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-lg hover:border-amber-500 transition-all duration-300 block">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-list-check text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-base mb-1 group-hover:text-amber-600 transition-colors">প্রশ্ন তালিকা</h3>
            <p class="text-slate-500 text-xs leading-relaxed mb-4">পূর্বে যুক্ত হওয়া সমস্ত প্রশ্নের তালিকা দেখুন, এডিট বা ডিলিট করুন।</p>
            <div class="flex items-center text-xs font-semibold text-amber-600 gap-1.5">
                <span>তালিকা দেখুন</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

    </div>

</div>

<?php include 'inc/footer.php'; ?>