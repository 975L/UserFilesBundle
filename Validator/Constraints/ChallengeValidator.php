<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ChallengeValidator extends ConstraintValidator
{
    //Validates the challenge
    public function validate($value, Constraint $constraint)
    {
        $postChallengeResult = (string) strtoupper($value);

        $session = new Session();
        $sessionChallengeResult = (string) $session->get('challengeResult');

        if ($sessionChallengeResult !== null && $sessionChallengeResult !== $postChallengeResult) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
