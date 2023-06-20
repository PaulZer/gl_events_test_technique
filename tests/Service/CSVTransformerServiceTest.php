<?php

namespace App\Tests\Service;

use App\Service\CSVTransformerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CSVTransformerServiceTest extends KernelTestCase
{
    public function testCSVTransformation()
    {
        # (1) boot the Symfony kernel
        self::bootKernel();

        # (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        # (3) get the service
        $csvTransformerService = $container->get(CSVTransformerService::class);

        # (4) test each of the service methods
        $row = ['test@valid.com', '', ''];
        $csvTransformerService->deleteInvalidEmails($row);
        $this->assertEquals($row[CSVTransformerService::EMAIL_COLUMN], 'test@valid.com');

        $row = ['@invalid.com', '', ''];
        $csvTransformerService->deleteInvalidEmails($row);
        $this->assertEquals($row[CSVTransformerService::EMAIL_COLUMN], '');

        $row = ['test@valid.com', 'lastname', 'Firstname'];
        $csvTransformerService->nameToUppercase($row);
        $this->assertEquals($row[CSVTransformerService::LAST_NAME_COLUMN], 'LASTNAME');

        $row = ['test@valid.com', 'LASTNAME', 'Firstname'];
        $csvTransformerService->concatenateFirstNameAndLastNameColumns($row);
        $this->assertEquals($row[CSVTransformerService::LAST_NAME_COLUMN], 'Firstname LASTNAME');

        $row = ['test@valid.com', 'Firstname LASTNAME'];
        $csvTransformerService->orderColumns($row);
        $this->assertEquals($row, ['Firstname LASTNAME', 'test@valid.com']);
    }
}