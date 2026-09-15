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

	public function processData($data, $step, $total) {
    $selectedAns = isset($data['ans']) ? (int)$data['ans'] : 0;
    $number      = (int)$data['number'];

    // সঠিক উত্তর চেক করা
    $query = "SELECT * FROM tbl_ans WHERE quesNo = '$number' AND rightAns = '1'";
    $getAns = $this->db->select($query)->fetch_assoc();

    if ($getAns && $getAns['id'] == $selectedAns) {
        $score = Session::get("score");
        Session::set("score", $score + 1);
    }

    // নেক্সট স্টেপ রিডাইরেকশন
    $nextStep = $step + 1;
    if ($nextStep > $total) {
        header("Location: final.php");
        exit();
    } else {
        header("Location: test.php?q=" . $nextStep);
        exit();
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

}


 ?>