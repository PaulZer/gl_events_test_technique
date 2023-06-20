<?php

namespace App\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UploadCSVFileSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::SUBMIT => 'submitData'];
    }

    /**
     * @param FormEvent $event
     */
    public function submitData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $uploadedFile = $data['csv_file'] ?? null;

        if(null === $uploadedFile || !in_array($uploadedFile->getMimeType(),  ['text/csv', 'text/plain'])) {
            $form->addError(new FormError("Please upload a valid CSV file."));
            return;
        }

        $tmpPath = $uploadedFile->getPathName();

        # Code below check that CSV file has 3 columns for each row and returns FormError if so
        $rowNo = 1;
        # $fp is file pointer to tmp csv file
        if (false !== ($fp = fopen($tmpPath, "r"))) {
            while (false !== ($row = fgetcsv($fp, null, ","))) {
                $num = count($row);
                if($num !== 3){
                    $form->addError(new FormError("Each row must have 3 columns (email, first name, last name) on the submitted CSV file. Row $rowNo has $num columns."));
                    return;
                }
                $rowNo++;
            }
            fclose($fp);
        }
    }
}
