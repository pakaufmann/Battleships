<?php
namespace kufi\BattleshipBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class GameOptionForm extends AbstractType
{
	private $difficulties;
	
	public function __construct($difficulties)
	{
		$this->difficulties = $difficulties;
	}
	
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add("difficulty", "choice", array(
			"choices" => $this->difficulties,
			"required" => true
		));
	}

	public function getName()
	{
		return "gameOptionForm";
	}}
