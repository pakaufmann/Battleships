<?php
namespace kufi\BattleshipBundle\Controller;

use JMS\DiExtraBundle\Annotation\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
	/**
	 * @Route("/statistics", name="bs_statistics")
	 * @Template()
	 */
	public function statisticsAction()
	{
		return array("users" => $this->get("userRepository")->getAllUsers());
	}
	
	/**
	 * @Route("/statistics/rss", name="bs_statistics_rss", defaults={"_format"="rss"})
	 * @Template()
	 */
	public function statisticsRssAction()
	{
	    return array("users" => $this->get("userRepository")->getAllUsers());
	}
}