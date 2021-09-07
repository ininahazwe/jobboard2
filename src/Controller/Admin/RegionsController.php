<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/regions", name="admin_regions_")
 * @package App\Controller\Admin
 */
class RegionsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RegionsRepository $regionsRepo): Response
    {
        return $this->render('regions/login.html.twig', [
            'regions' => $regionsRepo->findAll()
        ]);
    }

    /**
     * @Route("/ajout", name="ajout")
     */
    public function ajoutRegion(Request $request): Response
    {
        $region = new Regions;

        $form = $this->createForm(RegionsType::class, $region);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            return $this->redirectToRoute('admin_regions_home');
        }

        return $this->render('regions/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}