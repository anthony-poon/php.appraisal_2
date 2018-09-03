<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 17/8/2018
 * Time: 2:47 PM
 */

namespace App\Command;


use App\Entity\Appraisal\AppraisalAbstract;
use App\Entity\Appraisal\AppraisalResponse;
use App\Entity\Appraisal\AppVersion1;
use App\Entity\Base\User;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TempCommand extends Command {
	private $em;
	private $pdo;
	public function __construct(EntityManagerInterface $entityManager) {
		$this->em = $entityManager;
		parent::__construct();
	}

	protected function configure() {
		$this->setName("app:temp")
			->setDescription("Temp");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$str = "aaseasdf[grp_1][grp2][grp3]";
		$rtn = [];
		$ptr = &$rtn;
		if (preg_match_all("/\[([\w\d_\-]+)\]/", $str, $delimited)) {
			// Capture group is stored in [1]
			$captureGrp = $delimited[1];
			for ($i = 0; $i < count($captureGrp); $i++) {
				// If is last element
				if ($i == count($captureGrp) - 1) {
					$ptr[$captureGrp[$i]] = "abc";
				} else {
					$ptr[$captureGrp[$i]] = [];
				}
				// Walk 1 level deeper
				$ptr = &$ptr[$captureGrp[$i]];
			}
		}
		var_dump($rtn);
	}


}