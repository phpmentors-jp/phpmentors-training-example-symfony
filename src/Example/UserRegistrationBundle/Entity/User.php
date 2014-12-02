<?php
/*
 * Copyright (c) 2012, 2014 KUBO Atsuhiro <kubo@iteman.jp>,
 *               2014 YAMANE Nana <shigematsu.nana@gmail.com>,
 * All rights reserved.
 *
 * This file is part of PHPMentors_Training_Example_Symfony.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use PHPMentors\DomainKata\Entity\EntityInterface;

/**
 * @ORM\Entity(repositoryClass="Example\UserRegistrationBundle\Repository\UserRepository")
 * @ORM\Table(name="user",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="user_email_idx", columns={"email"}),
 *          @ORM\UniqueConstraint(name="user_activation_key_idx", columns={"activation_key"})
 *      })
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="email", message="このメールアドレスはすでに使用されています", groups={"registration"})
 */
class User implements EntityInterface
{
    const SALT = 'cbab20bf0631558e0b723e5f48c337237a4d862d';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="入力してください", groups={"registration"})
     * @Assert\Length(min=2, max=255, minMessage="{{ limit }} 文字以上で入力してください", maxMessage="{{ limit }} 文字以下で入力してください", groups={"registration"})
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="入力してください", groups={"registration"})
     * @Assert\Length(min=2, max=255, minMessage="{{ limit }} 文字以上で入力してください", maxMessage="{{ limit }} 文字以下で入力してください", groups={"registration"})
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="入力してください", groups={"registration"})
     * @Assert\Length(min=2, max=255, minMessage="{{ limit }} 文字以上で入力してください", maxMessage="{{ limit }} 文字以下で入力してください", groups={"registration"})
     * @Assert\Email(message="正しいメールアドレスを入力してください", groups={"registration"})
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $password
     *
     * @Assert\NotBlank(message="入力してください", groups={"registration"})
     * @Assert\Length(min=2, max=255, minMessage="{{ limit }} 文字以上で入力してください", maxMessage="{{ limit }} 文字以下で入力してください", groups={"registration"})
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_date", type="datetime")
     */
    private $registrationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="activation_date", type="datetime", nullable=true)
     */
    private $activationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="activation_key", type="string", length=255)
     */
    private $activationKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param \DateTime $registrationDate
     */
    public function setRegistrationDate(\DateTime $registrationDate)
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @param \DateTime $activationDate
     */
    public function setActivationDate(\DateTime $activationDate)
    {
        $this->activationDate = $activationDate;
    }

    /**
     * @return \DateTime
     */
    public function getActivationDate()
    {
        return $this->activationDate;
    }

    /**
     * @param string $activationKey
     */
    public function setActivationKey($activationKey)
    {
        $this->activationKey = $activationKey;
    }

    /**
     * @return string
     */
    public function getActivationKey()
    {
        return $this->activationKey;
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
    public function getCreatedAt()
    {
        return $this->createdAt;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
