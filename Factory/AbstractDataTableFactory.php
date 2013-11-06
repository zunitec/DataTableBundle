<?php

namespace Zuni\DataTableBundle\Factory;

/**
 * @author Neto
 */
abstract class AbstractDataTableFactory
{
    /**
     * @return DataTable 
     */
    abstract public function createDataTable($entity);
}
