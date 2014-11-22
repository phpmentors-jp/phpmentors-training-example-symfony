<?php
/*
 * Copyright (c) 2012 KUBO Atsuhiro <kubo@iteman.jp>,
 *               2014 YAMANE Nana <shigematsu.nana@gmail.com>,
 * All rights reserved.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace Example\UserRegistrationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Repository\RepositoryInterface;

class UserRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * @param \PHPMentors\DomainKata\Entity\EntityInterface $entity
     */
    public function add(EntityInterface $entity)
    {
    }

    /**
     * @param \PHPMentors\DomainKata\Entity\EntityInterface $entity
     */
    public function remove(EntityInterface $entity)
    {
    }
}
