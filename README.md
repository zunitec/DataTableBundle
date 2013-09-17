Data Table PHP 
=====================================================

Exmplo de uso 
--------


Controlador
=====================================================
/**
 * 
 * @Route("/grid/ajax", name="cidade_grid")
 * @Secure(roles="ROLE_CIDADE_SHOW")
 */
public function gridAction(Request $request)
{
    $dataTable = $this->createDataTable($request);

    $dataTable
            ->addColumn('id')
            ->addColumn('nome')
            ->addColumn('estado.nome')
            ->addColumnAction($this->getDataTableActionsDefault())
    ;

    $dataTable->setDqlPart('{estado.nome} = :estadoNome');
    $dataTable->setDqlParam(array('estadoNome' => 'sc'));


    return $this->getDataTableResponse($dataTable);
}



Index 
=====================================================

<table data-table-type="datatable-server-side" data-route="{{path("cidade_grid")}}">
    <thead>
        <tr>
            <th>{{ "cidade.id"|trans }}</th>
            <th>{{ "cidade.nome"|trans }}</th>
            <th>{{ "estado.titulo"|trans }}</th>
            <th data-table-width="132px">{{"grid.acoes"|trans}}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>



Métodos requeridos no controlador Super
=====================================================
/**
 * 
 * Create new DataTable for response json
 * 
 * @param \Symfony\Component\HttpFoundation\Request $request
 * @param string $condition DQL Contidion 
 * @param arrray $paramCondition
 * @param type $entity
 * @return \Zuni\DataTableBundle\Entity\DataTable
 */
protected function createDataTable(Request $request, $condition = "", array $paramCondition = array(), $entity = null)
{
    $entity = $entity ? $entity : $this->getNameEntity()." AS e";

    return new \Zuni\DataTableBundle\Entity\DataTable($request, $this->get("templating"), $this->getTwigLoaderString(), $entity);
}

/**
 * 
 * Create Data Table Response
 * 
 * @param \Zuni\DataTableBundle\Entity\DataTable $dataTable
 * @return \Symfony\Component\HttpFoundation\JsonResponse
 */
protected function getDataTableResponse(\Zuni\DataTableBundle\Entity\DataTable $dataTable, $collectionsEntity = null )
{
    $arrayData = $dataTable->getData($this->getDoctrine()->getManager(), $collectionsEntity);

    return new JsonResponse($arrayData);
}

/**
 * 
 * Clona e modifica o twig do symfony para renderizar strings 
 * 
 * @return \Symfony\Bridge\Twig\TwigEngine
 */
protected function getTwigLoaderString()
{
    $twigLoaderString = clone $this->get('twig');
    $twigLoaderString->setLoader(new Twig_Loader_String());

    return $twigLoaderString;
}


/**
 * 
 * Retorna as ações padrão da entidade
 * 
 * @todo Mudar as ações da DataTable para uma classe do bundle 
 * @return array
 */
protected function getDataTableActionsDefault()
{
    $entity = $this->getAliasEntity();
    $entityUpperCase = strtoupper($entity);

    return array(
        'show' => array(
            'route' => $entity.'_show', 'role' => "ROLE_{$entityUpperCase}_SHOW"
        ),
        'edit' => array(
            'route' => $entity.'_edit', 'role' => "ROLE_{$entityUpperCase}_EDIT"
        ),
        'delete' => array(
            'route' => $entity.'_delete_ajax', 'role' => "ROLE_{$entityUpperCase}_EDIT"
        ),
    );
}
