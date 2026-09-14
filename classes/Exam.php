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

  public function addQuestions($data) {
    $quesNo      = mysqli_real_escape_string($this->db->link, $data['quesNo']);
    $category_id = mysqli_real_escape_string($this->db->link, $data['category_id']);
    $subject_id  = mysqli_real_escape_string($this->db->link, $data['subject_id']);
    $ques        = mysqli_real_escape_string($this->db->link, $data['ques']);
    
    $ans = array();
    $ans[1] = mysqli_real_escape_string($this->db->link, $data['ans1']);
    $ans[2] = mysqli_real_escape_string($this->db->link, $data['ans2']);
    $ans[3] = mysqli_real_escape_string($this->db->link, $data['ans3']);
    $ans[4] = mysqli_real_escape_string($this->db->link, $data['ans4']);
    $rightAns = mysqli_real_escape_string($this->db->link, $data['rightAns']);

    // ১. খালি ফিল্ড চেক
    if (empty($quesNo) || empty($category_id) || empty($subject_id) || empty($ques) || empty($ans[1]) || empty($ans[2]) || empty($ans[3]) || empty($ans[4]) || empty($rightAns)) {
        $msg = "<span class='text-rose-600 font-semibold'>সবগুলো ফিল্ড অবশ্যই পূরণ করতে হবে!</span>";
        return $msg;
    } 

    // ২. tbl_ques টেবিলে মূল প্রশ্নটি মাত্র ১ বার Insert করা
    $query = "INSERT INTO tbl_ques(quesNo, category_id, subject_id, ques) 
              VALUES('$quesNo', '$category_id', '$subject_id', '$ques')";
    $insert_row = $this->db->insert($query);

    if ($insert_row) {
        // ৩. Loop চালিয়ে tbl_ans টেবিলে ৪টি অপশন আলাদাভাবে Insert করা
        foreach ($ans as $key => $ansName) {
            if ($ansName != '') {
                $isRight = ($rightAns == $key) ? '1' : '0';
                
                $rquery = "INSERT INTO tbl_ans(quesNo, rightAns, ans) 
                           VALUES('$quesNo', '$isRight', '$ansName')";
                $this->db->insert($rquery);
            }
        }
        $msg = "<span class='text-emerald-600 font-semibold'>প্রশ্ন ও ৪টি অপশন সফলভাবে সেভ হয়েছে!</span>";
        return $msg;
    } else {
        $msg = "<span class='text-rose-600 font-semibold'>ডাটা সেভ করতে সমস্যা হয়েছে!</span>";
        return $msg;
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

    $whereCond = "";
    if ($category_id > 0) {
        $whereCond = "WHERE category_id = '$category_id'";
    }

    // ডাটাবেজ থেকে রেনডমলি প্রশ্ন নেওয়া
    $query  = "SELECT quesNo FROM tbl_ques $whereCond ORDER BY RAND() LIMIT $num_questions";
    $result = $this->db->select($query);

    $quesList = array();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $quesList[] = $row['quesNo'];
        }
    }

    // সেশনে ডেটা সেভ করা
    Session::set("exam_questions", $quesList);
    Session::set("exam_total_ques", count($quesList));
    Session::set("exam_time_limit", $time_limit);
    Session::set("exam_start_time", time());
    Session::set("exam_category_id", $category_id);
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
}


 ?>