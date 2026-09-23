<?php 
    include_once ("../lib/Session.php");
    Session::checkAdminSession();
    include_once ("../lib/Database.php");
    include_once ("../helpers/Format.php");
    
    $db  = new Database();
    $fm  = new Format();

    header("Cache-Control: no-store, no-cache, must-revalidate"); 
    header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
    header("Pragma: no-cache"); 
    header("Expires: Mon, 6 Dec 1977 00:00:00 GMT"); 
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        Session::destroy();
        header("Location:login.php");
        exit();
    }

    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Online Exam System</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- Navbar Header -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Brand Logo -->
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-600/30 group-hover:bg-indigo-500 transition">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="text-white font-bold text-base tracking-wide block">Admin Portal</span>
                        <span class="text-slate-400 text-[11px] -mt-1 block">Online Exam System</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-1">
                    <a href="index.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'index.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                    <a href="users.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'users.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-users"></i> Users
                    </a>
                    <a href="add_category_subject.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'add_category_subject.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-layer-group"></i> Category & Subject
                    </a>
                    <a href="quesadd.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'quesadd.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-plus-circle"></i> Add Ques
                    </a>
                    <a href="queslist.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'queslist.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-list-check"></i> Ques List
                    </a>

                    <!-- Circular Management Links -->
                    <a href="circular_add.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 border border-indigo-500/20 <?php echo ($currentPage == 'circular_add.php') ? 'text-white bg-indigo-900/60' : 'text-indigo-300 hover:text-white hover:bg-indigo-950/50'; ?>">
                        <i class="fa-solid fa-file-circle-plus"></i> Add Circular
                    </a>
                    <a href="circular_list.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 border border-indigo-500/20 <?php echo ($currentPage == 'circular_list.php') ? 'text-white bg-indigo-900/60' : 'text-indigo-300 hover:text-white hover:bg-indigo-950/50'; ?>">
                        <i class="fa-solid fa-briefcase"></i> Circular List
                    </a>

                    <a href="subscriptions.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'subscriptions.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-gem text-amber-400"></i> Subscriptions
                    </a>
                    <a href="leaderboard.php" class="px-3 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 <?php echo ($currentPage == 'leaderboard.php') ? 'text-white bg-slate-800' : 'text-slate-300 hover:text-white hover:bg-slate-800/60'; ?>">
                        <i class="fa-solid fa-trophy text-amber-400"></i> Leaderboard
                    </a>
                    <a href="?action=logout" class="ml-2 px-3 py-2 rounded-lg text-xs font-semibold text-rose-400 hover:text-white hover:bg-rose-600/80 transition flex items-center gap-1.5 border border-rose-500/20">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="adminMobileBtn" class="lg:hidden p-2 text-slate-300 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="adminMobileMenu" class="hidden lg:hidden border-t border-slate-800 bg-slate-900 px-4 pt-3 pb-4 space-y-1.5">
            <a href="index.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-house w-5"></i> Home
            </a>
            <a href="users.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-users w-5"></i> Users
            </a>
            <a href="add_category_subject.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-layer-group w-5"></i> Category & Subject
            </a>
            <a href="quesadd.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-plus-circle w-5"></i> Add Ques
            </a>
            <a href="queslist.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-list-check w-5"></i> Ques List
            </a>
            <a href="circular_add.php" class="block px-3 py-2 text-xs font-semibold text-indigo-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-file-circle-plus w-5"></i> Add Circular
            </a>
            <a href="circular_list.php" class="block px-3 py-2 text-xs font-semibold text-indigo-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-briefcase w-5"></i> Circular List
            </a>
            <a href="subscriptions.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-gem text-amber-400 w-5"></i> Subscriptions
            </a>
            <a href="leaderboard.php" class="block px-3 py-2 text-xs font-semibold text-slate-300 hover:bg-slate-800 rounded-lg">
                <i class="fa-solid fa-trophy text-amber-400 w-5"></i> Leaderboard
            </a>
            <a href="?action=logout" class="block px-3 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-900/40 rounded-lg">
                <i class="fa-solid fa-right-from-bracket w-5"></i> Logout
            </a>
        </div>
    </header>

    <script>
        const adminMobileBtn = document.getElementById('adminMobileBtn');
        const adminMobileMenu = document.getElementById('adminMobileMenu');

        if (adminMobileBtn && adminMobileMenu) {
            adminMobileBtn.addEventListener('click', () => {
                adminMobileMenu.classList.toggle('hidden');
            });
        }
    </script>

    <!-- Main Content Wrapper -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-6">