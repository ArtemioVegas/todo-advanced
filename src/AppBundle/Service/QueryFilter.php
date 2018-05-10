<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class QueryFilter
{
    private $entityManager;

    private $user;

    public function __construct(TokenStorageInterface $token, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->user = $token->getToken()->getUser();
    }

    public function find(Request $request)
    {
        $today = new \DateTime();
        $tomorrow = clone $today;

        $today->format('Y-m-d');
        $tomorrow = $tomorrow->modify('+1 day')->format('Y-m-d');

        $done = false;
        $exp = '';

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('t')->from('AppBundle\Entity\Task', 't');
        $queryBuilder
            ->where('t.user = :u')
            ->setParameter('u', $this->user)
        ;

        $showCompleted = $request->query->get('show_completed','');
        $showCompleted = trim($showCompleted);

        $dateFilter = $request->query->get('date_filter','');
        $dateFilter = trim($dateFilter);


        if($showCompleted){
            switch ($showCompleted){
                case '1':
                    $exp = ' OR t.isDone = 1' ;
                    break;
            }
        }

        $queryBuilder
            ->andWhere('t.isDone = :done'. $exp)
        ;

        $queryBuilder->setParameter('done', $done);

        if($dateFilter){
            switch ($dateFilter){
                        case 'today':
                            $data = $today;
                            $expression = '=' . ':data' ;
                            break;
                        case 'expired':
                            $data = $today;
                            $expression = '<' . ':data';
                            break;
                        case 'tomorrow':
                            $data = $tomorrow;
                            $expression = '='. ':data' ;
                            break;
                        default:
                            $expression = null;

            }

            if($expression){
                $queryBuilder
                    ->andWhere('t.dueDate'.$expression)
                    ->setParameter('data',$data) ;
            }

        }


        // Дальше уже логика построения запроса с помощью API QueryBuilder
        // см. http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html

        return $queryBuilder->getQuery()->getResult();
    }
}