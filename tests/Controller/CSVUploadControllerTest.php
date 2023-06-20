<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class CSVUploadControllerTest extends WebTestCase
{
    public function testCSVUpload()
    {
        # set HTTP Basic credentials for admin user
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'john_admin',
            'PHP_AUTH_PW'   => 'test',
        ]);

        # Test form page is reachable
        $client->request('GET', '/upload-csv');
        $this->assertResponseIsSuccessful();

        # Mock upload folder existence (fails weirdly for now)
        # $filesystemMock = $this->createMock(Filesystem::class);
        # $filesystemMock->expects(self::once())
        #      ->method('exists')
        #      ->willReturn(true)
        #;

        # Mock file moved to directory (fails weirdly for now)
        # $uploadedFileMock = $this->createMock(UploadedFile::class);
        # $uploadedFileMock->expects(self::once())
        #     ->method('move')
        #     ->willReturn(true)
        # ;

        # Test file upload
        $filePath = __DIR__.'/../../fixtures/files/test_upload.csv';
        $file = new UploadedFile($filePath, 'test_upload.csv');

        $client->submitForm('Save', [
            'csv_file_upload[csv_file]' => $file
        ]);

        # Test that async message is sent to transport
        /* @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async_priority_normal');
        # TODO make this test work :/ can't find a way for now
        # $this->assertCount(1, $transport->get());

        $this->assertResponseIsSuccessful();
    }
}