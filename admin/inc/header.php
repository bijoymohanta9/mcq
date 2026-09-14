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
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
<div class="phpcoding flex flex-col min-h-screen">

    <!-- Navbar Header -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-600/30">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="text-white font-bold text-base tracking-wide block">Admin Portal</span>
                        <span class="text-slate-400 text-[11px] -mt-1 block">Online Exam System</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="index.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-house"></i> Home
                    </a>
                    <a href="users.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-users"></i> Manage User
                    </a>
                    <a href="add_category_subject.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-users"></i> Add Category & Subjects
                    </a>
                    <a href="quesadd.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle"></i> Add Ques
                    </a>
                    <a href="queslist.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-list-check"></i> Ques List
                    </a>
                    <a href="subscriptions.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-list-check"></i> Subscriptions
                    </a>
                    <a href="leaderboard.php" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy text-amber-400"></i> Leaderboard
                    </a>
                    <a href="?action=logout" class="ml-2 px-3.5 py-2 rounded-lg text-xs font-semibold text-rose-400 hover:text-white hover:bg-rose-600/80 transition flex items-center gap-1.5 border border-rose-500/20">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <section class="maincontent flex-1 max-w-7xl w-full mx-auto px-4 py-6">