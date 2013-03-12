<?php

namespace CiscoSystems\AuditBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CiscoSystems\AuditBundle\Entity\AuditScore;
use CiscoSystems\AuditBundle\Entity\AuditFormField;

class AuditFormFieldType extends AbstractType
{
    const SCORE_YES = 'answer_yes';
    const SCORE_NO = 'answer_no';
    const SCORE_ACCEPTABLE = 'answer_acceptable';
    const SCORE_NOT_APPLICABLE = 'answer_not_applicable';

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $scores = $options['data']->getScores();
        $builder->add( 'id', 'hidden', array(
            'mapped'        => false
        ));
        $builder->add( 'title', 'textarea', array(
            'attr'          => array( 'placeholder'=> 'Title for this field'),
            'required'      => true,
        ));
        $builder->add( 'section', 'entity', array(
            'required'      => false,
            'class'         => 'CiscoSystemsAuditBundle:AuditFormSection',
            'property'      => 'title',
            'empty_value'   => '(Choose a Section)',
        ));
        $builder->add( 'weight', 'integer' );
        $builder->add( 'flag', 'checkbox', array(
            'label'         => 'Should this field raise a flag?',
            'required'      => false,
        ));
        $builder->add( 'description', 'textarea', array(
            'attr'          => array( 'placeholder' => 'Description for the field. This should be as clear as possible' ),
        ));
        $builder->add( self::SCORE_YES, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[AuditScore::YES] ) ? $scores[AuditScore::YES] : '',
            'attr'          => array( 'placeholder' => 'Correct answer definition' ),
        ));
        $builder->add( self::SCORE_NO, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[AuditScore::NO] ) ? $scores[AuditScore::NO] : '',
            'attr'          => array( 'placeholder'=> 'Incorrect answer definition' ),
        ));
        $builder->add( self::SCORE_ACCEPTABLE, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[AuditScore::ACCEPTABLE] ) ? $scores[AuditScore::ACCEPTABLE] : '',
            'attr'          => array( 'placeholder'=> 'Partially correct answer definition' ),
            'label'         => 'Acceptable',
        ));
        $builder->add( self::SCORE_NOT_APPLICABLE, 'textarea', array(
            'mapped'        => false,
            'required'      => false,
            'data'          => isset( $scores[AuditScore::NOT_APPLICABLE] ) ? $scores[AuditScore::NOT_APPLICABLE] : '',
            'attr'          => array( 'placeholder'=> 'Answer not applicable' ),
            'label'         => 'N/A',
        ));
    }

    public function getName()
    {
        return 'field';
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'CiscoSystems\AuditBundle\Entity\AuditFormField',
        ));
    }

    /**
     * Convenience method for setting a non-mapped field from the form data
     *
     * @param CiscoSystems\AuditBundle\Entity\AuditFormField $entity
     * @param array $values
     */
    static public function mapScores( AuditFormField $entity, $values )
    {
        $extraFields = array(
            AuditScore::YES => self::SCORE_YES,
            AuditScore::NO => self::SCORE_NO,
            AuditScore::ACCEPTABLE => self::SCORE_ACCEPTABLE,
            AuditScore::NOT_APPLICABLE => self::SCORE_NOT_APPLICABLE,
        );
        foreach ( $extraFields as $key => $extraField )
        {
            if ( isset( $values[ $extraField ] ) && $values[ $extraField ] )
            {
                $entity->addScore( $key, $values[ $extraField ] );
            }
        }
    }
}
