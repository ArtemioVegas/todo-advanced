<?php

namespace AppBundle\Security;
use AppBundle\Entity\Task;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    private const SHOW = 'show';
    private const EDIT = 'edit';
    private const DELETE = 'delete';
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // this voter is only executed for three specific permissions on Post objects
        return ($subject instanceof Task || $subject instanceof Project) && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }
    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $task, TokenInterface $token)
    {
        $user = $token->getUser();
        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }
        // the logic of this voter is pretty simple: if the logged user is the
        // author of the given blog post, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $post is a Post object)
        return $user === $task->getUser();
    }
}