<?php

namespace App\MessageHandler;

use App\Message\CSVUploadMessage;
use App\Service\CSVTransformerService;
use App\Service\Mailer\SendTransformedCSVMailService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CSVTransformationHandler
{
    private CSVTransformerService $csvTransformerService;
    private SendTransformedCSVMailService $sendTransformedCSVMailService;

    public function __construct(CSVTransformerService $csvTransformerService, SendTransformedCSVMailService $sendTransformedCSVMailService)
    {
        $this->csvTransformerService = $csvTransformerService;
        $this->sendTransformedCSVMailService = $sendTransformedCSVMailService;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(CSVUploadMessage $message): void
    {
        # this function is responsible for applying wanted transformations to the CSV file
        # and then send it by mail to the address configured in config/services.yaml

        $csvFileName = $message->getCSVFileName();

        $this->csvTransformerService->transformCSVFile($csvFileName);

        $this->sendTransformedCSVMailService->sendCSVByEmail($csvFileName);
    }


}