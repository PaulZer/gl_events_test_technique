<?php

namespace App\Tests\Service\Mailer;

use App\Service\Mailer\SendTransformedCSVMailService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mime\Email;

class SendTransformedCSVMailServiceTest extends KernelTestCase
{
    use MailerAssertionsTrait;
    public function testSendCSVByEmail()
    {
        # (1) boot the Symfony kernel
        self::bootKernel();

        # (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        # (3) get the service
        $csvMailerService = $container->get(SendTransformedCSVMailService::class);

        $fileName = 'test_upload.csv';

        $csvMailerServiceMock = $this->createMock(SendTransformedCSVMailService::class);
        $csvMailerServiceMock->expects(self::once())
             ->method('getCSVFilePath')
             ->willReturn(__DIR__.'/../../../fixtures/files/test_upload.csv')
        ;

        $csvMailerService->sendCSVByEmail($fileName);

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();

        $this->assertEmailTextBodyContains($email, ':D');
    }
}