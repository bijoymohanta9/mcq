<?php 
 $filepath = realpath(dirname(__FILE__));
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');

class Exam{
	private $db;
	private $fm;
	function __construct()
	{
		$this->db = new Database();
		$this->fm = new Format();
	}

    public function getCategories() {
    $query = "SELECT * FROM tbl_category ORDER BY id ASC";
    return $this->db->select($query);
}

public function addQuestions($data) {
    $category_id = $this->fm->validation($data['category_id']);
    $subject_id  = $this->fm->validation($data['subject_id']);
    $quesNo      = $this->fm->validation($data['quesNo']);
    $ques        = mysqli_real_escape_string($this->db->link, $data['ques']);
    
    $ans = array();
    $ans[1] = $this->fm->validation($data['ans1']);
    $ans[2] = $this->fm->validation($data['ans2']);
    $ans[3] = $this->fm->validation($data['ans3']);
    $ans[4] = $this->fm->validation($data['ans4']);
    $rightAns = $this->fm->validation($data['rightAns']);

    $query = "INSERT INTO tbl_ques(quesNo, category_id, subject_id, ques) 
              VALUES('$quesNo', '$category_id', '$subject_id', '$ques')";
    
    $insert_ques = mysqli_query($this->db->link, $query);

    if ($insert_ques) {
        $inserted_ques_id = mysqli_insert_id($this->db->link);

        foreach ($ans as $key => $ansName) {
            if (!empty($ansName)) {
                $rAns = ($rightAns == $key) ? '1' : '0';
                $ansNameEscaped = mysqli_real_escape_string($this->db->link, $ansName);
                
                $ansQuery = "INSERT INTO tbl_ans(quesNo, rightAns, ans) 
                             VALUES('$inserted_ques_id', '$rAns', '$ansNameEscaped')";
                
                mysqli_query($this->db->link, $ansQuery);
            }
        }

        return "<div class='p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200'>প্রশ্ন ও উত্তরসমূহ সফলভাবে সেভ হয়েছে!</div>";
    } else {
        return "<div class='p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200'>এরর: " . mysqli_error($this->db->link) . "</div>";
    }
}

public function importBulkQuestions($fileData) {
    if (!isset($fileData['csv_file']) || $fileData['csv_file']['error'] != 0) {
        return "<div class='p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold'>সঠিক CSV ফাইল আপলোড করুন!</div>";
    }

    $fileName = $fileData['csv_file']['tmp_name'];
    $fileSize = $fileData['csv_file']['size'];

    if ($fileSize > 0) {
        $file = fopen($fileName, "r");
        fgetcsv($file);

        $insertedCount = 0;

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            if (empty(array_filter($column))) {
                continue;
            }

            $category_id = isset($column[0]) ? mysqli_real_escape_string($this->db->link, trim($column[0])) : '';
            $subject_id  = isset($column[1]) ? mysqli_real_escape_string($this->db->link, trim($column[1])) : '';
            $ques        = isset($column[2]) ? mysqli_real_escape_string($this->db->link, trim($column[2])) : '';
            $ans1        = isset($column[3]) ? mysqli_real_escape_string($this->db->link, trim($column[3])) : '';
            $ans2        = isset($column[4]) ? mysqli_real_escape_string($this->db->link, trim($column[4])) : '';
            $ans3        = isset($column[5]) ? mysqli_real_escape_string($this->db->link, trim($column[5])) : '';
            $ans4        = isset($column[6]) ? mysqli_real_escape_string($this->db->link, trim($column[6])) : '';
            $rightAns    = isset($column[7]) ? mysqli_real_escape_string($this->db->link, trim($column[7])) : '';

            if (!empty($ques) && !empty($ans1) && !empty($ans2) && !empty($rightAns)) {
                $nextQuesNo = 1;
                $numQuery = "SELECT MAX(quesNo) AS max_no FROM tbl_ques 
                             WHERE category_id = '$category_id' AND subject_id = '$subject_id'";
                $numRes = $this->db->select($numQuery);
                
                if ($numRes) {
                    $row = $numRes->fetch_assoc();
                    if ($row['max_no'] !== NULL) {
                        $nextQuesNo = (int)$row['max_no'] + 1;
                    }
                }

                $query = "INSERT INTO tbl_ques(quesNo, category_id, subject_id, ques) 
                          VALUES('$nextQuesNo', '$category_id', '$subject_id', '$ques')";
                $insert_row = $this->db->insert($query);

                if ($insert_row) {
                    $quesId = $this->db->link->insert_id;

                    $options = array(1 => $ans1, 2 => $ans2, 3 => $ans3, 4 => $ans4);

                    foreach ($options as $key => $option_name) {
                        if ($option_name !== '') {
                            $right_val = ($rightAns == $key) ? '1' : '0';
                            
                            $ansQuery = "INSERT INTO tbl_ans(quesNo, rightAns, ans) 
                                         VALUES('$quesId', '$right_val', '$option_name')";
                            $this->db->insert($ansQuery);
                        }
                    }
                    $insertedCount++;
                }
            }
        }
        fclose($file);

        if ($insertedCount > 0) {
            return "<div class='p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold'>সফলভাবে মোট {$insertedCount} টি প্রশ্ন ইম্পোর্ট করা হয়েছে!</div>";
        } else {
            return "<div class='p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold'>কোনো ডাটা ইম্পোর্ট করা সম্ভব হয়নি। ফাইলের ফরম্যাট যাচাই করুন।</div>";
        }
    }
}

public function addCircular($data) {
    $category_id  = $this->db->link->real_escape_string($data['category_id']);
    $title        = $this->db->link->real_escape_string($data['title']);
    $organization = $this->db->link->real_escape_string($data['organization']);
    $vacancy      = $this->db->link->real_escape_string($data['vacancy']);
    $deadline     = $this->db->link->real_escape_string($data['deadline']);
    $apply_url    = $this->db->link->real_escape_string($data['apply_url']);
    $pdf_url      = $this->db->link->real_escape_string($data['pdf_url']);

    if (empty($category_id) || empty($title) || empty($organization) || empty($deadline) || empty($apply_url)) {
        return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>আবশ্যকীয় ফিল্ডগুলো পূরণ করুন!</div>";
    }

    $query = "INSERT INTO tbl_circular(category_id, title, organization, vacancy, deadline, apply_url, pdf_url) 
              VALUES('$category_id', '$title', '$organization', '$vacancy', '$deadline', '$apply_url', '$pdf_url')";
    
    $insert_row = $this->db->insert($query);
    if ($insert_row) {
        return "<div class='p-4 mb-4 text-xs text-emerald-700 bg-emerald-100 rounded-xl font-bold'>সার্কুলার সফলভাবে যুক্ত হয়েছে!</div>";
    } else {
        return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>সার্কুলার যুক্ত করা সম্ভব হয়নি।</div>";
    }
}

public function getActiveCirculars($cat_id = null) {
    $where = "WHERE c.status = 1 AND c.deadline >= CURDATE()";
    if ($cat_id) {
        $cat_id = $this->db->link->real_escape_string($cat_id);
        $where .= " AND c.category_id = '$cat_id'";
    }

    $query = "SELECT c.*, cat.category_name 
              FROM tbl_circular c 
              JOIN tbl_category cat ON c.category_id = cat.id 
              $where 
              ORDER BY c.deadline ASC";
    return $this->db->select($query);
}

public function saveExamResult($userId, $categoryId, $subjectId, $total, $score, $wrong) {
    $userId      = (int)$this->fm->validation($userId);
    $categoryId  = (int)$this->fm->validation($categoryId);
    $subjectId   = (int)$this->fm->validation($subjectId);
    $total       = (int)$this->fm->validation($total);
    $score       = (int)$this->fm->validation($score);
    $wrong       = (int)$this->fm->validation($wrong);

    $marks       = $score;
    $percentage  = ($total > 0) ? round(($score / $total) * 100, 2) : 0;
    $status      = ($percentage >= 40) ? 'Passed' : 'Failed';
    $examDate    = date("Y-m-d H:i:s");

    $attemptCode = "EXM-" . date("Ymd") . "-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

    $query = "INSERT INTO tbl_exam_history 
              (attempt_code, user_id, category_id, subject_id, total_questions, correct_answers, wrong_answers, total_marks, percentage, status, exam_date) 
              VALUES 
              ('$attemptCode', '$userId', '$categoryId', '$subjectId', '$total', '$score', '$wrong', '$marks', '$percentage', '$status', '$examDate')";

    $inserted = $this->db->insert($query);

    if ($inserted) {
        return $attemptCode;
    } else {
        return false;
    }
}

public function createSubscriptionRequest($data, $userId) {
    $userId         = (int)$userId;
    $category_id    = (int)$this->fm->validation($data['category_id']);
    $exam_type      = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['exam_type']));
    $duration       = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['duration']));
    $amount         = (float)$this->fm->validation($data['amount']);
    $payment_method = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['payment_method']));
    $sender_number  = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['sender_number']));
    $trx_id         = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['trx_id']));

    if (empty($trx_id) || empty($sender_number)) {
        return "<div class='p-3 bg-rose-50 text-rose-600 rounded-lg text-xs font-bold mb-4'>সকল ফিল্ড পূরণ করুন!</div>";
    }

    $query = "INSERT INTO tbl_subscription(user_id, category_id, exam_type, duration, amount, payment_method, trx_id, sender_number, status) 
              VALUES('$userId', '$category_id', '$exam_type', '$duration', '$amount', '$payment_method', '$trx_id', '$sender_number', 'pending')";

    $inserted = $this->db->insert($query);

    if ($inserted) {
        return "<div class='p-3 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold mb-4'>পেমেন্ট রিকোয়েস্ট জমা হয়েছে! অ্যাডমিন ভেরিফাই করে এক্টিভ করে দেবে।</div>";
    } else {
        return "<div class='p-3 bg-rose-50 text-rose-600 rounded-lg text-xs font-bold mb-4'>পেমেন্ট রিকোয়েস্ট পাঠাতে সমস্যা হয়েছে।</div>";
    }
}

public function getSubscriptionByUserId($userId) {
    $userId = (int)$userId;
    $query  = "SELECT * FROM tbl_subscription WHERE user_id = '$userId' ORDER BY id DESC";
    $result = $this->db->select($query);
    return $result;
}

public function getCategoryById($id) {
    $id = mysqli_real_escape_string($this->db->link, $id);
    $query = "SELECT * FROM tbl_category WHERE id = '$id'";
    $result = $this->db->select($query);
    return $result;
}

public function delQuestion($quesno) {
    $quesno = mysqli_real_escape_string($this->db->link, $quesno);
    
    $query = "UPDATE tbl_ques SET isDeleted = 1 WHERE quesNo = '$quesno'";
    $updated_row = $this->db->update($query);
    
    if ($updated_row) {
        $msg = "<div class='p-3 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold mb-4'>প্রশ্নটি সফলভাবে রিমুভ করা হয়েছে!</div>";
        return $msg;
    } else {
        $msg = "<div class='p-3 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold mb-4'>ত্রুটি! প্রশ্নটি রিমুভ করা যায়নি।</div>";
        return $msg;
    }
}

public function getQueByOrder() {
    $query = "SELECT tbl_ques.*, tbl_category.category_name, tbl_subject.subject_name 
              FROM tbl_ques 
              LEFT JOIN tbl_category ON tbl_ques.category_id = tbl_category.id 
              LEFT JOIN tbl_subject ON tbl_ques.subject_id = tbl_subject.id 
              WHERE tbl_ques.isDeleted = 0 
              ORDER BY tbl_ques.quesNo ASC";
    $result = $this->db->select($query);
    return $result;
}

public function getUserExamHistory($userId) {
    $userId = mysqli_real_escape_string($this->db->link, $userId);
    
    $query = "SELECT h.*, 
                     c.category_name, 
                     s.subject_name 
              FROM tbl_exam_history h 
              LEFT JOIN tbl_category c ON h.category_id = c.id 
              LEFT JOIN tbl_subject s ON h.subject_id = s.id 
              WHERE h.user_id = '$userId' 
              ORDER BY h.id DESC";
              
    $result = $this->db->select($query);
    return $result;
}

public function getLeaderboardByCategory($category_id = 0) {
    $category_id = (int)$category_id;

    $whereClause = "";
    if ($category_id > 0) {
        $whereClause = "WHERE h.category_id = '$category_id'";
    }

    $query = "SELECT u.userId AS user_id, u.name, u.email, 
                     SUM(h.total_marks) as total_score, 
                     COUNT(h.id) as total_attempt 
              FROM tbl_exam_history h 
              INNER JOIN tbl_user u ON h.user_id = u.userId 
              $whereClause
              GROUP BY h.user_id 
              ORDER BY total_score DESC, total_attempt ASC 
              LIMIT 10";

    $result = $this->db->select($query);
    return $result;
}

public function getFilteredLeaderboard($category_id, $subject_id = 0, $attempts_limit = 0) {
    $category_id    = (int)$category_id;
    $subject_id     = (int)$subject_id;
    $attempts_limit = (int)$attempts_limit;

    $conditions = ["h.category_id = '$category_id'"];
    
    if ($subject_id > 0) {
        $conditions[] = "h.subject_id = '$subject_id'";
    }

    $whereClause = "WHERE " . implode(" AND ", $conditions);

    $havingClause = "";
    if ($attempts_limit > 0) {
        $havingClause = "HAVING total_attempt = '$attempts_limit'";
    }

    $query = "SELECT u.userId, u.name, u.email, 
                     SUM(h.total_marks) as total_score, 
                     COUNT(h.id) as total_attempt,
                     AVG(h.percentage) as avg_percentage
              FROM tbl_exam_history h 
              INNER JOIN tbl_user u ON h.user_id = u.userId 
              $whereClause 
              GROUP BY h.user_id 
              $havingClause
              ORDER BY total_score DESC, avg_percentage DESC 
              LIMIT 20";

    return $this->db->select($query);
}

public function setupCustomExam($category_id, $num_questions, $time_limit, $subject_id = 0) {
    $whereClause = "WHERE isDeleted = 0";

    if ($category_id > 0) {
        $whereClause .= " AND category_id = '$category_id'";
    }

    if ($subject_id > 0) {
        $whereClause .= " AND subject_id = '$subject_id'";
    }

    $query = "SELECT id FROM tbl_ques {$whereClause} ORDER BY RAND() LIMIT $num_questions";
    $result = $this->db->select($query);

    $questionsArr = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $questionsArr[] = $row['id'];
        }
    }

    Session::set("exam_questions", $questionsArr);
    Session::set("exam_total_ques", count($questionsArr));
    Session::set("exam_category_id", $category_id);
    Session::set("exam_subject_id", $subject_id);
    Session::set("exam_time_limit", $time_limit);
    Session::set("exam_start_time", time());
    
    Session::set("exam_score", 0);
    Session::set("user_answers", array());
}

public function getQuestionById($quesId) {
    $quesId = mysqli_real_escape_string($this->db->link, $quesId);
    
    $query  = "SELECT * FROM tbl_ques WHERE id = '$quesId'";
    $result = $this->db->select($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

public function getQuestionByNumber($quesNo) {
    $quesNo = (int)$quesNo;
    $query = "SELECT * FROM tbl_ques WHERE quesNo = '$quesNo' AND isDeleted = 0";
    $result = $this->db->select($query);
    if ($result) {
        return $result->fetch_assoc();
    }
    return false;
}

public function getAnswers($quesId) {
    $quesId = mysqli_real_escape_string($this->db->link, $quesId);
    $query = "SELECT * FROM tbl_ans WHERE quesNo = '$quesId'";
    return $this->db->select($query);
}

public function processAnswer($quesNo, $selectedAns) {
    $quesNo      = (int)$quesNo;
    $selectedAns = (int)$selectedAns;

    $query  = "SELECT id FROM tbl_ans WHERE quesNo = '$quesNo' AND rightAns = '1'";
    $result = $this->db->select($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $correct_ans_id = (int)$row['id'];

        if ($correct_ans_id > 0 && $correct_ans_id === $selectedAns) {
            $score   = Session::get("score") ? Session::get("score") + 1 : 1;
            $correct = Session::get("correct_ans") ? Session::get("correct_ans") + 1 : 1;

            Session::set("score", $score);
            Session::set("correct_ans", $correct);
        } else {
            $wrong = Session::get("wrong_ans") ? Session::get("wrong_ans") + 1 : 1;
            Session::set("wrong_ans", $wrong);
        }
    }
}

public function getTotalRows() {
    $query = "SELECT * FROM tbl_ques";
    $getResult = $this->db->select($query);
    if ($getResult) {
        $total = $getResult->num_rows;
        return $total;
    } else {
        return 0;
    }
}

public function getQuestion(){
    $query = "SELECT * FROM tbl_ques";
    $getData = $this->db->select($query);
    $result = $getData->fetch_assoc();
    return $result;
}

public function getSubjectsWithQuestions($category_id = 0) {
    $category_id = (int)$category_id;
    
    $where = "WHERE q.isDeleted = 0";
    if ($category_id > 0) {
        $where .= " AND q.category_id = '$category_id'";
    }

    $query = "SELECT DISTINCT s.id, s.subject_name, COUNT(q.quesNo) as total_ques 
              FROM tbl_subject s 
              INNER JOIN tbl_ques q ON s.id = q.subject_id 
              $where 
              GROUP BY s.id, s.subject_name 
              ORDER BY s.subject_name ASC";

    $result = $this->db->select($query);
    return $result;
}

public function getQuesByNumber($number){
    $query = "SELECT * FROM tbl_ques WHERE quesNo ='$number'";
    $getData = $this->db->select($query);
    $result = $getData->fetch_assoc();
    return $result;
}

public function getAnswer($quesId) {
    $quesId = mysqli_real_escape_string($this->db->link, $quesId);
    $query  = "SELECT * FROM tbl_ans WHERE quesNo = '$quesId'";
    $result = $this->db->select($query);
    return $result;
}

public function addCategory($category_name) {
    $category_name = $this->fm->validation($category_name);
    $category_name = mysqli_real_escape_string($this->db->link, $category_name);

    if (empty($category_name)) {
        return "<div class='text-rose-500 font-bold mb-3'>ক্যাটাগরির নাম দিন!</div>";
    }

    $query = "INSERT INTO tbl_category(category_name) VALUES('$category_name')";
    $inserted = $this->db->insert($query);
    return $inserted ? "<div class='text-emerald-500 font-bold mb-3'>ক্যাটাগরি যুক্ত হয়েছে!</div>" : "<div class='text-rose-500 font-bold mb-3'>সমস্যা হয়েছে!</div>";
}

public function addSubject($subject_name) {
    $subject_name = $this->fm->validation($subject_name);
    $subject_name = mysqli_real_escape_string($this->db->link, $subject_name);

    if (empty($subject_name)) {
        return "<div class='text-rose-500 font-bold mb-3'>বিষয়ের নাম দিন!</div>";
    }

    $query = "INSERT INTO tbl_subject(subject_name) VALUES('$subject_name')";
    $inserted = $this->db->insert($query);
    return $inserted ? "<div class='text-emerald-500 font-bold mb-3'>বিষয় যুক্ত হয়েছে!</div>" : "<div class='text-rose-500 font-bold mb-3'>সমস্যা হয়েছে!</div>";
}

public function getAllCategories() {
    $query = "SELECT * FROM tbl_category ORDER BY id DESC";
    return $this->db->select($query);
}

public function getAllSubjects() {
    $query = "SELECT * FROM tbl_subject ORDER BY id DESC";
    return $this->db->select($query);
}

// Get all subscriptions for Admin
public function getAllSubscriptions() {
    $query = "SELECT s.*, u.name as user_name, u.email 
              FROM tbl_subscription s 
              JOIN tbl_user u ON s.user_id = u.userId 
              ORDER BY s.id DESC";
    return $this->db->select($query);
}

// Subscription Filter Helper
private function buildSubscriptionWhere($search = '', $status = '', $method = '', $package = '', $fromDate = '', $toDate = '') {
    $conditions = [];

    if (!empty($search)) {
        $safeSearch = mysqli_real_escape_string($this->db->link, trim($search));
        $conditions[] = "(
            u.name LIKE '%{$safeSearch}%' OR 
            u.email LIKE '%{$safeSearch}%' OR 
            s.trx_id LIKE '%{$safeSearch}%' OR 
            s.sender_number LIKE '%{$safeSearch}%' OR 
            s.exam_type LIKE '%{$safeSearch}%'
        )";
    }

    if (!empty($status)) {
        $safeStatus = mysqli_real_escape_string($this->db->link, strtolower(trim($status)));
        $conditions[] = "LOWER(TRIM(s.status)) = '{$safeStatus}'";
    }

    if (!empty($method)) {
        $safeMethod = mysqli_real_escape_string($this->db->link, strtolower(trim($method)));
        $conditions[] = "LOWER(TRIM(s.payment_method)) = '{$safeMethod}'";
    }

    if (!empty($package)) {
        $safePackage = mysqli_real_escape_string($this->db->link, trim($package));
        $conditions[] = "s.exam_type = '{$safePackage}'";
    }

    if (!empty($fromDate)) {
        $safeFrom = mysqli_real_escape_string($this->db->link, trim($fromDate));
        $conditions[] = "DATE(s.start_date) >= '{$safeFrom}'";
    }

    if (!empty($toDate)) {
        $safeTo = mysqli_real_escape_string($this->db->link, trim($toDate));
        $conditions[] = "DATE(s.start_date) <= '{$safeTo}'";
    }

    return !empty($conditions) ? (" WHERE " . implode(" AND ", $conditions)) : "";
}

// Filtered Subscription Total Count (Pagination-এর জন্য)
public function getFilteredSubscriptionsCount($search = '', $status = '', $method = '', $package = '', $fromDate = '', $toDate = '') {
    $whereSql = $this->buildSubscriptionWhere($search, $status, $method, $package, $fromDate, $toDate);
    $query = "SELECT COUNT(*) AS total 
              FROM tbl_subscription s 
              JOIN tbl_user u ON s.user_id = u.userId 
              {$whereSql}";
    $result = $this->db->select($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

// Filtered + Paginated Subscriptions
public function getFilteredSubscriptionsPaginated($search = '', $status = '', $method = '', $package = '', $fromDate = '', $toDate = '', $limit = 10, $offset = 0) {
    $limit  = max(1, (int)$limit);
    $offset = max(0, (int)$offset);
    $whereSql = $this->buildSubscriptionWhere($search, $status, $method, $package, $fromDate, $toDate);

    $query = "SELECT s.*, u.name as user_name, u.email 
              FROM tbl_subscription s 
              JOIN tbl_user u ON s.user_id = u.userId 
              {$whereSql} 
              ORDER BY s.id DESC 
              LIMIT {$limit} OFFSET {$offset}";
    return $this->db->select($query);
}

// Package ড্রপডাউনের জন্য distinct exam_type তালিকা
public function getSubscriptionPackages() {
    $query = "SELECT DISTINCT exam_type FROM tbl_subscription WHERE exam_type != '' ORDER BY exam_type ASC";
    return $this->db->select($query);
}

// Update Subscription Status and set Start/Expire Date
public function updateSubscriptionStatus($sub_id, $status, $duration) {
    $sub_id   = mysqli_real_escape_string($this->db->link, $sub_id);
    $status   = mysqli_real_escape_string($this->db->link, $status);
    $duration = mysqli_real_escape_string($this->db->link, $duration);

    if (strtolower($status) == 'approved') {
        $start_date = date('Y-m-d H:i:s');
        
        if (strpos($duration, '365') !== false || strpos($duration, '12') !== false) {
            $expire_date = date('Y-m-d H:i:s', strtotime('+365 days'));
        } elseif (strpos($duration, '90') !== false || strpos($duration, '3') !== false) {
            $expire_date = date('Y-m-d H:i:s', strtotime('+90 days'));
        } else {
            $expire_date = date('Y-m-d H:i:s', strtotime('+180 days'));
        }

        $query = "UPDATE tbl_subscription 
                  SET status = '$status', start_date = '$start_date', expire_date = '$expire_date' 
                  WHERE id = '$sub_id'";
    } else {
        $query = "UPDATE tbl_subscription SET status = '$status' WHERE id = '$sub_id'";
    }

    $update_row = $this->db->link->query($query);

    if ($update_row) {
        return "<div class='p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-bold'>Subscription status updated successfully!</div>";
    } else {
        return "<div class='p-3 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 font-bold'>Failed to update: " . $this->db->link->error . "</div>";
    }
}

public function getAllCirculars() {
    $query = "SELECT c.*, cat.category_name 
              FROM tbl_circular c 
              JOIN tbl_category cat ON c.category_id = cat.id 
              ORDER BY c.id DESC";
    return $this->db->select($query);
}

public function getCircularListCount($category_id = 0, $status = 'all', $search = '') {
    $category_id = (int)$category_id;
    $status = in_array($status, array('all', 'active', 'expired'), true) ? $status : 'all';
    $search = $this->db->link->real_escape_string(trim($search));

    $where = "WHERE 1=1";
    if ($category_id > 0) {
        $where .= " AND c.category_id = '$category_id'";
    }
    if ($status === 'active') {
        $where .= " AND c.deadline >= CURDATE()";
    } elseif ($status === 'expired') {
        $where .= " AND c.deadline < CURDATE()";
    }
    if ($search !== '') {
        $where .= " AND (c.title LIKE '%$search%' OR c.organization LIKE '%$search%')";
    }

    $query = "SELECT COUNT(*) AS total FROM tbl_circular c $where";
    $result = $this->db->select($query);
    if ($result) {
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

public function getCircularListPaginated($category_id = 0, $status = 'all', $search = '', $limit = 10, $offset = 0) {
    $category_id = (int)$category_id;
    $status = in_array($status, array('all', 'active', 'expired'), true) ? $status : 'all';
    $search = $this->db->link->real_escape_string(trim($search));
    $limit = max(1, (int)$limit);
    $offset = max(0, (int)$offset);

    $where = "WHERE 1=1";
    if ($category_id > 0) {
        $where .= " AND c.category_id = '$category_id'";
    }
    if ($status === 'active') {
        $where .= " AND c.deadline >= CURDATE()";
    } elseif ($status === 'expired') {
        $where .= " AND c.deadline < CURDATE()";
    }
    if ($search !== '') {
        $where .= " AND (c.title LIKE '%$search%' OR c.organization LIKE '%$search%')";
    }

    $query = "SELECT c.*, cat.category_name
              FROM tbl_circular c
              LEFT JOIN tbl_category cat ON c.category_id = cat.id
              $where
              ORDER BY c.id DESC
              LIMIT $limit OFFSET $offset";
    return $this->db->select($query);
}

public function getCircularById($id) {
    $id = (int)$id;
    if ($id <= 0) return false;

    $query = "SELECT * FROM tbl_circular WHERE id = '$id' LIMIT 1";
    return $this->db->select($query);
}

public function updateCircular($id, $data) {
    $id = (int)$id;
    if ($id <= 0) {
        return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>সঠিক সার্কুলার নির্বাচন করা হয়নি।</div>";
    }

    $category_id  = (int)($data['category_id'] ?? 0);
    $title        = $this->db->link->real_escape_string(trim($data['title'] ?? ''));
    $organization = $this->db->link->real_escape_string(trim($data['organization'] ?? ''));
    $vacancy      = $this->db->link->real_escape_string(trim($data['vacancy'] ?? ''));
    $deadline     = $this->db->link->real_escape_string(trim($data['deadline'] ?? ''));
    $apply_url    = $this->db->link->real_escape_string(trim($data['apply_url'] ?? ''));
    $pdf_url      = $this->db->link->real_escape_string(trim($data['pdf_url'] ?? ''));

    if ($category_id <= 0 || $title === '' || $organization === '' || $deadline === '' || $apply_url === '') {
        return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>আবশ্যকীয় ফিল্ডগুলো পূরণ করুন।</div>";
    }

    $query = "UPDATE tbl_circular SET
                category_id = '$category_id',
                title = '$title',
                organization = '$organization',
                vacancy = '$vacancy',
                deadline = '$deadline',
                apply_url = '$apply_url',
                pdf_url = '$pdf_url'
              WHERE id = '$id'";

    $updated = $this->db->update($query);
    if ($updated) {
        return "<div class='p-4 mb-4 text-xs text-emerald-700 bg-emerald-100 rounded-xl font-bold'>সার্কুলার সফলভাবে আপডেট হয়েছে!</div>";
    }
    return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>সার্কুলার আপডেট করা যায়নি। ডাটাবেজ সংযোগ ও তথ্য যাচাই করুন।</div>";
}

public function deleteCircular($id) {
    $id = $this->db->link->real_escape_string($id);
    $query = "DELETE FROM tbl_circular WHERE id = '$id'";
    $delData = $this->db->delete($query);
    if ($delData) {
        return "<div class='p-4 mb-4 text-xs text-emerald-700 bg-emerald-100 rounded-xl font-bold'>সার্কুলার সফলভাবে মুছে ফেলা হয়েছে!</div>";
    } else {
        return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>মুছে ফেলা সম্ভব হয়নি।</div>";
    }
}

public function getQuestionListCount($category_id = 0, $subject_id = 0, $search = '') {
    $category_id = (int)$category_id;
    $subject_id  = (int)$subject_id;
    $search      = $this->db->link->real_escape_string(trim($search));

    $where = "WHERE q.isDeleted = 0";
    if ($category_id > 0) $where .= " AND q.category_id = '$category_id'";
    if ($subject_id > 0)  $where .= " AND q.subject_id = '$subject_id'";
    if ($search !== '')   $where .= " AND q.ques LIKE '%$search%'";

    $query = "SELECT COUNT(*) AS total FROM tbl_ques q $where";
    $result = $this->db->select($query);

    if ($result) {
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

public function getQuestionListPaginated($category_id = 0, $subject_id = 0, $search = '', $limit = 10, $offset = 0) {
    $category_id = (int)$category_id;
    $subject_id  = (int)$subject_id;
    $search      = $this->db->link->real_escape_string(trim($search));
    $limit       = max(1, (int)$limit);
    $offset      = max(0, (int)$offset);

    $where = "WHERE q.isDeleted = 0";
    if ($category_id > 0) $where .= " AND q.category_id = '$category_id'";
    if ($subject_id > 0)  $where .= " AND q.subject_id = '$subject_id'";
    if ($search !== '')   $where .= " AND q.ques LIKE '%$search%'";

    $query = "SELECT q.*, c.category_name, s.subject_name
              FROM tbl_ques q
              LEFT JOIN tbl_category c ON q.category_id = c.id
              LEFT JOIN tbl_subject s ON q.subject_id = s.id
              $where
              ORDER BY q.id DESC
              LIMIT $limit OFFSET $offset";
    return $this->db->select($query);
}

public function getEditableQuestionById($id) {
    $id = (int)$id;
    if ($id <= 0) return false;

    $query = "SELECT * FROM tbl_ques WHERE id = '$id' AND isDeleted = 0 LIMIT 1";
    $result = $this->db->select($query);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

public function getEditableAnswersByQuesId($quesId) {
    $quesId = (int)$quesId;
    if ($quesId <= 0) return false;

    $query = "SELECT * FROM tbl_ans WHERE quesNo = '$quesId' ORDER BY id ASC";
    return $this->db->select($query);
}

public function updateQuestionById($id, $data) {
    $id          = (int)$id;
    $category_id = (int)$this->fm->validation($data['category_id'] ?? 0);
    $subject_id  = (int)$this->fm->validation($data['subject_id'] ?? 0);
    $quesNo      = (int)$this->fm->validation($data['quesNo'] ?? 0);
    $ques        = mysqli_real_escape_string($this->db->link, trim($data['ques'] ?? ''));
    $rightAns    = (int)$this->fm->validation($data['rightAns'] ?? 0);

    $ans = array(
        1 => trim($data['ans1'] ?? ''),
        2 => trim($data['ans2'] ?? ''),
        3 => trim($data['ans3'] ?? ''),
        4 => trim($data['ans4'] ?? '')
    );

    if ($id <= 0 || $category_id <= 0 || $subject_id <= 0 || $ques === '') {
        return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>ক্যাটাগরি, বিষয় এবং প্রশ্ন সঠিকভাবে পূরণ করুন!</div>";
    }

    $updateQuesQuery = "UPDATE tbl_ques 
                        SET quesNo = '$quesNo', category_id = '$category_id', subject_id = '$subject_id', ques = '$ques' 
                        WHERE id = '$id'";
    $updatedQues = $this->db->update($updateQuesQuery);

    if ($updatedQues) {
        $this->db->delete("DELETE FROM tbl_ans WHERE quesNo = '$id'");

        foreach ($ans as $key => $ansName) {
            if ($ansName !== '') {
                $rAns = ($rightAns === $key) ? '1' : '0';
                $ansEscaped = mysqli_real_escape_string($this->db->link, $ansName);
                $ansQuery = "INSERT INTO tbl_ans(quesNo, rightAns, ans) VALUES('$id', '$rAns', '$ansEscaped')";
                $this->db->insert($ansQuery);
            }
        }

        return "<div class='p-4 mb-4 text-xs text-emerald-700 bg-emerald-100 rounded-xl font-bold'>প্রশ্ন ও অপশন সফলভাবে আপডেট হয়েছে!</div>";
    }

    return "<div class='p-4 mb-4 text-xs text-rose-700 bg-rose-100 rounded-xl font-bold'>প্রশ্ন আপডেট করা যায়নি!</div>";
}

// ================= Category & Subject Management =================

public function updateCategory($id, $name) {
    $id = (int)$id;
    $name = trim($this->db->link->real_escape_string($name));
    if ($id <= 0 || $name === '') return array(false, "সঠিক ক্যাটাগরির নাম দিন।");

    $query = "UPDATE tbl_category SET category_name = '$name' WHERE id = '$id'";
    if ($this->db->update($query)) {
        return array(true, "ক্যাটাগরি সফলভাবে আপডেট হয়েছে।");
    }
    return array(false, "ক্যাটাগরি আপডেট করা যায়নি।");
}

public function updateSubject($id, $name) {
    $id = (int)$id;
    $name = trim($this->db->link->real_escape_string($name));
    if ($id <= 0 || $name === '') return array(false, "সঠিক বিষয়ের নাম দিন।");

    $query = "UPDATE tbl_subject SET subject_name = '$name' WHERE id = '$id'";
    if ($this->db->update($query)) {
        return array(true, "বিষয় সফলভাবে আপডেট হয়েছে।");
    }
    return array(false, "বিষয় আপডেট করা যায়নি।");
}

private function getReferenceCount($table, $column, $id) {
    $id = (int)$id;
    $query = "SELECT COUNT(*) AS total FROM $table WHERE $column = '$id'";
    $result = $this->db->select($query);
    if (!$result) return false;
    $row = $result->fetch_assoc();
    return (int)($row['total'] ?? 0);
}

public function deleteCategorySafely($id) {
    $id = (int)$id;
    if ($id <= 0) return array(false, "সঠিক ক্যাটাগরি নির্বাচন করুন।");

    $references = array(
        array('tbl_ques', 'category_id'),
        array('tbl_circular', 'category_id'),
        array('tbl_exam_history', 'category_id'),
        array('tbl_subscription', 'category_id')
    );

    foreach ($references as $reference) {
        $count = $this->getReferenceCount($reference[0], $reference[1], $id);
        if ($count === false) {
            return array(false, "ব্যবহারের তথ্য যাচাই করা যায়নি। নিরাপত্তার জন্য ক্যাটাগরিটি মুছে ফেলা হয়নি।");
        }
        if ($count > 0) {
            return array(false, "এই ক্যাটাগরি {$count}টি রেকর্ডে ব্যবহৃত হচ্ছে। আগে সংশ্লিষ্ট রেকর্ডের Category পরিবর্তন করুন; তারপর Delete করতে পারবেন।");
        }
    }

    if ($this->db->delete("DELETE FROM tbl_category WHERE id = '$id'")) {
        return array(true, "ক্যাটাগরি সফলভাবে মুছে ফেলা হয়েছে।");
    }
    return array(false, "ক্যাটাগরি মুছে ফেলা যায়নি।");
}

public function deleteSubjectSafely($id) {
    $id = (int)$id;
    if ($id <= 0) return array(false, "সঠিক বিষয় নির্বাচন করুন।");

    $references = array(
        array('tbl_ques', 'subject_id'),
        array('tbl_exam_history', 'subject_id')
    );

    foreach ($references as $reference) {
        $count = $this->getReferenceCount($reference[0], $reference[1], $id);
        if ($count === false) {
            return array(false, "ব্যবহারের তথ্য যাচাই করা যায়নি। নিরাপত্তার জন্য বিষয়টি মুছে ফেলা হয়নি।");
        }
        if ($count > 0) {
            return array(false, "এই বিষয় {$count}টি রেকর্ডে ব্যবহৃত হচ্ছে। আগে সংশ্লিষ্ট রেকর্ডের Subject পরিবর্তন করুন; তারপর Delete করতে পারবেন।");
        }
    }

    if ($this->db->delete("DELETE FROM tbl_subject WHERE id = '$id'")) {
        return array(true, "বিষয় সফলভাবে মুছে ফেলা হয়েছে।");
    }
    return array(false, "বিষয় মুছে ফেলা যায়নি।");
}

}
?>