<?php
 


namespace Zuni\DataTableBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig_Loader_String;
use Zuni\DataTableBundle\Entity\DataTable;


/**
 * DataTableController é a classe responsavel por cuidar de todas 
 * as requisisões referentes a grid data-table que irá responder com 
 * um json para monta a grid 
 * 
 * @Route("/datatable")
 */
class DataTableController extends Controller
{
    
    /**
     * Retorna um response json 
     * @Route("/grid")
     * @Method("POST")
     
     */
    public function gridAction(Request $request) 
    {
        return new JsonResponse($this->getData($request));
    }
    
    
    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request) 
    {
        $dataTable = $this->createDataTableFromRequest($request);
        $twigLoaderString = clone $this->get('twig');
        $twigLoaderString->setLoader(new Twig_Loader_String());
        return $dataTable->getData($this->getDoctrine()->getManager(), $request->request->getInt("sEcho"), $this->get("templating"), $twigLoaderString);
    }
    
    
    /**
     * Instancia um DataTable a partir de um request
     * @param Request $request
     * @return DataTable
     */
    private function createDataTableFromRequest(Request $request)
    {
        $gets = \explode(",", $request->request->get("gets"));
        $typeParamenters = \explode(",", $request->request->get("typeParamenter"));
        $entity = $request->get("entity");
        $lenght = $request->request->get("iDisplayLength");
        $start = $request->request->get("iDisplayStart");
        $search = $request->request->get("sSearch");
        $columnOrder = $request->request->get("iSortCol_0");
        $typeOrder = $request->request->get("sSortDir_0");
        
        return new DataTable($gets, $typeParamenters, $entity, $lenght, $start, $columnOrder, $typeOrder, $search);
    }
    
}