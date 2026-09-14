<?php
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

header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: pre-check=0, post-check=0, max-age=0"); 
header("Pragma: no-cache"); 
header("Expires: Mon, 6 Dec 1977 00:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam System</title>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="no-cache">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="Cache-Control" content="no-cache">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    
    <script src="js/jquery.js"></script>
    <script src="js/main.js"></script>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col justify-between font-sans text-slate-800">

    <?php 
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        Session::destroy();
        header("Location:index.php");
        exit();
    }
    ?>

    <!-- Main Navigation Header -->
    <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap justify-between items-center gap-4">
            
            <!-- Logo / Brand -->
            <a href="index.php" class="flex items-center space-x-3">
                <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-md">
                    <i class="fa-solid fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 tracking-tight leading-none">Online Exam System</h1>
                    <span class="text-xs text-slate-500">PHP OOP & MySQLi</span>
                </div>
            </a>

            <!-- Navigation Links & User Welcome -->
            <div class="flex items-center space-x-4">
                <nav class="flex items-center space-x-1 sm:space-x-2">
                    <?php
                    $login = Session::get("login");
                    if ($login == true) { ?>
                        <a href="profile.php" class="px-3 py-2 text-sm font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition flex items-center gap-1.5">
                            <i class="fa-regular fa-user text-slate-400"></i> Profile
                        </a>
                        <a href="exam.php" class="px-3 py-2 text-sm font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition flex items-center gap-1.5">
                            <i class="fa-solid fa-file-pen text-slate-400"></i> Exam
                        </a>
                        <a href="?action=logout" class="px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition flex items-center gap-1.5">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    <?php } else { ?>
                        <a href="register.php" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition">Register</a>
                        <a href="index.php" class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">Login</a>
                    <?php } ?>
                </nav>

                <?php if ($login == true) { ?>
                    <div class="hidden md:flex items-center pl-4 border-l border-slate-200 text-xs text-slate-500">
                        <span>Welcome,&nbsp;<strong class="text-slate-800 font-semibold"><?php echo Session::get('name'); ?></strong></span>
                    </div>
                <?php } ?>
            </div>

        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 md:p-8">