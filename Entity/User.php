<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\UserFilesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\Request;
use c975L\UserFilesBundle\Validator\Constraints as UserFilesBundleAssert;
use c975L\UserFilesBundle\Validator\Constraints\Challenge;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_8D93D64992FC23A8", columns={"username_canonical"}), @ORM\UniqueConstraint(name="UNIQ_8D93D649A0D96FBF", columns={"email_canonical"})})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="c975L\UserFilesBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="gender", type="string")
     * @Assert\Choice(
     *      choices = {"woman", "man"},
     *      message = "gender.choose_valid"
     * )
     */
    protected $gender;

    /**
     * @ORM\Column(name="firstname", type="string")
     * @Assert\Length(
     *      min = 2,
     *      max = 48,
     *      minMessage = "firstname.min_length",
     *      maxMessage = "firstname.max_length"
     * )
     */
    protected $firstname;

    /**
     * @ORM\Column(name="lastname", type="string")
     * @Assert\Length(
     *      min = 2,
     *      max = 48,
     *      minMessage = "lastname.min_length",
     *      maxMessage = "lastname.max_length"
     * )
     */
    protected $lastname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation", type="datetime", nullable=true)
     */
    protected $creation;

    /**
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "email.not_valid",
     *     checkMX = true
     * )
     */
    protected $email;

    /**
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Za-z])(?=.*[@#$%^*])(?=.*[0-9]).{8,48}/",
     *      message="password.requirement"
     * )
     */
    protected $plainPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_logout", type="datetime", nullable=true)
     */
    protected $lastLogout;

    /**
     * @UserFilesBundleAssert\Challenge(
     *      message = "label.error_challenge"
     * )
     */
    protected $challenge;


    public function setDataFromArray($data)
    {
        foreach ($data as $key => $value) {
            $function = 'set' . ucfirst($key);
            $this->$function($value);
        }
    }


    /**
     * Set challenge
     *
     * @param string $challenge
     *
     * @return User
     */
    public function setChallenge($challenge)
    {
        $this->challenge = $challenge;

        return $this;
    }

    /**
     * Get challenge
     *
     * @return string
     */
    public function getChallenge()
    {
        return $this->challenge;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set creation
     *
     * @param DateTime $creation
     *
     * @return User
     */
    public function setCreation($creation)
    {
        $this->creation = $creation;

        return $this;
    }

    /**
     * Get creation
     *
     * @return DateTime
     */
    public function getCreation()
    {
        return $this->creation;
    }

    /**
     * Set lastLogout
     *
     * @param DateTime $lastLogout
     *
     * @return User
     */
    public function setLastLogout($lastLogout)
    {
        $this->lastLogout = $lastLogout;

        return $this;
    }

    /**
     * Get lastLogout
     *
     * @return DateTime
     */
    public function getLastLogout()
    {
        return $this->lastLogout;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}
