<?php

namespace App\Repository;

use App\Entity\Tasks;
use App\Entity\Checklists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tasks>
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

	/* récupérer les tâches qui ne sont pas associés à cette checlist */
	public function findTasksNotInChecklist(Checklists $checklist)
    {
        return $this->createQueryBuilder('t')
            ->where('t NOT IN (
                SELECT tasks FROM App\Entity\Checklists c
                JOIN c.tasks tasks
                WHERE c = :checklist
            )')
		    ->andWhere('t.archived = false')
            ->setParameter('checklist', $checklist)
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Tasks[] Returns an array of Tasks objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tasks
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
