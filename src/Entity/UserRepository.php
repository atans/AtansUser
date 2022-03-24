<?php
namespace AtansUser\Entity;

use AtansUser\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class UserRepository extends EntityRepository
{
    /**
     * Pagination
     *
     * @param  array $data
     * @return Paginator
     * @throws \AtansUser\Exception\InvalidArgumentException
     */
    public function pagination(array $data)
    {
        if (!isset($data['page']) || !isset($data['count'])) {
            throw new Exception\InvalidArgumentException("'page' and 'count' are must be defined");
        }

        $qb = $this->createQueryBuilder('u');
        $qb->select('u');

        if (isset($data['status']) && strlen($data['status']) > 0) {
            $qb->andWhere($qb->expr()->eq('u.status', ':status'))
                ->setParameter('status', $data['status']);
        }

        if (isset($data['query']) && strlen($queryString = trim($data['query']))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.username', ':query'),
                $qb->expr()->like('u.email', ':query')
            ));
            $qb->setParameter('query', "%$queryString%");
        }

        $order = 'DESC';
        if (isset($data['order']) && in_array(strtoupper($data['order']), array('ASC', 'DESC'))) {
            $order = $data['order'];
        }
        $qb->addOrderBy('u.id', $order);

        $paginator = new Paginator(new DoctrinePaginator(
            new ORMPaginator($qb)
        ));

        $paginator->setCurrentPageNumber($data['page'])
            ->setItemCountPerPage($data['count']);

        return $paginator;
    }
}
