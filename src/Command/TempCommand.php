<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 17/8/2018
 * Time: 2:47 PM
 */

namespace App\Command;


use App\Entity\Appraisal\AppraisalAbstract;
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
		$repo = $this->em->getRepository(AppraisalAbstract::class);
		$app = $repo->find(8);
		/* @var \App\Entity\Appraisal\AppraisalAbstract $app */
		var_dump($app->getRenderData());
	}


}