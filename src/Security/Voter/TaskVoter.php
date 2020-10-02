<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskVoter
 * @package App\Security\Voter
 */
class TaskVoter extends Voter
{
    const DELETE = 'delete';

    /**
     * @param string $attribute
     * @param Task   $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::DELETE]) && $subject instanceof Task;
    }

    /**
     * @param string         $attribute
     * @param Task           $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface || !$subject instanceof Task) {
            return false;
        }
        $task = $subject;

        if (is_null($task->getUser())) {
            return $user->hasRoles('ROLE_ADMIN');
        }

        return /*$user->hasRoles('ROLE_ADMIN') || */$user === $task->getUser();
    }
}
