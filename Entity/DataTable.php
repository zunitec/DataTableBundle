<?php

namespace Zuni\DataTableBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Twig_Environment;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * 
 * 
 */
class DataTable
{
    
    private $id;
    private $gets;
    private $typeParamenters;
    private $entity;
    private $aliasEntity;
    private $length;
    private $start;
    private $columnOrderPos;
    private $typeOrder;
    private $search;
    private $entities;
    private $aliasEntities;
    private $columns;
    
    
    function __construct(array $gets = array(),array $typeParamenters = array(), $entity = "", $length = 10, $start = 0, $columnOrderPos = 0, $typeOrder = "asc", $search ="")
    {
        $this->setGets($gets);
        $this->setTypeParamenters($typeParamenters);
        $this->setEntityAndAlias($entity);
        $this->setLength($length);
        $this->setStart($start);
        $this->setColumnOrderPos($columnOrderPos);
        $this->setTypeOrder($typeOrder);
        $this->setSearch($search);
    }

    public function getId()
    {
        return $this->id;
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
        $entityParam = \str_replace("AS", "as", $entityParam);
        list($paramReturn["entity"] , $paramReturn["alias"]) = \explode("as", \trim($entityParam), 2);
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
        if (!$this->entities)
        {
            
            $typeParamenters = $this->getTypeParamenters();
            foreach ($this->getGets() as $key => $get)
            {
                if ($typeParamenters[$key] == "access")
                {
                    $this->setAssociatedEntitiesRecursive($get);
                }
            }
        }
        
        if(!$this->entities)
        {
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
        
        if (count($separate) != 2)
        {
            $nameTable = $separate[1];

            if(empty($this->entities[$nameTable]))
            {
                $this->entities[$nameTable] = array();
                $this->entities[$nameTable]['alias'] = "t".count($this->entities);
                $this->entities[$nameTable]['previous'] = $separate[0];
                
                unset($separate[0]);
                $this->setAssociatedEntitiesRecursive(\implode(".", $separate));
                
            }
            else
            {
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
        
        if (!$this->aliasEntities)
        {
            $this->aliasEntities = array($this->getAliasEntity());
            
            foreach ($this->getAssociatedEntities() as $associatedEntity)
            {
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
        if ($this->getColumnOrderPos() === null)
        {
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
        $stringTwig = explode("|", $stringTwig , 2);
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
        if (!$this->columns)
        {
            
            $this->columns = array();
            
            $typeParam = $this->getTypeParamenters();
            
            foreach ($this->getGets() as $key => $get)
            {
                if($typeParam[$key] === 'access')
                {
                    
                    $getExploded = \explode(".", $get);

                    $classOrder = $getExploded[count($getExploded) - 2];
                    $columnOrder = $this->clrearMetodoTwig($getExploded[count($getExploded) - 1]);

                    $associatedEntities = $this->getAssociatedEntities();

                    if (isset($associatedEntities[$classOrder]['alias']))
                    {
                        $aliasEntityClassOrder = $associatedEntities[$classOrder]['alias'];
                    }
                    else
                    {
                        $aliasEntityClassOrder = $this->getAliasEntity();
                    }

                    $this->columns[] = $aliasEntityClassOrder.".".$columnOrder;
                
                }
            }
            
        }
        
        return $this->columns;
    }
    
    /**
     * 
     * Retorna um array contendo a lista de Entities para montar a grid 
     * 
     * @param type $entityManager
     * @param string $sEcho parametro que vem por post 
     * @param \Symfony\Bundle\TwigBundle\TwigEngine $twig
     * @return array
     */
    public function getData($entityManager, $sEcho, TwigEngine $twig, Twig_Environment $twigLoaderString)
    {
        
        $collectionEntity = $this->getCollectionEntities(new QueryBuilder($entityManager));
        
        $rows = array();
        $paramActions = null;
        foreach ($collectionEntity as $entity)
        {
            $row = array();
            for ($i = 0; $i < count($this->getGets()); $i++)
            {
                $get = $this->getGets();
                $types = $this->getTypeParamenters();
                
                if($types[$i] === "access"){
                    $row[] = $this->getValueFromSyntaxTwig($twigLoaderString, $get[$i], array($this->getAliasEntity() => $entity));
                }else{
                    
                    if(!$paramActions)
                    {
                        $paramActions = json_decode("{".str_replace("|", ",", $get[$i])."}", true);
                    }
                    
                    $row[] = $this->renderActions($twig, $paramActions , $entity);
                }
            }
            
            $rows[] = $row;
        }
        
        $amountFromEntity = $this->getAmountFromEntity(new QueryBuilder($entityManager));
        
        return $returnData = array(
		"sEcho" => $sEcho,
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
        return $twig->render("ZuniDataTableBundle:DataTable:actions.html.twig", array("actions" => $actions, "entity" => $entity));
    }
    
    private function getAmountFromEntity(QueryBuilder $query)
    {
        $query->select('COUNT('.$this->getAliasEntity().')');
        $query->from($this->getEntity(),  $this->getAliasEntity());

        return $query->getQuery()->getSingleScalarResult();
    }
    
    /**
     * 
     * Valida toda a string twig de forma recursiva, e retorna , 
     * renderizado
     * 
     * @param Twig_Environment $twig
     * @param stirng $string
     * @param array $parameter
     * @return boolean
     */
    private function isValueValidFromSyntaxTwig(Twig_Environment $twig, $string, array $parameter, $posTest = 2)
    {
        $stringExploded = \explode(".", $string);
        if (count($stringExploded) <= 2)
        {
            return true;
        }
        
        $stringConcat = $stringExploded[0];
        
        for ($i = 1; $i < $posTest; $i++)
        {
            $stringConcat .= ".".$stringExploded[$i];
        }
        
        $valid = ((boolean)\trim($twig->render($this->createStringTwigTest($stringConcat), $parameter)));
        
        if(count($stringExploded) >= $posTest + 1 && $valid)
        {
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
        
        if ($validString)
        {
            return $twig->render("{{".\trim($string)."}}", $parameter);
        }
        
        return "";
    }
    
    
     /**
     * Retorna uma coleção de itens com todos os requisitos para o mesmo 
     * order limit ... 
     * @todo Mudar método para o Reposioty de DataTable
     */
    public function getCollectionEntities(QueryBuilder $query)
    {
        
        $query->select($this->getAliasEntities());
        $query->from($this->getEntity(), $this->getAliasEntity());
        
        $associatedEntities = $this->getAssociatedEntities();
        $aliasJoinClass = "";
        
        foreach ($associatedEntities as $key => $assicuatedEntity)
        {
            if (isset($associatedEntities[$assicuatedEntity['previous']]))
            {
                $aliasJoinClass= $associatedEntities[$assicuatedEntity['previous']]['alias'];
            }
            else
            {
                $aliasJoinClass= $this->getAliasEntity();
            }
            
            $query->leftJoin($aliasJoinClass.".".$key, $assicuatedEntity['alias']);
        }
        
        
        $typeGet = $this->getTypeParamenters();
        if ($typeGet[$this->getColumnOrderPos()] == "access")
        {
            $query->orderBy($this->getColumnOrder(), $this->getTypeOrder());
        }
        
        foreach ($this->getColumns() as $columns)
        {
            $query->orWhere($columns." LIKE :search");
        }
        
        $query->setFirstResult($this->getStart())
              ->setMaxResults($this->getLength());
        
        $query->setParameter("search", "%".$this->getSearch()."%");        
        
        return $query->getQuery()->getResult();
    }
    
}