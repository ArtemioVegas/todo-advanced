<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskForm;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Security("has_role('ROLE_USER')")
 * @Route("/app")
 */
class TaskController extends Controller
{
    /**
     * @Route("/all", name="task_all")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $sorterService = $this->get('app.query_filter');
        $tasks = $sorterService->find($request);

        return $this->render('task/all.html.twig',
            ['tasks'=>$tasks]
        );
    }
    /**
     * @Route("/task/add", name="task_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        // Если у пользователя нет проектов то редиректим
        if($this->redirectIfNoCategoriesFound()){
            exit;
        }

        $task = new Task();
        $form = $this->createForm(TaskForm::class,$task,array(
            'user_object' => $this->getUser()
        ));

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            $task->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Задача успешно создана');

            return $this->redirect($this->generateUrl('task_all'));

        }
        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/task/{id}/edit", name="task_edit",requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Task $task)
    {
        $this->denyAccessUnlessGranted('edit', $task, 'Tasks can only be edit to their authors.');

        $form = $this->createForm(TaskForm::class, $task,array(
            'user_object' => $this->getUser()
        ));
        // only handles data on POST
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Задача успешно изменена');

            return $this->redirectToRoute('project_show',array('id' => $task->getProject()->getId() ));

        }
        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/task/{id}/show", name="task_show",requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function showAction(Task $task)
    {
        $this->denyAccessUnlessGranted('show', $task, 'Tasks can only be show to their authors.');

        return $this->render('task/show.html.twig', [
            'task' => $task
        ]);
    }

    /**
     * @Route("/task/{id}/done", name="task_done",requirements={"id": "\d+"})
     * @Method("POST")
     */
    public function doneAction(Request $request, Task $task = null)
    {
        // default response
        $response = array('message' => 'You can access this only using Ajax!');
        $status = 400;

        if ($request->isXmlHttpRequest()) {
            try{
                $error = 0;

                if(empty($task)){
                    throw new NotFoundHttpException;
                }

                $this->denyAccessUnlessGranted('edit', $task, 'Tasks can only be edit to their authors.');

                $task->setIsDone(true);
                $task->setCompleteDate(new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->flush();

                $message = "Задача {$task->getTaskName()} успешно выполнена!";

            }catch(NotFoundHttpException  $e){
                $message = 'Данная задача не найдена';
                $error   =  1;
            }catch(AccessDeniedException $e){
                $message = $e->getMessage();
                $error   =  1;
            }

            //return
            $response = array(
                'error'   => $error,
                'message' => $message,
            );
            $status = 200;
        }

        return new JsonResponse($response, $status);
    }

    /**
     * Deletes a Task entity.
     *
     * @Route("/task/{id}/delete", name="task_delete",requirements={"id": "\d+"})
     * @Method("POST")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Task $task = null)
    {
        // default response
        $response = array('message' => 'You can access this only using Ajax!');
        $status = 400;

        if ($request->isXmlHttpRequest()) {

            try{
                $error   = '';

                if(empty($task)){
                    throw new NotFoundHttpException;
                }

                $this->denyAccessUnlessGranted('delete', $task, 'Tasks can only be deleted to their authors.');

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($task);
                $entityManager->flush();

                $message = "Задача {$task->getTaskName()} успешно удалена!";

            }catch(NotFoundHttpException $e){
                $message = 'Данная задача не найдена';
                $error   =  1;
            }catch(AccessDeniedException $e){
                $message = $e->getMessage();
                $error   =  1;
            }

            //return
            $response = array(
                'error'   => $error,
                'message' => $message,
            );
            $status = 200;
        }

        return new JsonResponse($response, $status);
    }

    /**
     * @Route("/task/{id}/clone", name="task_clone",requirements={"id": "\d+"})
     * @Method("POST")
     */
    public function cloneAction(Request $request, Task $task = null)
    {
        // default response
        $response = array('message' => 'You can access this only using Ajax!');
        $status = 400;

        if ($request->isXmlHttpRequest()){
            try{
                $error   = '';
                $data    = [];

                if(empty($task)){
                    throw new NotFoundHttpException;
                }

                $this->denyAccessUnlessGranted('edit', $task, 'Tasks can only be cloned to their authors.');

                $cloneTask = clone $task;

                $timesTamp =  new \DateTime();
                $timesTamp = $timesTamp->getTimestamp();

                $firstAppend = strpos($cloneTask->getTaskName(),'_clone_');
                $cloneTask->setTaskName(substr($cloneTask->getTaskName(),0,$firstAppend ? $firstAppend : strlen($cloneTask->getTaskName())) .'_clone_'. $timesTamp );
                $em = $this->getDoctrine()->getManager();
                $em->persist($cloneTask);
                $em->flush();

                $message = "Задача {$task->getTaskName()} успешно дублирована!";

                // Получаем инфу о проекте
                $projectInfo = $this->getDoctrine()->getRepository(Task::class)->findProjectName($task->getId());

                $data['id'] =  $cloneTask->getId();
                $data['task_name'] =  $cloneTask->getTaskName();
                $data['project_name'] =  $projectInfo['projectName'];

                /// УРЛЫ
                $data['urls']['edit_url'] = $this->generateUrl('task_edit', array('id' => $cloneTask->getId()));
                $data['urls']['clone_url'] = $this->generateUrl('task_clone', array('id' => $cloneTask->getId()));
                $data['urls']['done_url'] = $this->generateUrl('task_done', array('id' => $cloneTask->getId()));
                $data['urls']['delete_url'] = $this->generateUrl('task_delete', array('id' => $cloneTask->getId()));
                $data['urls']['project_url'] = $this->generateUrl('project_show', array('id' => $projectInfo['id']));

                $data['due_date'] =  $cloneTask->getDueDate()->format('d-m-Y');

            }catch(NotFoundHttpException $e){
                $message = 'Данная задача не найдена';
                $error   =  1;
            }catch(AccessDeniedException $e){
                $message = $e->getMessage();
                $error   =  1;
            }

            $response = array(
                'error'   => $error,
                'message' => $message,
                'info'    => $data,
            );
            $status = 200;
        }

        return new JsonResponse($response, $status);
    }

    /**
     * @Route("/search", name="task_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        $query = $request->query->get('q', '');
        // Sanitizing the query: removes all non-alphanumeric characters except whitespaces
        $query = preg_replace('/[^[:alnum:] ]/', '', trim(preg_replace('/[[:space:]]+/', ' ', $query)));
        // Splits the query into terms and removes all terms which
        // length is less than 2
        $terms = array_unique(explode(' ', mb_strtolower($query)));
        $terms = array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
        $tasks = [];

        if (!empty($terms)) {
            $tasks = $this->getDoctrine()->getRepository(Task::class)->findByTerms($terms,$this->getUser());
        }

        return $this->render('task/search.html.twig',['tasks'=>$tasks]);
    }

    private function redirectIfNoCategoriesFound()
    {
        if(!$this->getDoctrine()->getManager()->getRepository('AppBundle:Project')
            ->findAllProjectsByUser($this->getUser())){

            $this->addFlash('warning', 'Необходимо создать хотя бы 1 проект!');

            return $this->redirectToRoute('project_add')->sendHeaders();
        }
        return false;
    }
}