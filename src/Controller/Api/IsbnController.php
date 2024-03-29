<?php

namespace App\Controller\Api;

use App\Service\GetBookByIsbn;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class IsbnController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/isbn")
     * @Rest\View(serializerGroups={"get_book_by_isbn"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(GetBookByIsbn $getBookByIsbn, Request $request)
    {
        $isbn = $request->get('isbn');

        if ($isbn === null) {
            return View::create('Please, specify an isbn', Response::HTTP_BAD_REQUEST);
        }
        
        $json = ($getBookByIsbn)($isbn);

        return View::create($json);
    }
}
