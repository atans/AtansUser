<?php
namespace AtansUser\Entity;

use AtansUser\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class RoleRepository extends EntityRepository
{
    /**
     * Find all role without id
     *
     * @param  null|int $id
     * @return array
     */
    public function findAllRoleWithoutId($id = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from($this->getEntityName(), 'r');

        if (!is_null($id) && is_int($id)) {
            $qb->where($qb->expr()->neq('r.id', ':id'))
               ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }

    public function pagination(array $data)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('r')
            ->from($this->getEntityName(), 'r');

        if (isset($data['query']) && strlen($queryString = trim($data['query']))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('r.name', ':query')
            ));
            $qb->setParameter('query', "%$queryString%");
        }

        $order = 'DESC';
        if (isset($data['order']) && in_array(strtoupper($data['order']), array('ASC', 'DESC'))) {
            $order = $data['order'];
        }
        $qb->addOrderBy('r.id', $order);

        if (!isset($data['page']) || !isset($data['count'])) {
            throw new Exception\InvalidArgumentException("'page' and 'count' are must be defined");
        }

        $paginator = new Paginator(new DoctrinePaginator(
            new ORMPaginator($qb)
        ));

        $paginator->setCurrentPageNumber($data['page'])
                  ->setItemCountPerPage($data['count']);

        return $paginator;
    }
}
