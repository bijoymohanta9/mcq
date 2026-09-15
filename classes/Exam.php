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
    $userId = $this->fm->validation($userId);
    $userId = mysqli_real_escape_string($this->db->link, $userId);

    $query = "SELECT h.*, c.category_name, s.subject_name 
              FROM tbl_exam_history h 
              LEFT JOIN tbl_category c ON h.category_id = c.id 
              LEFT JOIN tbl_subject s ON h.subject_id = s.id
              WHERE h.user_id = '$userId' 
              ORDER BY h.id DESC";
              
    return $this->db->select($query);
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

  public function setupCustomExam($category_id, $num_questions, $time_limit) {
    $category_id   = (int)$category_id;
    $num_questions = (int)$num_questions;
    $time_limit    = (int)$time_limit;

    // ১. ক্যাটাগরি অনুসারে ক্যোয়ারী তৈরি
    if ($category_id > 0) {
        $query = "SELECT quesNo FROM tbl_ques WHERE category_id = '$category_id' AND isDeleted = 0 ORDER BY RAND() LIMIT $num_questions";
    } else {
        $query = "SELECT quesNo FROM tbl_ques WHERE isDeleted = 0 ORDER BY RAND() LIMIT $num_questions";
    }

    $result = $this->db->select($query);

    $examQuestions = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $examQuestions[] = $row['quesNo'];
        }
    }

    // ২. পরীক্ষার সেশন ডাটা সেট করা
    Session::set("exam_questions", $examQuestions);
    Session::set("exam_total_ques", count($examQuestions)); // সঠিক মোট প্রশ্ন সংখ্যা
    Session::set("exam_time_limit", $time_limit);
    Session::set("exam_category_id", $category_id); // পরবর্তীতে হিস্ট্রিতে সেভ করার জন্য
    Session::set("exam_start_time", time());
    
    // স্কোর ও ট্র্যাকিং সেশন রিসেট
    Session::set("score", 0);
    Session::set("correct_ans", 0);
    Session::set("wrong_ans", 0);
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
}


 ?>