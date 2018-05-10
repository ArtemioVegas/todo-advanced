<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Project;
use AppBundle\Form\ProjectForm;

/**
 * @Security("has_role('ROLE_USER')")
 * @Route("/app")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/project/add", name="project_add")
     * @Method({"GET", "POST"})
     */
    public function addProjectAction(Request $request)
    {
        $form = $this->createForm(ProjectForm::class);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {

            $project = $form->getData();
            $project->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Проект успешно создан');

            return $this->redirect($this->generateUrl('task_all'));

        }
        return $this->render('project/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/{id}", name="project_show",requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function showAction(Project $project)
    {
        $this->denyAccessUnlessGranted('show', $project, 'Projects can only be show to their authors.');

        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('AppBundle:Task')
            ->findBy(
                array('user' => $this->getUser(),'project' => $project, 'isDone' => false),
                array('id' => 'ASC')
            );

        return $this->render('project/show.html.twig', array(
            'project' => $project,
            'tasks'   => $tasks
        ));
    }

    /**
     * @Route("/project/{id}/edit", name="project_edit",requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Project $project)
    {
        $this->denyAccessUnlessGranted('edit', $project, 'Projects can only be edit to their authors.');

        $form = $this->createForm(ProjectForm::class, $project);
        // only handles data on POST
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $project = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Проект успешно изменен');

            return $this->redirectToRoute('task_all');
        }
        return $this->render('project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/project/{id}/delete", name="project_delete",requirements={"id": "\d+"})
     * @Method("POST")
     *
     * The Security annotation value is an expression (if it evaluates to false,
     * the authorization mechanism will prevent the user accessing this resource).
     */
    public function deleteAction(Request $request, Project $project)
    {
        $this->denyAccessUnlessGranted('delete', $project, 'Tasks can only be deleted to their authors.');

        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('task_all');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($project);
        $entityManager->flush();

        $this->addFlash('success', 'Проект успешно удален');

        return $this->redirectToRoute('task_all');
    }

    /**
     * @Route("/project/get-projects", name="project_count_tasks")
     * @Method("POST")
     */
    public function getProjectsAndtasks(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $result = $em->getRepository('AppBundle:Project')->getProjectsAndTasks($this->getUser());

            $response = new JsonResponse(
                array(
                    'info'    =>  $result
                ));
            return $response;
        }
    }
}