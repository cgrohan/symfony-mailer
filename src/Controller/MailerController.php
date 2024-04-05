<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'mailer')]
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {
        $data = new ContactDTO();

        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $service = $data->service;

            if ($service == 'technique') {
                $service = 'technique@domain.fr';
            } elseif ($service == 'rh') {
                $service = 'rh@domain.fr';
            } else {
                $service = 'direction@domain.fr';
            }

            $email = (new Email())
                ->from($data->email)
                ->to($service)
                ->html($data->message)
                ->subject('Demande de contact');

            $mailer->send($email);

            $this->addFlash('success', 'Nous avons bien reçu votre message.');

            return $this->redirectToRoute('mailer');
        }

        return $this->render('mailer/index.html.twig', [
            'form' => $form,
        ]);
    }
}
