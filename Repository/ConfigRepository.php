<?php

namespace Plugin\Summary\Repository;

use Eccube\Repository\AbstractRepository;
//use Plugin\Summary\Entity\Config;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Plugin\Summary\Entity\SummaryConfig;
/**
 * ConfigRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConfigRepository extends AbstractRepository
{
    /**
     * ConfigRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SummaryConfig::class);
    }

    public function newSummaryConfig()
    {
        $SummaryConfig = new \Plugin\Summary\Entity\SummaryConfig();

        return $SummaryConfig;
    }
    /**
     * @param Custom $custom
     *
     * @return SummaryConfig|Config
     */
    public function get($custom)
    {
        #return $this->find($id);
        //return $this->find(1);
        return $this->findOneBy(['custom_id' => $custom]);
        #return $this->findOneBy(['Summary' => $custom]);
        //return 0;
    }
}
