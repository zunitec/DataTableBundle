<?php

namespace Zuni\DataTableBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Environment;

/**
 * 
 * Entity que representa a grid do datatable js
 * 
 */
class DataTable
{

    /**
     *
     * @var integer
     */
    private $id;

    /**
     *
     * Métodos de acesso
     * 
     * @var string 
     */
    private $gets;

    /**
     *
     * Tipo de parametros, pode ser ação o acesso
     * 
     * @var array 
     */
    private $typeParamenters;

    /**
     *
     * Nome da entidade com por ex: ZuniPessoaBundle:Cidade
     * 
     * @var string 
     */
    private $entity;

    /**
     * Apelido da entidade, se a entidade for
     * ZuniPessoaBundle:Cidade as cidade, o apelido será cidade
     * 
     * @var string 
     */
    private $aliasEntity;

    /**
     *
     * Offset, até onde será mostrada as entidades
     * 
     * @var integer 
     */
    private $length;

    /**
     * páginação, de onde começa a mostrar a grid
     * @var integer 
     */
    private $start;

    /**
     *
     * Número da coluna que vai ser ordenada
     * 
     * @var integer
     */
    private $columnOrderPos;

    /**
     *  
     * tipo de ordenação 
     * 
     * @var string ASC | DESC 
     */
    private $typeOrder;

    /**
     * Filtro geral do datatable
     * @var string
     */
    private $search;

    /**
     *
     */
    private $entities;

    /**
     * array que contém todos os apelidos de todas as entidades
     * gerenciadas no atual DQL 
     * 
     * @var array 
     */
    private $aliasEntities;

    /**
     * Todas as colunas da datatable
     * @var array
     */
    private $columns;

    /**
     *
     * Nome do método que contém um dql parte, para montar o filtro dql
     * @var string 
     */
    private $methodDqlPart;

    /**
     * Quaisquer parametros passados para o datatable
     * @var string json personalizado, separador por "|" 
     */
    private $parameters;

    /**
     * DQL parte, usado para montar o sql que busca a coleção de entidades
     * @var stirng 
     */
    private $dqlPart;

    /**
     *
     * DQL parametros, compõe o DQL parte
     * @var array 
     */
    private $dqlParam;

    /**
     *
     * Quantidade de views que tem a table 
     * 
     * @var integer 
     */
    private $amountView;

    /**
     * 
     * Usado para renderizar as actions
     * 
     * @var TwigEngine 
     */
    private $twig;

    /**
     *
     * Usado para renderizar os métodos
     * 
     * @var Twig_Environment 
     */
    private $twigLoaderString;

    /**
     * Caminho do arquivo de ações para renderizar os botões da grid
     * 
     * @var string
     */
    private $pathFileActions;
    
    /**
     * Construtor
     */
    public function __construct($entity, TwigEngine $twig, Twig_Environment $twigLoaderString)
    {
        $this->setEntityAndAlias($entity);
        $this->setTwig($twig);
        $this->setTwigLoaderString($twigLoaderString);
    }

    /**
     * 
     * Iniciliza DataTable Com o request
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return DataTable 
     */
    public function bootFromRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        if ($request->get("entity")) {
            $this->setEntityAndAlias($request->get("entity"));
        }

        if ($request->request->get("typeParamenter")) {
            $typeParamenters = \explode(",", $request->request->get("typeParamenter"));
            $this->setTypeParamenters($typeParamenters);
        }

        if ($request->request->get("gets")) {
            $gets = \explode(",", $request->request->get("gets"));
            $this->setGets($gets);
        }

        $this->setLength($request->request->get("iDisplayLength"));
        $this->setStart($request->request->get("iDisplayStart"));
        $this->setColumnOrderPos($request->request->get("iSortCol_0"));
        $this->setTypeOrder($request->request->get("sSortDir_0"));
        $this->setSearch($request->request->get("sSearch"));
        $this->setMethodDqlPart($request->request->get("methodDqlPart"));
        $this->setParameters($request->request->get("parameters"));
        $this->setAmountView($request->request->getInt("sEcho"));

        return $this;
    }

    /**
     * Não implementado
     * @return int
     */
    private function getId()
    {
        return $this->id;
    }

    /**
     * 
     * Add new Column
     * 
     * @param string $stringGet Prperty access
     * @return \Zuni\DataTableBundle\Entity\DataTable
     */
    public function addColumn($stringGet, $alias = "e")
    {
        $this->gets[] = $alias . "." . $stringGet;
        $this->typeParamenters[] = 'access';

        return $this;
    }

    /**
     * 
     * Add new Column Action
     * 
     * @param array $actions 
     * @return \Zuni\DataTableBundle\Entity\DataTable
     */
    public function addColumnAction(array $actions)
    {
        $this->gets[] = $actions;
        $this->typeParamenters[] = 'action';

        return $this;
    }

    public function getGets()
    {
        return $this->gets;
    }

    public function setGets(array $gets)
    {
        $this->gets = $gets;
    }

    public function getTypeParamenters()
    {
        return $this->typeParamenters;
    }

    public function setTypeParamenters(array $typeParamenters)
    {
        $this->typeParamenters = $typeParamenters;
    }

    public function getPathFileActions()
    {
        return $this->pathFileActions;
    }

    public function setPathFileActions($pathFileActions)
    {
        $this->pathFileActions = $pathFileActions;
    }

        
    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public function getAliasEntity()
    {
        return $this->aliasEntity;
    }

    public function setAliasEntity($aliasEntity)
    {
        $this->aliasEntity = $aliasEntity;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getColumnOrderPos()
    {
        return $this->columnOrderPos;
    }

    public function setColumnOrderPos($columnOrder)
    {
        $this->columnOrderPos = $columnOrder;
    }

    public function getTypeOrder()
    {
        return $this->typeOrder;
    }

    public function setTypeOrder($typeOrder)
    {
        $this->typeOrder = $typeOrder;
    }

    public function getSearch()
    {
        return $this->search;
    }

    public function setSearch($search)
    {
        $this->search = $search;
    }

    public function getMethodDqlPart()
    {
        return $this->methodDqlPart;
    }

    public function setMethodDqlPart($methodDqlPart)
    {
        $this->methodDqlPart = $methodDqlPart;
    }

    public function getParameter($index)
    {
        $parameters = $this->getParameters();

        return array_key_exists($index, $parameters) ? $parameters[$index] : null;
    }

    public function getParameters()
    {

        if ($this->parameters && !is_array($this->parameters)) {
            $this->parameters = json_decode("{" . str_replace("|", ",", $this->parameters) . "}", true);
        }

        return $this->parameters;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * 
     * Get Dql Part
     * 
     * @return string
     */
    public function getDqlPart()
    {
        return $this->dqlPart;
    }

    /**
     * 
     * Set DQL part 
     * 
     * @param string
     */
    public function setDqlPart($dqlPart)
    {
        $this->dqlPart = $dqlPart;
    }

    /**
     * 
     * Get Dql param
     * 
     * @return array
     */
    public function getDqlParam()
    {
        if (!$this->dqlParam) {
            $this->dqlParam = array();
        }

        return $this->dqlParam;
    }

    /**
     * 
     * Set Dql parameters
     * 
     * @param array $dqlParam
     */
    public function setDqlParam(array $dqlParam)
    {
        $this->dqlParam = $dqlParam;
    }

    /**
     * 
     * Get Amount View
     * 
     * @return integer
     */
    public function getAmountView()
    {
        return $this->amountView;
    }

    /**
     * 
     * Set Amount View
     * 
     * @param integer $amountView
     */
    public function setAmountView($amountView)
    {
        $this->amountView = $amountView;
    }

    /**
     * 
     * Set Twig Engine
     * 
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $twig
     */
    public function setTwig(TwigEngine $twig)
    {
        $this->twig = $twig;
    }

    /**
     * 
     * Set Twig Environment usado para renderizar as actions
     * 
     * @param Twig_Environment $twigLoaderString
     */
    public function setTwigLoaderString(Twig_Environment $twigLoaderString)
    {
        $this->twigLoaderString = $twigLoaderString;
    }

    /**
     * 
     * Separa o apelido do nome da entidade, e seta os dois atributos
     * 
     * @param strin $entity ZuniPessoaBundle:Cidade as cidade
     */
    public function setEntityAndAlias($entity)
    {
        $entityAndAlias = $this->getEntityAndAliasName($entity);
        $this->setAliasEntity($entityAndAlias['alias']);
        $this->setEntity($entityAndAlias['entity']);
    }

    /**
     * Retirna um array assoc com o nome da entity e se alias 
     * 
     * Here is an inline example:
     * <pre><code>
     * <?php
     * $array = $this->getEntityAndAliasName($entityParam);
     * echo $array['entity'];
     * echo $array['alias'];
     * ?>
     * </code></pre>
     * 
     * @todo Testar quando não tiver alias
     * @param string $entityParam passodo pelo request
     * @return array
     */
    private function getEntityAndAliasName($entityParam)
    {
        $paramReturn = array();
        $entityParam = \str_replace(" AS ", " as ", $entityParam);
        list($paramReturn["entity"], $paramReturn["alias"]) = \explode(" as ", \trim($entityParam), 2);
        $paramReturn["entity"] = \trim($paramReturn["entity"]);
        $paramReturn["alias"] = \trim($paramReturn["alias"]);
        return $paramReturn;
    }

    /**
     * Retorna todas as entities que serão necessarias para pegar os 
     * valores de gets com nome e apelido 
     * 
     * O Método irá retornar um array com o nome da entity coomo as chaves do array
     * e em cada entity, terá uma chave alias e outra previous
     * 
     * @todo Recolocar $this->getGets no lugar de $gets
     * @todo Ignorar ponto de valores de parametros twig 
     * @return array Entitis com apelidos  
     */
    public function getAssociatedEntities()
    {
        if (!$this->entities) {

            $typeParamenters = $this->getTypeParamenters();
            foreach ($this->getGets() as $key => $get) {
                if ($typeParamenters[$key] == "access") {
                    $this->setAssociatedEntitiesRecursive($get);
                }
            }
        }

        if (!$this->entities) {
            $this->entities = array();
        }

        return $this->entities;
    }

    /**
     * 
     * Valida todoas as classes usadas para ter acesso ao método
     * 
     * 
     * @param string $get Twig Syntax
     */
    private function setAssociatedEntitiesRecursive($get)
    {

        $get = $this->clrearMetodoTwig($get);

        $separate = \explode(".", $get);

        if (count($separate) != 2) {
            $nameTable = $separate[1];

            if (empty($this->entities[$nameTable])) {
                $this->entities[$nameTable] = array();
                $this->entities[$nameTable]['alias'] = "t" . count($this->entities);
                $this->entities[$nameTable]['previous'] = $separate[0];

                unset($separate[0]);
                $this->setAssociatedEntitiesRecursive(\implode(".", $separate));
            }
            else {
                $this->entities[$nameTable]['select'] = count($separate) == 2;
                unset($separate[0]);
                $this->setAssociatedEntitiesRecursive(\implode(".", $separate));
            }
        }
    }

    /**
     * Com todos os apelidos de classes que a data table possui
     * @return array
     */
    public function getAliasEntities()
    {

        if (!$this->aliasEntities) {
            $this->aliasEntities = array($this->getAliasEntity());

            foreach ($this->getAssociatedEntities() as $associatedEntity) {
                $this->aliasEntities[] = $associatedEntity['alias'];
            }
        }

        return $this->aliasEntities;
    }

    /**
     * Retorna a coluna que será ordenada
     * 
     * @return string 
     */
    public function getColumnOrder()
    {
        if ($this->getColumnOrderPos() === null) {
            return null;
        }

        $columns = $this->getColumns();
        return $columns[$this->getColumnOrderPos()];
    }

    /**
     * Limpa os filtros da string twig 
     * @param string $stringTwig
     */
    private function clrearMetodoTwig($stringTwig)
    {
        $stringTwig = explode("|", $stringTwig, 2);
        return $stringTwig[0];
    }

    /**
     * 
     * Retorna todas as colunas com e suas classs com apelidos 
     * 
     * Ex: pessoa.id
     * 
     * @return array
     */
    public function getColumns()
    {
        if (!$this->columns) {

            $this->columns = array();

            $typeParam = $this->getTypeParamenters();

            foreach ($this->getGets() as $key => $get) {
                if ($typeParam[$key] === 'access') {

                    $getExploded = \explode(".", $get);

                    $classOrder = $getExploded[count($getExploded) - 2];
                    $columnOrder = $this->clrearMetodoTwig($getExploded[count($getExploded) - 1]);

                    $associatedEntities = $this->getAssociatedEntities();

                    if (isset($associatedEntities[$classOrder]['alias'])) {
                        $aliasEntityClassOrder = $associatedEntities[$classOrder]['alias'];
                    }
                    else {
                        $aliasEntityClassOrder = $this->getAliasEntity();
                    }

                    $this->columns[] = $aliasEntityClassOrder . "." . $columnOrder;
                }
            }
        }

        return $this->columns;
    }

    /**
     * 
     * Retorna um array contendo a lista de Entities para montar a grid 
     * 
     * @param type $entityManager Usado para montar o QueryBuilder
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $twig
     * @param \ArrayObject $collectionEntity Coleção de entidades
     * @return array
     */
    public function getData($entityManager, $collectionEntity = null)
    {

        if (!$collectionEntity) {
            $collectionEntity = $this->getCollectionEntities(new QueryBuilder($entityManager), $entityManager);
        }

        $rows = array();
        foreach ($collectionEntity as $entity) {
            $row = array();
            for ($i = 0; $i < count($this->getGets()); $i++) {
                $get = $this->getGets();
                $types = $this->getTypeParamenters();

                if ($types[$i] === "access") {
                    $row[] = $this->getValueFromSyntaxTwig($this->twigLoaderString, $get[$i], array($this->getAliasEntity() => $entity));
                }
                else {

                    if (!is_array($get[$i])) {
                        $get[$i] = json_decode("{" . str_replace("|", ",", $get[$i]) . "}", true);
                    }

                    $row[] = $this->renderActions($this->twig, $get[$i], $entity);
                }
            }

            $rows[] = $row;
        }

        $amountFromEntity = $this->getAmountFromEntity(new QueryBuilder($entityManager));

        return $returnData = array(
            "sEcho" => $this->getAmountView(),
            "iTotalRecords" => $amountFromEntity,
            "iTotalDisplayRecords" => $amountFromEntity,
            "aaData" => $rows
        );
    }

    /**
     * Renderiza o html das ações
     * 
     * @return string 
     */
    private function renderActions(TwigEngine $twig, array $actions, $entity)
    {
        return $twig->render($this->getPathFileActions(), array("actions" => $actions, "entity" => $entity));
    }

    /**
     * 
     * Retorna a quantidade total de entidades que tem no banco 
     * 
     * @param \Doctrine\ORM\QueryBuilder $query
     * @return integer
     */
    private function getAmountFromEntity(QueryBuilder $query)
    {
        $query->select('COUNT(' . $this->getAliasEntity() . ')');
        $query->from($this->getEntity(), $this->getAliasEntity());

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * 
     * Valida toda a string twig de forma recursiva, e retorna , 
     * renderizado
     * 
     * @todo Alterar método para não testar as variaveis com Twig
     * Utilizar a classe PropertyAcess do symfony ( desempenho )
     * @param Twig_Environment $twig
     * @param stirng $string
     * @param array $parameter
     * @return boolean
     */
    private function isValueValidFromSyntaxTwig(Twig_Environment $twig, $string, array $parameter, $posTest = 2)
    {
        $stringExploded = \explode(".", $string);
        if (count($stringExploded) <= 2) {
            return true;
        }

        $stringConcat = $stringExploded[0];

        for ($i = 1; $i < $posTest; $i++) {
            $stringConcat .= "." . $stringExploded[$i];
        }

        $valid = ((boolean) \trim($twig->render($this->createStringTwigTest($stringConcat), $parameter)));

        if (count($stringExploded) >= $posTest + 1 && $valid) {
            $valid = $this->isValueValidFromSyntaxTwig($twig, $string, $parameter, ++$posTest);
        }

        return $valid;
    }

    /**
     * Cria a stirng de teste
     * @param string $valueTest
     * @return string
     */
    private function createStringTwigTest($valueTest)
    {
        $stringTest = <<<TWIG
                {% if {$valueTest} is not null %}
                {{true}}
                {% else %}
                {{false}}
                {% endif %}
TWIG;
        return $stringTest;
    }

    /**
     * 
     * Retorna a string twig renderezida com os filtros 
     * 
     * @param Twig_Environment $twig
     * @param stirng $string
     * @param array $parameter
     * @return mixed 
     */
    private function getValueFromSyntaxTwig(Twig_Environment $twig, $string, array $parameter)
    {
        $validString = $this->isValueValidFromSyntaxTwig($twig, $this->clrearMetodoTwig($string), $parameter);

        if ($validString) {
            return $twig->render("{{" . \trim($string) . "}}", $parameter);
        }

        return "";
    }

    /**
     * 
     * Invoca o método da entidade que possui a dql part
     * 
     * @param \Doctrine\ORM\EntityManager $entityManager Usado para resolver o 
     * apelido da classe, converte ZuniPessoaBundle:Pessoa em seu namespace real
     * @return string
     */
    private function getDqlPartFromMethod($entityManager)
    {

        $newInstance = $entityManager->getClassMetadata($this->getEntity())->newInstance();

        return PropertyAccess::getPropertyAccessor()->getValue($newInstance, $this->getMethodDqlPart());
    }

    /**
     * 
     * Converte o DQL part passado, para o verdadeira pedaço DQL 
     * com os apelidos da entidade
     * 
     * @param string $param
     * @param array $alias Apelidos de todos os relacionamenstos 
     */
    private function getRealDqlPart($dqlPart, $alias)
    {
        $open = strpos($dqlPart, "{");

        if ($open === false) {
            return $dqlPart;
        }

        $open++;

        $close = strpos($dqlPart, "}");
        $pieceDql = substr($dqlPart, $open, $close - $open);

        $realPieceDql = $this->getAliasEntity() . "." . $pieceDql;

        if (substr_count($realPieceDql, ".") > 1) {
            $realPieceDqlExploded = explode(".", $realPieceDql);
            $entityName = $realPieceDqlExploded[count($realPieceDqlExploded) - 2];
            $entityValue = $realPieceDqlExploded[count($realPieceDqlExploded) - 1];

            $realPieceDql = $alias[$entityName]['alias'] . "." . $entityValue;
        }

        $dqlPart = str_replace("{{$pieceDql}}", $realPieceDql, $dqlPart);

        if (strpos($dqlPart, "{") !== false) {
            $dqlPart = $this->getRealDqlPart($dqlPart, $alias);
        }

        return $dqlPart;
    }

    /**
     * Retorna uma coleção de itens com todos os requisitos para o mesmo 
     * order limit ... 
     * @todo Mudar método para o Reposioty de DataTable
     */
    public function getCollectionEntities(QueryBuilder $query, $entityManager)
    {

        $query->select($this->getAliasEntities());
        $query->from($this->getEntity(), $this->getAliasEntity());

        $associatedEntities = $this->getAssociatedEntities();
        $aliasJoinClass = "";

        foreach ($associatedEntities as $key => $assicuatedEntity) {
            if (isset($associatedEntities[$assicuatedEntity['previous']])) {
                $aliasJoinClass = $associatedEntities[$assicuatedEntity['previous']]['alias'];
            }
            else {
                $aliasJoinClass = $this->getAliasEntity();
            }

            $query->leftJoin($aliasJoinClass . "." . $key, $assicuatedEntity['alias']);
        }


        $typeGet = $this->getTypeParamenters();
        if ($typeGet[$this->getColumnOrderPos()] == "access") {
            $query->orderBy($this->getColumnOrder(), $this->getTypeOrder());
        }

        foreach ($this->getColumns() as $columns) {
            $query->orWhere("LOWER(CHAR($columns)) LIKE :search");
        }

        if ($this->getDqlPart()) {
            $query->andWhere("( " . $this->getRealDqlPart($this->getDqlPart(), $associatedEntities) . " )");
        }


        if ($this->getMethodDqlPart()) {
            $query->andWhere("( " . $this->getDqlPartFromMethod($entityManager) . " )");
        }

        $query->setFirstResult($this->getStart())
                ->setMaxResults($this->getLength());

        if ($this->getDqlParam()) {
            foreach ($this->getDqlParam() as $param => $value) {
                $query->setParameter($param, $value);
            }
        }

        $query->setParameter("search", "%" . \strtolower($this->getSearch()) . "%");

        return $query->getQuery()->getResult();
    }

}