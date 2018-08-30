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
		$repo = $this->em->getRepository(AppVersion1::class);
		$this->em->beginTransaction();
		$user1 = new User();
		$user1->setUsername("abcd");
		$user1->setFullName("abcd");
		$user1->setPassword("nan");
		$user1->setEmail("asdf");
		$user2 = new User();
		$user2->setUsername("abcdd");
		$user2->setFullName("abcd");
		$user2->setPassword("nan");
		$user2->setEmail("asdfs");
		$this->em->persist($user1);
		$this->em->persist($user2);
		$this->em->flush();
		$id = $user2->getId();
		var_dump($id);
		$app = new AppVersion1();
		$app->setJsonData([]);
		$app->setOwner($user1);
		$this->em->persist($app);
		$this->em->flush();
		$response = new AppraisalResponse();
		$response->setOwner($user2);
		$response->setJsonData(["OK"]);
		$response->setAppraisal($app);
		$response->setResponseType("TESTING");
		$this->em->persist($response);
		$this->em->flush();
		$app = $repo->findOneBy(["owner" => $user1->getId()]);

		var_dump($app);
		$this->em->rollback();
	}


}