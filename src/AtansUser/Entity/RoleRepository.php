<?php
namespace AtansUser\Entity;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    /**
     * Check name exist
     *
     * @param  string $value
     * @param  int $id
     * @return bool
     */
    public function noNameExists($value, $id)
    {
        $qb  = $this->getEntityManager()->createQueryBuilder();
        $row = $qb->select('COUNT(r.id) as c')
                  ->from($this->getClassName(), 'r')
                  ->where($qb->expr()->eq('r.name', ':name'))
                  ->andWhere($qb->expr()->neq('r.id', ':id'))
                  ->setParameter('name', $value)
                  ->setParameter('id', $id)
                  ->getQuery()
                  ->getSingleResult();

        $result = (int) $row['c'] ? false : true;
        return $result;
    }
}
