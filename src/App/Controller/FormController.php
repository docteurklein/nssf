<?php

namespace App\Controller;

use Knp\RadBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\Form;
use App\Document;

class FormController extends BaseController
{
    public function indexAction(\Iterator $forms)
    {
        return [
            'forms' => $forms
        ];
    }


    public function showAction(Document\Form $formDocument)
    {
        return [
            'formDocument' => $formDocument
        ];
    }

    public function newAction(Form $form)
    {
        return [
            'form' => $form->createView()
        ];
    }

    public function editAction(Form $form)
    {
        return [
            'form' => $form->createView(),
            'formDocument' => $form->getData(),
        ];
    }

    public function createAction(Form $form)
    {
        if ($form->isValid()) {

            $this->get('doctrine_mongodb.odm.default_document_manager')->persist($form->getData());
            $this->get('doctrine_mongodb.odm.default_document_manager')->flush();

            return $this->redirectToRoute('app_form_index');
        }

        return [
            'form' => $form->createView(),
            'formDocument' => $form->getData(),
        ];
    }

    public function updateAction(Form $form)
    {
        return $this->createAction($form);
    }
}

