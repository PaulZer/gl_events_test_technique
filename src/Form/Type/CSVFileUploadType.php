<?php

namespace App\Form\Type;

use App\Form\EventSubscriber\UploadCSVFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class CSVFileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('csv_file', FileType::class, [
                'label' => 'CSV File',
                'required' => true,
                'attr' => [
                    'accept' => '.csv',
                ],
            ])
            ->add('save', SubmitType::class)
            ->addEventSubscriber(new UploadCSVFileSubscriber())
        ;
    }
}