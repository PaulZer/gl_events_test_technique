<?php

namespace App\Service\Mailer;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendTransformedCSVMailService
{
    private string $transformedCSVDirectory;
    private string $emailAddressTo;
    private MailerInterface $mailer;
    public function __construct(ParameterBagInterface $params, MailerInterface $mailer)
    {
        $this->transformedCSVDirectory = $params->get('transformed_csv_directory');
        $this->emailAddressTo = $params->get('email_address_to');
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendCSVByEmail(string $csvFileName): void
    {
        $email = (new Email())
            ->from('noreply@app.com')
            ->to($this->emailAddressTo)
            ->subject('A new file have been processed asyncronously !')
            ->text(':D')
            ->attachFromPath($this->getCSVFilePath($csvFileName));

        $this->mailer->send($email);
    }

    public function getCSVFilePath(string $csvFileName): string
    {
        return $this->transformedCSVDirectory.$csvFileName;
    }
}