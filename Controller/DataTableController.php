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
        $dataTable = $this->get("datatable.factory")->createDataTable($request->request->get("entity"));
        
        return $dataTable->getData($this->getDoctrine()->getManager());
    }
    
    /**
     * @Route("/publico/dataTable/js/dataTableUsage.js", name="zuni_datatable_usage")
     */
    public function dataTableUsageAction()
    {
        $response = $this->render('ZuniDataTableBundle:DataTable:jquery.dataTableUsage.js.twig', array());
        $response->headers->set('Content-Type', 'text/javascript; charset=UTF-8');
        return $response;
    }
    
}