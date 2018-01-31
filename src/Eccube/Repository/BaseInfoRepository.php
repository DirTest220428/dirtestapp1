<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Repository;

use Eccube\Entity\BaseInfo;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * BaseInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BaseInfoRepository extends AbstractRepository
{
    /**
     * @var  KernelInterface
     */
    protected $kernel;

    /**
     * BaseInfoRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param KernelInterface $kernel
     */
    public function __construct(RegistryInterface $registry, KernelInterface $kernel)
    {
        parent::__construct($registry, BaseInfo::class);
        $this->kernel = $kernel;
    }

    /**
     * @param int $id
     * @return mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get($id = 1)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id);

        if (!$this->kernel->isDebug()) {
            $qb->setCacheable(true);
        }

        return $qb->getQuery()
            ->useResultCache(true, $this->getCacheLifetime())
            ->getSingleResult();
    }
}
