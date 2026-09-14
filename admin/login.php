<?php 
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/../classes/Admin.php');
    $ad = new Admin();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $adminData = $ad->getAdminData($_POST);
    }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Online Exam System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Font (Inter & Hind Siliguri) -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background Decorative Gradient Blurs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600/20 border border-indigo-500/30 rounded-2xl mb-4 shadow-xl backdrop-blur-md">
                <i class="fa-solid fa-shield-halved text-2xl text-indigo-400"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">অ্যাডমিন কন্ট্রোল প্যানেল</h1>
            <p class="text-slate-400 text-xs mt-1">অনলাইন এক্সাম ম্যানেজমেন্ট সিস্টেম</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            
            <h2 class="text-lg font-semibold text-white mb-6 border-b border-slate-700/60 pb-3 flex items-center justify-between">
                <span>লগইন করুন</span>
                <span class="text-xs bg-indigo-500/10 text-indigo-400 px-2.5 py-1 rounded-full border border-indigo-500/20">Secure Portal</span>
            </h2>

            <!-- Error / Alert Message -->
            <?php if (isset($adminData)): ?>
                <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                    <span><?php echo $adminData; ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Section -->
            <form action="" method="post" class="space-y-5">
                
                <!-- Username Input -->
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-2">ইউজারনেম</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-solid fa-user text-xs"></i>
                        </span>
                        <input type="text" name="adminUser" placeholder="ইউজারনেম লিখুন" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-2">পাসওয়ার্ড</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" name="adminPass" placeholder="••••••••" required 
                               class="w-full pl-11 pr-4 py-3 bg-slate-900/60 border border-slate-700 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="login" 
                        class="w-full mt-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transition-all flex items-center justify-center space-x-2 text-sm">
                    <span>প্রবেশ করুন</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

            </form>

        </div>

        <!-- Footer Footer -->
        <div class="text-center mt-8">
            <p class="text-xs text-slate-500">© 2026 Online Exam System. All rights reserved.</p>
        </div>

    </div>

</body>
</html>