<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;

class CSVTransformerService
{
    const EMAIL_COLUMN = 0;
    const LAST_NAME_COLUMN = 1;
    const FIRST_NAME_COLUMN = 2;

    private string $csvUploadDirectory;
    private string $transformedCSVDirectory;

    public function __construct(ParameterBagInterface $params, MailerInterface $mailer)
    {
        $this->csvUploadDirectory = $params->get('csv_upload_directory');
        $this->transformedCSVDirectory = $params->get('transformed_csv_directory');
    }

    public function transformCSVFile(string $csvFilePath): void
    {
        # csv file open
        if (false !== ($open = fopen($this->csvUploadDirectory.$csvFilePath, "r"))) {

            # csv file get data and convert to array
            while (false !== ($row = fgetcsv($open, 1000, ","))) {

                # apply transformations row by row
                $this->deleteInvalidEmails($row);
                $this->nameToUppercase($row);
                $this->concatenateFirstNameAndLastNameColumns($row);
                $this->orderColumns($row);

                $data[] = $row;
            }

            # file close
            fclose($open);
        }

        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->transformedCSVDirectory)){
            $filesystem->mkdir($this->transformedCSVDirectory);
        }

        # csv File update
        $transFormedFilePath = fopen($this->transformedCSVDirectory.$csvFilePath, 'wb');

        foreach ($data as $row)
        {
            # write transformed row in new csv file
            fputcsv($transFormedFilePath, $row);
        }

        # File close
        fclose($transFormedFilePath);
    }

    public function deleteInvalidEmails(array &$row): void
    {
        #removing eventual white spaces
        $row[self::EMAIL_COLUMN] = trim($row[self::EMAIL_COLUMN]);

        # validating email format
        if (!filter_var($row[self::EMAIL_COLUMN], FILTER_VALIDATE_EMAIL)) {
            # Remove the invalid email
            $row[self::EMAIL_COLUMN] = '';
        }
    }

    public function nameToUppercase(array &$row): void
    {
        $row[self::LAST_NAME_COLUMN] = strtoupper($row[self::LAST_NAME_COLUMN]);
    }

    public function concatenateFirstNameAndLastNameColumns(array &$row): void
    {
        $row[self::LAST_NAME_COLUMN] = $row[self::FIRST_NAME_COLUMN].' '.$row[self::LAST_NAME_COLUMN];
        unset($row[self::FIRST_NAME_COLUMN]);
    }

    public function orderColumns(array &$row): void
    {
        $row = array_reverse($row);
    }
}