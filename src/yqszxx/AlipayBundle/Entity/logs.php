<?php

namespace yqszxx\AlipayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * logs
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class logs
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="array", type="array")
     */
    private $array;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set array
     *
     * @param array $array
     * @return logs
     */
    public function setArray($array)
    {
        $this->array = $array;

        return $this;
    }

    /**
     * Get array
     *
     * @return array 
     */
    public function getArray()
    {
        return $this->array;
    }
}
