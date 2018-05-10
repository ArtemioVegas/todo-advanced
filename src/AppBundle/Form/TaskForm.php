<?php

namespace AppBundle\Form;

use AppBundle\Repository\ProjectRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\Task;
use AppBundle\Entity\Project;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskForm extends AbstractType
{
    protected $currentField;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user =  $options['user_object'];
        $builder
        ->add('taskName', null,[
            'attr' =>['placeholder' => 'Введите название задачи'],
            'label' => 'Название задачи'
        ])
        ->add('description', null,[
            'label' => 'Описание'
        ])
        ->add('project', EntityType::class, [
            'label' => 'Проект',
            'placeholder' => 'Выберите проект',
            'class' => Project::class,
            'query_builder' => function(ProjectRepository $repo) use ($user) {
                return $repo->createAlphabeticalQueryBuilder($user);
            }
        ])
        ->add('uploadFile',FileType::class,[
            'label' => 'Загружаемый файл',
            'required' => false,
            'mapped'   => true,
        ])
        ->add('dueDate',DateType::class,
            ['label' => 'Выполнить до:',
            'widget' => 'choice',
            'years' => range(date('Y'), date('Y')+10),
            'months' => range(1, 12),
            'days' => range(1, 31),
            'invalid_message' => 'Это значение не является допустимой датой',
            ]
            )
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $this->currentField = $event->getData()->getUploadFile();
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            // Retrieve submitted data
            $form = $event->getForm();

            $inputFile = $form->getData();
            $newHeader = $inputFile->getUploadFile();

            // Verify if the form field is empty and if the field is set on database
            // Set the value of database again to avoid deleting it
            if (is_null($newHeader) && !is_null($this->currentField)) {
                $inputFile->setUploadFile($this->currentField);
            }
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class
        ]);

        $resolver->setRequired('user_object');

    }
}