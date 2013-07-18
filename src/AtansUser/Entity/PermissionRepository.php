<?php
namespace AtansUser\Entity;

use Doctrine\ORM\EntityRepository;

class PermissionRepository extends EntityRepository
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
        $row = $qb->select('COUNT(p.id) as c')
            ->from($this->getClassName(), 'p')
            ->where($qb->expr()->eq('p.name', ':name'))
            ->andWhere($qb->expr()->neq('p.id', ':id'))
            ->setParameter('name', $value)
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();

        $result = (int) $row['c'] ? false : true;
        return $result;
    }
}
