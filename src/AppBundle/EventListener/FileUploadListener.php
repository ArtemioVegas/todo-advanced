<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Task;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;

class FileUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        // upload only works for Task entities
        if (!$entity instanceof Task) {
            return;
        }

        // получаем файл из модели
        $file = $entity->getUploadFile();

        // если загружается новый файл
        if ($file instanceof UploadedFile) {

            $fileName = $this->uploader->upload($file);
            $originalFileName = $this->uploader->getOriginalFileName($file);

            $entity->setUploadFile($fileName);
            $entity->setUploadOriginalName($originalFileName);
       // если файл уже был
        }elseif ($file instanceof File){
            $entityFile = new \SplFileInfo($file);
            $entity->setUploadFile($entityFile->getFilename());
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Task) {
            return;
        }

        if ($fileName = $entity->getUploadFile()) {
            $entity->setUploadFile(new File($this->uploader->getTargetDir().'/'.$fileName));
        }

    }
}