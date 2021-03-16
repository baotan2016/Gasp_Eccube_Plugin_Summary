<?php

namespace Plugin\Summary\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
/**
 * Config
 *
 * @ORM\Table(name="plg_summary_config")
 * @ORM\Entity(repositoryClass="Plugin\Summary\Repository\ConfigRepository")
 */
class SummaryConfig extends AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="SummaryColumn", type="string", length=255)
     */
    private $SummaryColumn;

    /**
     * Set product_review config id.
     *
     * @param string $id
     *
     * @return SummaryConfig
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set SummaryColumn.
     *
     * @param string $SummaryColumn
     *
     * @return SummaryConfig
     */
    public function setSummaryColumn($SummaryColumn)
    {
        $this->SummaryColumn = $SummaryColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummaryColumn()
    {
        return $this->SummaryColumn;
    }


}
