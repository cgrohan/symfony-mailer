<?php

namespace App\Controller;

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
        $contactFormDTO = new ContactType();

        $form = $this->createForm(ContactType::class, $contactFormDTO);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
            ->from($contactFormDTO->getEmail())
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Demande de contact')
            ->text($contactFormDTO->getMessage())
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        }

        return $this->render('mailer/index.html.twig', [
            'form' => $form,
        ]);
    }
}
