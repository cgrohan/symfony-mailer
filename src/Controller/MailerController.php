<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
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

            try {
                $email = (new TemplatedEmail())
                ->from($data->email)
                ->to($data->service)
                ->htmlTemplate('mailer/_template_mail.html.twig')
                ->context([
                    'data' => $data
                ])
                ->subject('Demande de contact')
                ;

                $mailer->send($email);

                $this->addFlash('success', 'Nous avons bien reçu votre message.');
                return $this->redirectToRoute('mailer');

            } catch (TransportExceptionInterface | \Exception $e) {
                $this->addFlash('danger', 'Votre mail n\'a pas pu s\'envoyer.');
                $this->addFlash('warning', $e);
            }
        }

        return $this->render('mailer/index.html.twig', [
            'form' => $form,
        ]);
    }
}
