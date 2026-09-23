<?php
    ob_start();
    $filepath = realpath(dirname(__FILE__));
    include_once ($filepath.'/../lib/Session.php');
    Session::init();
    include_once ($filepath.'/../lib/Database.php');
    include_once ($filepath.'/../helpers/Format.php');
    
    spl_autoload_register(function($class){
        include_once "classes/".$class.".php";
    });
    
    $db = new Database();
    $fm = new Format();
    $usr = new User();
    $exm = new Exam();
    $pro = new Process();

    // Logout handling before HTML output
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        Session::destroy();
        header("Location: index.php");
        exit();
    }

    // Active page helper
    $currentPage = basename($_SERVER['PHP_SELF']);

    header("Cache-Control: no-store, no-cache, must-revalidate"); 
    header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
    header("Pragma: no-cache"); 
    header("Expires: Mon, 6 Dec 1977 00:00:00 GMT"); 
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam System</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/main.css">
    
    <script src="js/jquery.js"></script>
    <script src="js/main.js"></script>
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-between text-slate-800">

    <!-- Main Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md shadow-sm border-b border-slate-200/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo / Brand -->
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="bg-indigo-600 group-hover:bg-indigo-700 text-white p-2 rounded-xl shadow-md transition-all duration-200">
                        <i class="fa-solid fa-graduation-cap text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-slate-900 tracking-tight leading-none group-hover:text-indigo-600 transition">ExamPrep</h1>
                        <span class="text-[10px] text-slate-500 font-medium tracking-wide uppercase">Job Assessment</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="circulars.php" class="px-3 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 <?php echo ($currentPage == 'circulars.php') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50'; ?>">
                        <i class="fa-solid fa-briefcase text-indigo-500"></i> সার্কুলার
                    </a>

                    <?php if (Session::get("login") == true) { ?>
                        <a href="exam.php" class="px-3 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 <?php echo ($currentPage == 'exam.php') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50'; ?>">
                            <i class="fa-solid fa-file-pen"></i> পরীক্ষা দিন
                        </a>

                        <a href="exam_history.php" class="px-3 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 <?php echo ($currentPage == 'exam_history.php') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 hover:text-indigo-600 hover:bg-slate-50'; ?>">
                            <i class="fa-solid fa-clock-rotate-left"></i> রেকর্ডস
                        </a>

                        <a href="leaderboard.php" class="px-3 py-2 text-xs font-semibold rounded-lg transition-all flex items-center gap-1.5 <?php echo ($currentPage == 'leaderboard.php') ? 'text-amber-600 bg-amber-50' : 'text-slate-600 hover:text-amber-600 hover:bg-amber-50'; ?>">
                            <i class="fa-solid fa-trophy text-amber-500"></i> লিডারবোর্ড
                        </a>
                    <?php } ?>
                </nav>

                <!-- Right Side Actions & User Menu -->
                <div class="flex items-center space-x-3">
                    <?php if (Session::get("login") == true) { ?>
                        
                        <!-- Primary Action: Buy Subscription -->
                        <a href="subscription.php" class="px-3.5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 rounded-xl transition shadow-sm hover:shadow flex items-center gap-1.5">
                            <i class="fa-solid fa-gem text-amber-300"></i> Plan কিনুন
                        </a>

                        <!-- User Profile Dropdown Menu -->
                        <div class="relative" id="userMenuWrapper">
                            <button id="userMenuBtn" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-slate-100 border border-transparent hover:border-slate-200 transition focus:outline-none">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center border border-indigo-200">
                                    <?php echo strtoupper(substr(Session::get('name'), 0, 1)); ?>
                                </div>
                                <span class="hidden lg:inline-block text-xs font-semibold text-slate-700 max-w-[100px] truncate">
                                    <?php echo Session::get('name'); ?>
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>

                            <!-- Dropdown Box -->
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 animate-fadeIn">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-[11px] text-slate-400 font-medium">Logged in as</p>
                                    <p class="text-xs font-bold text-slate-800 truncate"><?php echo Session::get('name'); ?></p>
                                </div>
                                
                                <a href="profile.php" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                                    <i class="fa-regular fa-user w-4 text-slate-400"></i> Profile
                                </a>
                                
                                <a href="my_subscriptions.php" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition">
                                    <i class="fa-solid fa-receipt w-4 text-slate-400"></i> Subscriptions
                                </a>

                                <div class="border-t border-slate-100 my-1"></div>

                                <a href="?action=logout" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                    <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                                </a>
                            </div>
                        </div>

                    <?php } else { ?>
                        <a href="register.php" class="px-3 py-2 text-xs font-semibold text-slate-600 hover:text-indigo-600 transition">Register</a>
                        <a href="index.php" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition">Login</a>
                    <?php } ?>

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="md:hidden p-2 text-slate-600 hover:text-indigo-600 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-2">
            <a href="circulars.php" class="block px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">
                <i class="fa-solid fa-briefcase text-indigo-500 mr-2"></i> সরকারি সার্কুলার
            </a>
            <?php if (Session::get("login") == true) { ?>
                <a href="exam.php" class="block px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-file-pen text-slate-400 mr-2"></i> পরীক্ষা দিন
                </a>
                <a href="exam_history.php" class="block px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400 mr-2"></i> রেকর্ডস
                </a>
                <a href="leaderboard.php" class="block px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">
                    <i class="fa-solid fa-trophy text-amber-500 mr-2"></i> লিডারবোর্ড
                </a>
            <?php } ?>
        </div>
    </header>

    <script>
        // User Profile Dropdown Toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>

    <!-- Main Content Wrapper -->
    <!-- Main Content Wrapper -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">