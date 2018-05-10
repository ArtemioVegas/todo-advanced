<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Task
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *      max = 50,
     * )
     */
    private $taskName;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *      max = 201,
     * )
     */
    private $description;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $uploadOriginalName;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(
     *     maxSize = "2M",
     *     mimeTypes = {"image/gif", "image/jpeg","image/png"}
     *     )
     */
    private $uploadFile;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today",message="Срок исполнения должен быть больше сегодняшней даты")
     */
    private $dueDate;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completeDate;
    /**
     * @var Boolean
     * @ORM\Column(type="boolean",options={"default":0})
     */
    private $isDone = false;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project",inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Не выбран проект для задачи!!!")
     */
    private $project;

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCompleteDate()
    {
        return $this->completeDate;
    }

    /**
     * @param \DateTime $completeDate
     */
    public function setCompleteDate(\DateTime $completeDate)
    {
        $this->completeDate = $completeDate;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return boolean
     */
    public function isIsDone()
    {
        return $this->isDone;
    }

    /**
     * @param boolean $isDone
     */
    public function setIsDone($isDone)
    {
        $this->isDone = $isDone;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getTaskName()
    {
        return $this->taskName;
    }

    /**
     * @param mixed $taskName
     */
    public function setTaskName($taskName)
    {
        $this->taskName = $taskName;
    }

    /**
     * @return mixed
     */
    public function getUploadFile()
    {
        return $this->uploadFile;
    }

    /**
     * @param mixed $uploadFile
     */
    public function setUploadFile($uploadFile)
    {
        $this->uploadFile = $uploadFile;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUploadOriginalName()
    {
        return $this->uploadOriginalName;
    }

    /**
     * @param mixed $uploadOriginalName
     */
    public function setUploadOriginalName($uploadOriginalName)
    {
        $this->uploadOriginalName = $uploadOriginalName;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
        }
    }
}