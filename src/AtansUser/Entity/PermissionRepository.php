<?php
namespace AtansUser\Entity;

use AtansUser\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class PermissionRepository extends EntityRepository
{
    public function pagination(array $data)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from($this->getEntityName(), 'p');

        if (isset($data['query']) && strlen($queryString = trim($data['query']))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('p.name', ':query'),
                $qb->expr()->like('p.description', ':query')
            ));
            $qb->setParameter('query', "%$queryString%");
        }

        $order = 'DESC';
        if (isset($data['order']) && in_array(strtoupper($data['order']), array('ASC', 'DESC'))) {
            $order = $data['order'];
        }
        $qb->addOrderBy('p.id', $order);

        if (!isset($data['page']) || !isset($data['size'])) {
            throw new Exception\InvalidArgumentException("'page' and 'size' are must be defined");
        }

        $paginator = new Paginator(new DoctrinePaginator(
            new ORMPaginator($qb)
        ));

        $paginator->setCurrentPageNumber($data['page'])
            ->setItemCountPerPage($data['size']);

        return $paginator;
    }
}
