<?php

namespace Zuni\DataTableBundle\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Loader_String;

class DataTableFactory extends AbstractDataTableFactory
{

    /**
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * Retorna o endereço das ações da DataTable
     * 
     */
    public function createDataTable($entity)
    {
        $fileActions =  $this->container->getParameter('zuni_data_table.file_actions');

        $twigLoaderString = clone $this->container->get('twig');
        $twigLoaderString->setLoader(new Twig_Loader_String());
        $dataTable = new \Zuni\DataTableBundle\Entity\DataTable($entity, $this->container->get("templating"), $twigLoaderString);

        $dataTable->bootFromRequest($this->container->get("request"));
        
        $dataTable->setPathFileActions($fileActions);
        
        return $dataTable;
    }

}
