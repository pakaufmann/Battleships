<?php
namespace kufi\BattleshipBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MultiplayerForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add("name");
    }
    
    public function getName()
    {
        return "multiplayerForm";
    }
}