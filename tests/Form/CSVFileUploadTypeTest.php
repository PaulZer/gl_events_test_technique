<?php

namespace App\Tests\Form;

use App\Form\Type\CSVFileUploadType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CSVFileUploadTypeTest extends TypeTestCase
{
    public function testCSVFileUploadForm()
    {
        $form = $this->factory->create(CSVFileUploadType::class);

        # Tests thar form contains required fields
        $this->assertArrayHasKey('csv_file', $form);
        $this->assertArrayHasKey('save', $form);
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(CSVFileUploadType::class);

        $filePath = __DIR__.'/../../fixtures/files/test_upload.csv';
        $file = new UploadedFile($filePath, 'test_upload.csv', 'text/csv', null, true);

        # Submit data to the form
        $form->submit([
            'csv_file_upload' => $file,
            'save' => 'Save',
        ]);

        # Test that UploadCSVFileSubscriber does not produce error
        $this->assertTrue($form->isSynchronized());
    }

    public function testSubmitInvalidData()
    {
        $form = $this->factory->create(CSVFileUploadType::class);

        $filePath = __DIR__.'/../../fixtures/files/test_upload_invalid.csv';
        $file = new UploadedFile($filePath, 'test_upload_invalid.csv', 'text/csv', null, true);

        # Submit data to the form
        $form->submit([
            'csv_file' => $file,
            'save' => 'Save',
        ]);

        # Test that UploadCSVFileSubscriber does produce error
        $this->assertCount(1, $form->getErrors());
    }
}