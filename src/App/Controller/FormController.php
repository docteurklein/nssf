<?php

namespace App\Controller;

use Knp\RadBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\Form;
use App\Mongo;
use App\Mongo\Form as FormDocument;

class FormController extends BaseController
{
    public function indexAction(\Iterator $forms)
    {
        return [
            'forms' => $forms
        ];
    }

    public function showAction(FormDocument $formDocument)
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

            $this->get('mongodb.form_repository')->save($form->getData());

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

