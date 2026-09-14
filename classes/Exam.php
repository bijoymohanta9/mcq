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
    $ques        = $this->fm->validation($data['ques']);
    $rightAns    = $this->fm->validation($data['rightAns']);

    $category_id = mysqli_real_escape_string($this->db->link, $category_id);
    $subject_id  = mysqli_real_escape_string($this->db->link, $subject_id);
    $quesNo      = mysqli_real_escape_string($this->db->link, $quesNo);
    $ques        = mysqli_real_escape_string($this->db->link, $ques);
    $rightAns    = mysqli_real_escape_string($this->db->link, $rightAns);

    $ans = array();
    $ans[1] = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['ans1']));
    $ans[2] = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['ans2']));
    $ans[3] = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['ans3']));
    $ans[4] = mysqli_real_escape_string($this->db->link, $this->fm->validation($data['ans4']));

    // ১. tbl_ques টেবিলে প্রশ্ন এবং ক্যাটাগরি/সাবজেক্ট আইডি ইনসার্ট
    $query = "INSERT INTO tbl_ques(quesNo, ques, category_id, subject_id) 
              VALUES('$quesNo', '$ques', '$category_id', '$subject_id')";
    $insert_row = $this->db->insert($query);

    if ($insert_row) {
        // ২. tbl_ans টেবিলে ৪টি অপশন ও সঠিক উত্তর ইনসার্ট
        foreach ($ans as $key => $ansName) {
            if ($ansName != '') {
                $isRight = ($rightAns == $key) ? '1' : '0';
                $rquery = "INSERT INTO tbl_ans(quesNo, rightAns, ans) 
                           VALUES('$quesNo', '$isRight', '$ansName')";
                $this->db->insert($rquery);
            }
        }
        return "প্রশ্ন সফলভাবে যুক্ত করা হয়েছে!";
    } else {
        return "প্রশ্ন যুক্ত করতে সমস্যা হয়েছে!";
    }
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

    // ট্র্যাকিং আইডির জন্য ইউনিক কোড জেনারেট (যেমন: EXM-20260915-A1B2)
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
    $query = "SELECT * FROM tbl_category WHERE id = '$id'"; // Replace tbl_category with your actual database table name if different
    $result = $this->db->select($query);
    return $result;
}

  // সফট ডিলিট করার জন্য (DELETE কোয়েরির বদলে UPDATE কোয়েরি)
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

  // ১. শুধুমাত্র একটিভ (isDeleted = 0) প্রশ্নগুলো নিয়ে আসার জন্য
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
    
    // LEFT JOIN দিয়ে Category ও Subject এর আসল নাম তুলে আনা
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

  public function getLeaderboardByCategory($category_id) {
		$category_id = mysqli_real_escape_string($this->db->link, $category_id);

		// প্রতিটি ইউজারের মোট পয়েন্ট এবং সঠিক উত্তরের সংখ্যা গণনা করার কোয়েরি
		$query = "SELECT tbl_user.name, tbl_user.email, 
						SUM(tbl_score.score) as total_score, 
						COUNT(tbl_score.id) as total_attempt 
				FROM tbl_score 
				INNER JOIN tbl_user ON tbl_score.userId = tbl_user.userId 
				WHERE tbl_score.category_id = '$category_id' 
				GROUP BY tbl_score.userId 
				ORDER BY total_score DESC, total_attempt ASC 
				LIMIT 10";

		$result = $this->db->select($query);
		return $result;
	}

  public function setupCustomExam($category_id, $num_questions, $time_limit, $subject_id = 0) {
    $category_id   = (int)$category_id;
    $subject_id    = (int)$subject_id;
    $num_questions = (int)$num_questions;
    $time_limit    = (int)$time_limit;

    // ফিল্টারিং ক্যোয়ারী
    $conditions = ["isDeleted = 0"];

    if ($category_id > 0) {
        $conditions[] = "category_id = '$category_id'";
    }
    if ($subject_id > 0) {
        $conditions[] = "subject_id = '$subject_id'";
    }

    $whereClause = implode(" AND ", $conditions);
    $query = "SELECT quesNo FROM tbl_ques WHERE $whereClause ORDER BY RAND() LIMIT $num_questions";

    $result = $this->db->select($query);

    $examQuestions = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $examQuestions[] = $row['quesNo'];
        }
    }

    // সেশন ডাটা সেট
    Session::set("exam_questions", $examQuestions);
    Session::set("exam_total_ques", count($examQuestions));
    Session::set("exam_time_limit", $time_limit);
    Session::set("exam_category_id", $category_id);
    Session::set("exam_subject_id", $subject_id);
    Session::set("exam_start_time", time());
    
    Session::set("score", 0);
    Session::set("correct_ans", 0);
    Session::set("wrong_ans", 0);
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

// প্রশ্নের উত্তর (Options) ফেচ করার মেথড
public function getAnswers($quesNo) {
    $quesNo = (int)$quesNo;
    $query = "SELECT * FROM tbl_ans WHERE quesNo = '$quesNo'";
    $result = $this->db->select($query);
    return $result;
}

// ইউজার সাবমিট করা উত্তর প্রসেস ও রেজাল্ট ক্যালকুলেট করার মেথড
public function processAnswer($quesNo, $selectedAns) {
    $quesNo      = (int)$quesNo;
    $selectedAns = (int)$selectedAns;

    // ১. ডাটাবেস থেকে উক্ত প্রশ্নের সঠিক উত্তরটি নিয়ে আসা
    // tbl_ans টেবিলে সঠিক উত্তরের জন্য rightAns = '1' ব্যবহার করা হয়েছে
    $query  = "SELECT id FROM tbl_ans WHERE quesNo = '$quesNo' AND rightAns = '1'";
    $result = $this->db->select($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $correct_ans_id = (int)$row['id'];

        // ২. ইউজারের সিলেক্ট করা উত্তর ও ডাটাবেসের সঠিক উত্তর ম্যাচ করানো
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

  // ক্যাটাগরি ও প্রশ্ন থাকার ওপর ভিত্তি করে সাবজেক্ট লোড করার মেথড
public function getSubjectsWithQuestions($category_id = 0) {
    $category_id = (int)$category_id;
    
    $where = "WHERE q.isDeleted = 0";
    if ($category_id > 0) {
        $where .= " AND q.category_id = '$category_id'";
    }

    // tbl_ques টেবিলের সাথে JOIN দিয়ে প্রশ্ন থাকা সাবজেক্ট ফেচ করা
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

  public function getAnswer($number){
    $query = "SELECT * FROM tbl_ans WHERE quesNo ='$number'";
    $getData = $this->db->select($query);
    return $getData;
  }

  // ক্যাটাগরি ও সাবজেক্ট সেভ করার মেথড
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

    // ড্রপডাউনে ডাটা লোড করার মেথড
    public function getAllCategories() {
        $query = "SELECT * FROM tbl_category ORDER BY id DESC";
        return $this->db->select($query);
    }

    public function getAllSubjects() {
        $query = "SELECT * FROM tbl_subject ORDER BY id DESC";
        return $this->db->select($query);
    }

    // Get all subscriptions for Admin
// Get all subscriptions for Admin
public function getAllSubscriptions() {
    $query = "SELECT s.*, u.name as user_name, u.email 
              FROM tbl_subscription s 
              JOIN tbl_user u ON s.user_id = u.userId 
              ORDER BY s.id DESC";
    return $this->db->select($query);
}

// Update Subscription Status and set Start/Expire Date
// Update Subscription Status and set Start/Expire Date
public function updateSubscriptionStatus($sub_id, $status, $duration) {
    $sub_id   = mysqli_real_escape_string($this->db->link, $sub_id);
    $status   = mysqli_real_escape_string($this->db->link, $status);
    $duration = mysqli_real_escape_string($this->db->link, $duration);

    if (strtolower($status) == 'approved') {
        $start_date = date('Y-m-d H:i:s');
        
        // Duration Check
        if (strpos($duration, '365') !== false || strpos($duration, '1') !== false) {
            $expire_date = date('Y-m-d H:i:s', strtotime('+365 days'));
        } elseif (strpos($duration, '90') !== false || strpos($duration, '3') !== false) {
            $expire_date = date('Y-m-d H:i:s', strtotime('+90 days'));
        } else {
            $expire_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        }

        $query = "UPDATE tbl_subscription 
                  SET status = '$status', start_date = '$start_date', expire_date = '$expire_date' 
                  WHERE id = '$sub_id'";
    } else {
        $query = "UPDATE tbl_subscription SET status = '$status' WHERE id = '$sub_id'";
    }

    // Execute Query
    $update_row = $this->db->link->query($query);

    if ($update_row) {
        return "<div class='p-3 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 font-bold'>Subscription status updated successfully!</div>";
    } else {
        return "<div class='p-3 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 font-bold'>Failed to update: " . $this->db->link->error . "</div>";
    }
}
}


 ?>