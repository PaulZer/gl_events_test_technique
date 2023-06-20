<?php

namespace App\Controller;

use App\Form\Type\CSVFileUploadType;
use App\Message\CSVUploadMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class CSVUploadController extends AbstractController
{

    public function index()
    {
        $form = $this->createForm(CSVFileUploadType::class);

        return $this->render('csv_upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function uploadCSV(Request $request, MessageBusInterface $bus)
    {
        $form = $this->createForm(CSVFileUploadType::class);
        $uploadDirectory = $this->getParameter('csv_upload_directory');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $csvFile = $form['csv_file']->getData();
            $newFilename = "csv_upload_".time().'.'.$csvFile->guessExtension();

            try {
                $filesystem = new Filesystem();
                if(!$filesystem->exists($uploadDirectory)){
                    $filesystem->mkdir($uploadDirectory);
                }

                $csvFile->move($uploadDirectory, $newFilename);

            } catch (IOExceptionInterface $exception) {
                echo "An error occurred while creating your directory at ".$exception->getPath();
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            # We dispatch a message to the message bus to inform that a new CSV file has been uploaded
            $bus->dispatch(new CSVUploadMessage([
                'file_name' => $newFilename
            ]));

            return $this->render('csv_upload/success.html.twig', [
               'successMessage' => 'CSV file uploaded successfully. It will be processed in the background.',
            ]);
        }

        # If form is not valid, we return the form view which will display the errors
        return $this->render('csv_upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}