<?php 
 $filepath = realpath(dirname(__FILE__));
 include_once ($filepath.'/../lib/Session.php');
	//Session::init();
include_once ($filepath.'/../lib/Database.php');
include_once ($filepath.'/../helpers/Format.php');

class Process{
	private $db;
	private $fm;
	function __construct()
	{
		$this->db = new Database();
		$this->fm = new Format();
	}

	public function processData($data){
		$selectedAns    = $this->fm->validation($data['ans']);
		$number         = $this->fm->validation($data['number']);
		$selectedAns    = mysqli_real_escape_string($this->db->link,$selectedAns);
		$number         = mysqli_real_escape_string($this->db->link,$number);
		$next           = $number+1;

		if (!isset($_SESSION['score'])) {
			$_SESSION['score'] = '0';
		}

		$total = $this->getTotal();
		$right = $this->rightAns($number);
		if ($right == $selectedAns) {
			$_SESSION['score']++;
		}
		if ($number == $total) {
			header("Location:final.php");
			exit();
		}else{
			header("Location:test.php?q=".$next);
		}

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

	private function getTotal(){
	$query = "SELECT * FROM tbl_ques";
    $getResult = $this->db->select($query);
    $total = $getResult->num_rows;
    return $total;

	}
	private function rightAns($number){
	$query = "SELECT * FROM tbl_ans WHERE quesNo = '$number' AND rightAns = '1'";
    $getdata = $this->db->select($query)->fetch_assoc();
    $result = $getdata['id'];
    return $result;
	}

	public function insertExamHistory() {
		$userId       = Session::get("userId");
		$categoryId   = Session::get("exam_category_id") ? Session::get("exam_category_id") : 1;
		$totalQues    = Session::get("exam_total_ques");
		$correctAns   = Session::get("score") ? Session::get("score") : 0;
		$wrongAns     = $totalQues - $correctAns;
		$totalMarks   = $correctAns; // যদি প্রতি প্রশ্নে ১ মার্ক হয়
		$percentage   = ($totalQues > 0) ? ($correctAns / $totalQues) * 100 : 0;
		$status       = ($percentage >= 40) ? 'Passed' : 'Failed';
		
		// ইউনিক অ্যাটেম্পট কোড জেনারেট
		$attemptCode  = "EXM-" . date("Ymd") . "-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

		$query = "INSERT INTO tbl_exam_history 
				(attempt_code, user_id, category_id, subject_id, total_questions, correct_answers, wrong_answers, total_marks, percentage, status) 
				VALUES 
				('$attemptCode', '$userId', '$categoryId', 1, '$totalQues', '$correctAns', '$wrongAns', '$totalMarks', '$percentage', '$status')";

		return $this->db->insert($query);
	}

}


 ?>