<?php

namespace WG\AuditBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WG\AuditBundle\Entity\AuditForm;
use WG\AuditBundle\Form\Type\AuditFormType;

class AuditFormController extends Controller
{

    public function indexAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        $formlist = $repo->findAll();
        $newform = new AuditForm();
        $form = $this->createForm( new AuditFormType(), $newform );
        if ( null !== $request->get( $form->getName() ) )
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $newform );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'wgauditforms' ) );
            }
        }
        return $this->render( 'WGAuditBundle:AuditForm:index.html.twig', array(
            'forms' => $formlist,
            'form'  => $form->createView(),
        ));
    }

    public function viewAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        if ( null === $form = $repo->find( $request->get( 'id' ) ))
        {
            throw $this->createNotFoundException( 'Form does not exist' );
        }
        return $this->render( 'WGAuditBundle:AuditForm:view.html.twig', array(
            'form' => $form,
        ));
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        $formlist = $repo->findAll();
        return $this->render( 'WGAuditBundle:AuditForm:list.html.twig', array(
            'forms' => $formlist,
        ));
    }

    public function editAction( Request $request )
    {
        $edit = false;
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        $auditform = new AuditForm();
        if ( $request->get( 'id' ) )
        {
            $edit = true;
            $auditform = $repo->find( $request->get( 'id' ));
        }
        $form = $this->createForm( new AuditFormType(), $auditform);
        if ( null !== $request->get( $form->getName() ))
        {
            $form->bind( $request );
            if ( $form->isValid() )
            {
                $em->persist( $auditform );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'wgauditforms' ));
            }
        }
        return $this->render( 'WGAuditBundle:AuditForm:edit.html.twig', array(
            'edit'      => $edit,
            'auditform' => $auditform,
            'form'      => $form->createView(),
        ));
    }

    public function deleteAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        if ( null !== $form = $repo->find( $request->get( 'id' ) ))
        {
            $em->remove( $form );
            $em->flush();
            return $this->redirect( $this->generateUrl( 'wgauditforms' ));
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    public function removeAction( Request $request )
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository( 'WGAuditBundle:AuditForm' );
        $auditform = $repo->find( $request->get( 'id' ));
        if ( null !== $auditform )
        {
            $sectionRep = $em->getRepository( 'WGAuditBundle:AuditFormSection' );
            $section = $sectionRep->find( $request->get( 'section_id' ));
            if ( null !== $section )
            {
                $auditform->removeSection( $section );
                $em->persist( $auditform );
                $em->flush();
                return $this->redirect( $this->generateUrl( 'wgauditforms' ));
            }
            throw $this->createNotFoundException( 'Section does not exist' );
        }
        throw $this->createNotFoundException( 'Form does not exist' );
    }

    public function removeFieldAction()
    {
        
    }
}
