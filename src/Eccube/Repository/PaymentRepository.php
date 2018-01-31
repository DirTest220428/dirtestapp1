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

use Doctrine\ORM\Query;
use Eccube\Entity\Payment;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 */
class PaymentRepository extends AbstractRepository
{
    /**
     * PaymentRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @deprecated 呼び出し元で制御する
     * @param $id
     * @return \Eccube\Entity\Payment|null|object
     */
    public function findOrCreate($id)
    {
        if ($id == 0) {

            $Payment = $this->findOneBy([], ['sort_no' => 'DESC']);

            $sortNo = 1;
            if ($Payment) {
                $sortNo = $Payment->getSortNo() + 1;
            }

            $Payment = new \Eccube\Entity\Payment();
            $Payment
                ->setSortNo($sortNo)
                ->setFixed(true)
                ->setVisible(true);
        } else {
            $Payment = $this->find($id);
        }

        return $Payment;
    }

    /**
     * @return array
     */
    public function findAllArray()
    {

        $query = $this
            ->getEntityManager()
            ->createQuery('SELECT p FROM Eccube\Entity\Payment p INDEX BY p.id');
        $result = $query
            ->getResult(Query::HYDRATE_ARRAY);

        return $result;
    }

    /**
     * 支払方法を取得
     * 条件によってはDoctrineのキャッシュが返されるため、arrayで結果を返すパターンも用意
     *
     * @param $delivery
     * @param $returnType true : Object、false: arrayが戻り値
     * @return array
     */
    public function findPayments($delivery, $returnType = false)
    {

        $query = $this->createQueryBuilder('p')
            ->innerJoin('Eccube\Entity\PaymentOption', 'po', 'WITH', 'po.payment_id = p.id')
            ->where('po.Delivery = (:delivery)')
            ->orderBy('p.sort_no', 'DESC')
            ->setParameter('delivery', $delivery)
            ->getQuery();

        $query->expireResultCache(false);

        if ($returnType) {
            $payments = $query->getResult();
        } else {
            $payments = $query->getArrayResult();
        }

        return $payments;
    }

    /**
     * 共通の支払方法を取得
     *
     * @param $deliveries
     * @return array
     */
    public function findAllowedPayments($deliveries, $retuenType = false)
    {
        $payments = [];
        $i = 0;

        foreach ($deliveries as $Delivery) {
            $p = $this->findPayments($Delivery, $retuenType);

            if ($i != 0) {

                $arr = [];
                foreach ($p as $payment) {
                    foreach ($payments as $pay) {
                        if ($payment['id'] == $pay['id']) {
                            $arr[] = $payment;
                            break;
                        }
                    }
                }

                $payments = $arr;
            } else {
                $payments = $p;
            }
            $i++;
        }

        return $payments;
    }
}
