<?php
namespace kufi\BattleshipBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class UserLoginForm extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add("username");
		$builder->add("password", "password");
		$builder->add("firstname");
		$builder->add("lastname");
	}
	
	public function getName()
	{
		return "userLoginForm";
	}
}