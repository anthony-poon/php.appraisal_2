<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 9/8/2018
 * Time: 5:19 PM
 */

namespace App\Command;

use App\Entity\Appraisal\AppraisalPeriod;
use App\Entity\Appraisal\AppraisalResponse;
use App\Entity\Appraisal\AppVersion1;
use App\Entity\Base\SecurityGroup;
use App\Entity\Base\User;
use App\Entity\Department;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class MigrationCommand extends Command {
	private $em;
	private $encoder;
	private $userCache = [];
	private $appCache = [];
	private $periodCache = [];
	/**
	 * @var \PDO
	 */
	private $pdo;
	public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder) {
		$this->em = $entityManager;
		$this->encoder = $passwordEncoder;
		parent::__construct();
	}

	protected function configure() {
		$this->setName("app:migrate")
			->setDescription("Migrate from old database");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$helper = $this->getHelper("question");
		$dbname = $helper->ask($input, $output, new Question("Database name:", "pa_survey"));
		$host = $helper->ask($input, $output, new Question("Database host:", "localhost"));
		$username = $helper->ask($input, $output, new Question("Database username:", "root"));
		$pQuestion = new Question("Database password:", null);
		$pQuestion->setHidden(true);
		$pQuestion->setHiddenFallback(false);
		$password = $helper->ask($input, $output, $pQuestion);
		$this->pdo = new \PDO("mysql:dbname=$dbname;host=$host", $username, $password);
		$this->pdo->exec("set names utf8mb4");
		$this->migrateUser();
		$this->migrateAppraisal();
		$this->migratePartA();
		$this->migratePartB1();
		$this->migratePartB2();
		$this->migratePartD();
		$this->em->flush();
	}

	private function migrateUser() {
		$stm = "SELECT * FROM pa_user";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		$results = $query->fetchAll();
		$userRepo = $this->em->getRepository(User::class);
		$depRepo = $this->em->getRepository(Department::class);
		$secRepo = $this->em->getRepository(SecurityGroup::class);
		$adminGp = $secRepo->findOneBy(["name" => "Admin"]);
		$reportUserGp = $secRepo->findOneBy(["name" => "Report User"]);
		if (empty($adminGp)) {
			$adminGp = new SecurityGroup();
			$adminGp->setName("Admin");
			$adminGp->setSiteToken("ROLE_ADMIN");
		}
		if (empty($reportUserGp)) {
			$reportUserGp = new SecurityGroup();
			$reportUserGp->setName("Report User");
			$reportUserGp->setSiteToken("ROLE_REPORT_USER");
		}
		$depts = [];
		// Setup user and user right
		foreach ($results as $r) {
			$user = $userRepo->findOneBy(["username" => $r["username"]]);
			if (empty($user)){
				$user = new User();
			}
			$user->setUsername($r["username"]);
			$user->setFullName($r["user_full_name"]);
			$user->setEmail($r["user_email"]);
			//$user->setPassword($this->encoder->encodePassword($user, $r["user_password"]));
			$user->setPassword($this->encoder->encodePassword($user, "password"));
			$user->setIsSenior((bool) $r["is_senior"]);
			/* @var \App\Entity\Department $dept */
			// Get dept from cache first
			$dept = $depts[$r["user_department"]] ?? null;
			// If not in cache, find in db
			if (empty($dept)) {
				$dept = $depRepo->findOneBy(["name" => $r["user_department"]]);
			}
			// Else create new
			if (empty($dept)) {
				$dept = new Department();
				$dept->setName($r["user_department"]);
				$this->em->persist($dept);
			}
			$depts[$r["user_department"]] = $dept;
			$user->setDepartment($dept);
			if ((bool) $r["is_admin"] && !in_array("ROLE_ADMIN", $user->getRoles())) {
				$adminGp->getChildren()->add($user);
			}
			if ((bool) $r["is_report_user"] && !in_array("ROLE_REPORT_USER", $user->getRoles())) {
				$reportUserGp->getChildren()->add($user);
			}
			$user->setIsActive($r["is_active"] == true);
			// Cache entity in memory for later use
			$this->userCache[strtolower($user->getUsername())] = $user;
			$this->em->persist($user);
		}
		$this->em->persist($adminGp);
		$this->em->persist($reportUserGp);

		// Setup countersigner and appraiser

		$stm = "SELECT username, appraiser_username, countersigner_username_1, countersigner_username_2 FROM pa_user";
		$query = $this->pdo->query($stm);
		$query->execute();
		$results = $query->fetchAll();
		foreach ($results as $r) {
			/* @var User $user */
			/* @var User $app */
			/* @var User $counter1 */
			/* @var User $counter2 */
			$user = $this->userCache[strtolower($r["username"])];
			$app = $this->userCache[strtolower($r["appraiser_username"])] ?? null;
			$counter1 = $this->userCache[strtolower($r["countersigner_username_1"])] ?? null;
			$counter2 = $this->userCache[strtolower($r["countersigner_username_2"])] ?? null;
			if (!empty($app) && !$user->getAppraisers()->contains($app)) {
				$user->getAppraisers()->add($app);
			}
			if (!empty($counter1) && !$user->getCountersigners()->contains($counter1)) {
				$user->getCountersigners()->add($counter1);
			}
			if (!empty($counter2) && !$user->getCountersigners()->contains($counter2)) {
				$user->getCountersigners()->add($counter2);
			}
			$this->em->persist($user);
		}
	}

	private function migrateAppraisal() {
		$stm = "SELECT * FROM pa_form_period";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		$results = $query->fetchAll();
		$periodRepo = $this->em->getRepository(AppraisalPeriod::class);
		$this->periodCache = [];
		foreach ($results as $r) {
			$period = $periodRepo->findOneBy(["name" => $r["survey_period"]]);
			if (empty($period)) {
				$period = new AppraisalPeriod();
				$period->setName($r["survey_period"]);
				$period->setIsEnabled(false);
			}
			$this->periodCache[$r["survey_period"]] = $period;
			$this->em->persist($period);
		}
		$userRepo = $this->em->getRepository(User::class);
		$appRepo = $this->em->getRepository(AppVersion1::class);
		$stm = "
				SELECT form_username, p.survey_period, staff_name, staff_department, staff_office, staff_position, 
				core_competency_1, core_competency_2, core_competency_3, 
				function_training_0_to_1_year, function_training_1_to_2_year, function_training_2_to_3_year, 
				generic_training_0_to_1_year, generic_training_1_to_2_year, generic_training_2_to_3_year, 
				on_job_0_to_1_year, on_job_1_to_2_year, on_job_2_to_3_year, 
				prof_competency_1, prof_competency_2, prof_competency_3, is_senior, 
				appraiser_name, countersigner_name, survey_commencement_date, 
				countersigner_1_part_a_score, countersigner_1_part_b_score, countersigner_2_part_a_score, countersigner_2_part_b_score, 
				countersigner_1_name, countersigner_2_name, countersigner_1_weight, countersigner_2_weight, 
				part_a_b_total, part_a_total, part_b_total, 
				part_a_overall_score, part_b1_overall_comment, part_b1_overall_score, part_b2_overall_comment, part_b2_overall_score, 
				survey_overall_comment
				FROM pa_form_data as p
				LEFT JOIN pa_form_period as period 
				ON p.survey_uid = period.uid";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		while (($r = $query->fetch(\PDO::FETCH_ASSOC)) != null) {
			/* @var \App\Entity\Base\User $user */
			/* @var \App\Entity\Appraisal\AppraisalPeriod $period */
			/* @var \App\Entity\Appraisal\AppVersion1 $app */
			$user = $this->userCache[strtolower($r["form_username"])];
			$period = $this->periodCache[$r["survey_period"]];
			$app = $appRepo->findOneBy([
				"owner" => $user->getId(),
				"period" => $period->getId(),
			]);
			if (!$user) {
				throw new \Exception("Unable to retrieve user by query: ".$r["form_username"]);
			}
			if (!$period) {
				throw new \Exception("Unable to retrieve survey period by query: ".$r["survey_period"]);
			}
			// Might not need to separate ctn and main json
			$ctn1 = $userRepo->findOneBy(["fullName" => $r["countersigner_1_name"]]);
			$ctn2 = $userRepo->findOneBy(["fullName" => $r["countersigner_2_name"]]);
			if ($r["countersigner_1_name"] && !$ctn1) {
				throw new \Exception("Unable to query ctn1");
			}
			if ($r["countersigner_2_name"] && !$ctn2) {
				throw new \Exception("Unable to query ctn2");
			}
			if (empty($app)) {
				$app = new AppVersion1();
			}
			$app->setIsLocked(true);
			$app->setPeriod($period);
			$app->setOwner($user);
			$app->setJsonData($r);
			$this->appCache[strtolower($r["form_username"])][$r["survey_period"]] = $app;
			$this->em->persist($app);
		}
	}

	private function migratePartA() {
		$stm = "SELECT p_a.form_username, p_a.survey_uid, question_no, 
				respon_name, respon_result, respon_comment, respon_weight, 
				respon_score, period.survey_period, app_profile.username as app_username, appraiser_name, countersigner_1_name, countersigner_2_name 
				FROM pa_part_a as p_a
				LEFT JOIN pa_form_period as period 
				ON p_a.survey_uid = period.uid 
				LEFT JOIN pa_form_data as p 
				ON p_a.form_username = p.form_username AND p_a.survey_uid = p.survey_uid 
				LEFT JOIN pa_user as app_profile ON 
				p.appraiser_name = app_profile.user_full_name";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		$parsedResult = [];
		while (($r = $query->fetch()) != null) {
			$parsedResult[$r["form_username"]][$r["survey_period"]]["app_username"] = $r["app_username"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_1_name"] = $r["countersigner_1_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_2_name"] = $r["countersigner_2_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["part_a"][$r["question_no"]] = [
				"respon_name" => $r["respon_name"],
				"respon_result" => $r["respon_result"],
				"respon_comment" => $r["respon_comment"],
				"respon_weight" => $r["respon_weight"],
				"respon_score" => $r["respon_score"],
			];
		}
		foreach ($parsedResult as $username => $arr) {
			foreach ($arr as $periodName => $data) {
				/* @var \App\Entity\Base\User $user */
				/* @var \App\Entity\Appraisal\AppraisalPeriod $period */
				/* @var \App\Entity\Appraisal\AppVersion1 $app */
				$user = $this->userCache[strtolower($username)];
				$period = $this->periodCache[$periodName];
				$app = $this->appCache[strtolower($username)][$periodName];
				if (!$user) {
					throw new \Exception("Unable to retrieve user by query: ".$username);
				}
				if (!$period) {
					throw new \Exception("Unable to retrieve survey period by query: ".$periodName);
				}
				if (!$app) {
					throw new \Exception("Unable to retrieve appraisal where owner = ".$user->getId(). " and period = ". $period->getId());
				}
				$ownerResponse = $app->getResponses()->filter(function(AppraisalResponse $rsp) use ($user) {
					return $rsp->getOwner() === $user;
				})->first();
				if (empty($ownerResponse)) {
					$ownerResponse = new AppraisalResponse();
					$ownerResponse->setOwner($user);
					$ownerResponse->setResponseType("owner");
					$ownerResponse->setAppraisal($app);
					$app->getResponses()->add($ownerResponse);
				}
				$json = $ownerResponse->getJsonData();
				$json["part_a"] = [];
				foreach ($data["part_a"] as $qNo => $q) {
					$json["part_a"]["q_".$qNo] = [
						"respon_name" => $q["respon_name"],
						"respon_result" => $q["respon_result"],
					];
				}
				$ownerResponse->setJsonData($json);
				$this->em->persist($ownerResponse);

				/* @var User $appraiser */
				if ($data["app_username"]) {
					$appraiser = $this->userCache[strtolower($data["app_username"])];
					if (!$appraiser) {
						throw new \Exception("Query :".$data["app_username"]." return no user");
					} else {
						$appraiserResponse = $app->getResponses()->filter(function (AppraisalResponse $rsp) use ($appraiser) {
							return $rsp->getOwner() === $appraiser;
						})->first();
						if (empty($appraiserResponse)) {
							$appraiserResponse = new AppraisalResponse();
							$appraiserResponse->setOwner($appraiser);
							$appraiserResponse->setResponseType("appraiser");
							$appraiserResponse->setAppraisal($app);
							$app->getResponses()->add($appraiserResponse);
						}
						$json = $appraiserResponse->getJsonData();
						$json["part_a"] = [];
						foreach ($data["part_a"] as $qNo => $q) {
							$json["part_a"]["q_".$qNo] = [
								"respon_comment" => $q["respon_comment"],
								"respon_weight" => $q["respon_weight"],
								"respon_score" => $q["respon_score"],
							];
						}
						$appraiserResponse->setJsonData($json);
						$this->em->persist($appraiserResponse);
					}
				}
			}
		}
	}

	private function migratePartB1() {
		$stm = "SELECT p_b1.form_username, p_b1.survey_uid, app_profile.username as app_username, appraiser_name, question_no, self_example, self_score, appraiser_example, appraiser_score, period.survey_period, appraiser_name, countersigner_1_name, countersigner_2_name 
				FROM pa_part_b1 as p_b1
				LEFT JOIN pa_form_period as period 
				ON p_b1.survey_uid = period.uid 
				LEFT JOIN pa_form_data as p 
				ON p_b1.form_username = p.form_username AND p_b1.survey_uid = p.survey_uid 
				LEFT JOIN pa_user as app_profile ON 
				p.appraiser_name = app_profile.user_full_name";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		$parsedResult = [];
		while (($r = $query->fetch()) != null) {
			$parsedResult[$r["form_username"]][$r["survey_period"]]["app_username"] = $r["app_username"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_1_name"] = $r["countersigner_1_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_2_name"] = $r["countersigner_2_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["part_b1"][$r["question_no"]] = [
				"self_example" => $r["self_example"],
				"self_score" => $r["self_score"],
				"appraiser_example" => $r["appraiser_example"],
				"appraiser_score" => $r["appraiser_score"],
			];
		}
		foreach ($parsedResult as $username => $arr) {
			foreach ($arr as $periodName => $data) {
				/* @var \App\Entity\Base\User $user */
				/* @var \App\Entity\Appraisal\AppraisalPeriod $period */
				/* @var \App\Entity\Appraisal\AppVersion1 $app */
				$user = $this->userCache[strtolower($username)];
				$period = $this->periodCache[$periodName];
				$app = $this->appCache[strtolower($username)][$periodName];
				if (!$user) {
					throw new \Exception("Unable to retrieve user by query: ".$username);
				}
				if (!$period) {
					throw new \Exception("Unable to retrieve survey period by query: ".$periodName);
				}
				if (!$app) {
					throw new \Exception("Unable to retrieve appraisal where owner = ".$user->getId(). " and period = ". $period->getId());
				}

				$ownerResponse = $app->getResponses()->filter(function(AppraisalResponse $rsp) use ($user) {
					return $rsp->getOwner() === $user;
				})->first();
				if (empty($ownerResponse)) {
					$ownerResponse = new AppraisalResponse();
					$ownerResponse->setOwner($user);
					$ownerResponse->setResponseType("owner");
					$ownerResponse->setAppraisal($app);
					$app->getResponses()->add($ownerResponse);
				}
				$json = $ownerResponse->getJsonData();
				$json["part_b1"] = [];
				foreach ($data["part_b1"] as $qNo => $q) {
					$json["part_b1"]["q_".$qNo] = [
						"self_example" => $q["self_example"],
						"self_score" => $q["self_score"],
					];
				}
				$ownerResponse->setJsonData($json);
				$this->em->persist($ownerResponse);

				/* @var User $appraiser */
				if ($data["app_username"]) {
					$appraiser = $this->userCache[strtolower($data["app_username"])];
					if (!$appraiser) {
						throw new \Exception("Query :" . $data["app_username"] . " return no user");
					} else {
						$appraiserResponse = $app->getResponses()->filter(function (AppraisalResponse $rsp) use ($appraiser) {
							return $rsp->getOwner() === $appraiser;
						})->first();
						if (empty($appraiserResponse)) {
							$appraiserResponse = new AppraisalResponse();
							$appraiserResponse->setOwner($appraiser);
							$appraiserResponse->setResponseType("appraiser");
							$appraiserResponse->setAppraisal($app);
						}
						$json = $appraiserResponse->getJsonData();
						$json["part_b1"] = [];
						foreach ($data["part_b1"] as $qNo => $q) {
							$json["part_b1"]["q_".$qNo] = [
								"appraiser_example" => $q["appraiser_example"],
								"appraiser_score" => $q["appraiser_score"],
							];
						}
						$appraiserResponse->setJsonData($json);
						$this->em->persist($appraiserResponse);
					}
				}
			}
		}
	}

	private function migratePartB2() {
		$stm = "SELECT p_b2.form_username, p_b2.survey_uid, app_profile.username as app_username, appraiser_name, question_no, self_example, self_score, appraiser_example, appraiser_score, period.survey_period, appraiser_name, countersigner_1_name, countersigner_2_name 
				FROM pa_part_b2 as p_b2
				LEFT JOIN pa_form_period as period 
				ON p_b2.survey_uid = period.uid 
				LEFT JOIN pa_form_data as p 
				ON p_b2.form_username = p.form_username AND p_b2.survey_uid = p.survey_uid 
				LEFT JOIN pa_user as app_profile ON 
				p.appraiser_name = app_profile.user_full_name";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		$parsedResult = [];
		while (($r = $query->fetch()) != null) {
			$parsedResult[$r["form_username"]][$r["survey_period"]]["app_username"] = $r["app_username"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_1_name"] = $r["countersigner_1_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_2_name"] = $r["countersigner_2_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["part_b2"][$r["question_no"]] = [
				"self_example" => $r["self_example"],
				"self_score" => $r["self_score"],
				"appraiser_example" => $r["appraiser_example"],
				"appraiser_score" => $r["appraiser_score"],
			];
		}
		foreach ($parsedResult as $username => $arr) {
			foreach ($arr as $periodName => $data) {
				/* @var \App\Entity\Base\User $user */
				/* @var \App\Entity\Appraisal\AppraisalPeriod $period */
				/* @var \App\Entity\Appraisal\AppVersion1 $app */
				$user = $this->userCache[strtolower($username)];
				$period = $this->periodCache[$periodName];
				$app = $this->appCache[strtolower($username)][$periodName];
				if (!$user) {
					throw new \Exception("Unable to retrieve user by query: ".$username);
				}
				if (!$period) {
					throw new \Exception("Unable to retrieve survey period by query: ".$periodName);
				}
				if (!$app) {
					throw new \Exception("Unable to retrieve appraisal where owner = ".$user->getId(). " and period = ". $period->getId());
				}

				$ownerResponse = $app->getResponses()->filter(function(AppraisalResponse $rsp) use ($user) {
					return $rsp->getOwner() === $user;
				})->first();

				if (empty($ownerResponse)) {
					$ownerResponse = new AppraisalResponse();
					$ownerResponse->setOwner($user);
					$ownerResponse->setResponseType("owner");
					$ownerResponse->setAppraisal($app);
					$app->getResponses()->add($ownerResponse);
				}
				$json = $ownerResponse->getJsonData();
				$json["part_b2"] = [];
				foreach ($data["part_b2"] as $qNo => $q) {
					$json["part_b2"]["q_".$qNo] = [
						"self_example" => $q["self_example"],
						"self_score" => $q["self_score"],
					];
				}
				$ownerResponse->setJsonData($json);
				$this->em->persist($ownerResponse);

				/* @var User $appraiser */
				if ($data["app_username"]) {
					$appraiser = $this->userCache[strtolower($data["app_username"])];
					if (!$appraiser) {
						throw new \Exception("Query :" . $data["app_username"] . " return no user");
					} else {
						$appraiserResponse = $app->getResponses()->filter(function (AppraisalResponse $rsp) use ($appraiser) {
							return $rsp->getOwner() === $appraiser;
						})->first();
						if (empty($appraiserResponse)) {
							$appraiserResponse = new AppraisalResponse();
							$appraiserResponse->setOwner($appraiser);
							$appraiserResponse->setResponseType("appraiser");
							$appraiserResponse->setAppraisal($app);
							$app->getResponses()->add($appraiserResponse);
						}
						$json = $appraiserResponse->getJsonData();
						$json["part_b2"] = [];
						foreach ($data["part_b2"] as $qNo => $q) {
							$json["part_b2"]["q_".$qNo] = [
								"appraiser_example" => $q["appraiser_example"],
								"appraiser_score" => $q["appraiser_score"],
							];
						}
						$appraiserResponse->setJsonData($json);
						$this->em->persist($appraiserResponse);
					}
				}
			}
		}
	}

	private function migratePartD() {
		$stm = "SELECT p_d.form_username, p_d.survey_uid, app_profile.username as app_username, appraiser_name, question_no, 
				key_respon, goal_name, measurement_name, goal_weight, complete_date, period.survey_period, appraiser_name, countersigner_1_name, countersigner_2_name 
				FROM pa_part_d as p_d
				LEFT JOIN pa_form_period as period 
				ON p_d.survey_uid = period.uid 
				LEFT JOIN pa_form_data as p 
				ON p_d.form_username = p.form_username AND p_d.survey_uid = p.survey_uid 
				LEFT JOIN pa_user as app_profile ON 
				p.appraiser_name = app_profile.user_full_name";
		$query = $this->pdo->prepare($stm);
		$query->execute();
		$parsedResult = [];
		while (($r = $query->fetch()) != null) {
			$parsedResult[$r["form_username"]][$r["survey_period"]]["app_username"] = $r["app_username"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_1_name"] = $r["countersigner_1_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["countersigner_2_name"] = $r["countersigner_2_name"];
			$parsedResult[$r["form_username"]][$r["survey_period"]]["part_d"][$r["question_no"]] = [
				"key_respon" => $r["key_respon"],
				"goal_name" => $r["goal_name"],
				"measurement_name" => $r["measurement_name"],
				"goal_weight" => $r["goal_weight"],
				"complete_date" => $r["complete_date"],
			];
		}
		foreach ($parsedResult as $username => $arr) {
			foreach ($arr as $periodName => $data) {
				/* @var \App\Entity\Base\User $user */
				/* @var \App\Entity\Appraisal\AppraisalPeriod $period */
				/* @var \App\Entity\Appraisal\AppVersion1 $app */
				$user = $this->userCache[strtolower($username)];
				$period = $this->periodCache[$periodName];
				$app = $this->appCache[strtolower($username)][$periodName];
				if (!$user) {
					throw new \Exception("Unable to retrieve user by query: ".$username);
				}
				if (!$period) {
					throw new \Exception("Unable to retrieve survey period by query: ".$periodName);
				}
				if (!$app) {
					throw new \Exception("Unable to retrieve appraisal where owner = ".$user->getId(). " and period = ". $period->getId());
				}

				/* @var User $appraiser */
				if ($data["app_username"]) {
					$appraiser = $this->userCache[strtolower($data["app_username"])];
					if (!$appraiser) {
						throw new \Exception("Query :" . $data["app_username"] . " return no user");
					} else {
						$appraiserResponse = $app->getResponses()->filter(function (AppraisalResponse $rsp) use ($appraiser) {
							return $rsp->getOwner() === $appraiser;
						})->first();
						if (empty($appraiserResponse)) {
							$appraiserResponse = new AppraisalResponse();
							$appraiserResponse->setOwner($appraiser);
							$appraiserResponse->setResponseType("appraiser");
							$appraiserResponse->setAppraisal($app);
						}
						$json = $appraiserResponse->getJsonData();
						$json["part_d"] = [];
						foreach ($data["part_d"] as $qNo => $q) {
							$json["part_d"]["q_".$qNo] = [
								"key_respon" => $q["key_respon"],
								"goal_name" => $q["goal_name"],
								"measurement_name" => $q["measurement_name"],
								"goal_weight" => $q["goal_weight"],
								"complete_date" => $q["complete_date"],
							];
						}
						$appraiserResponse->setJsonData($json);
						$this->em->persist($appraiserResponse);
					}
				}
			}
		}
	}
}