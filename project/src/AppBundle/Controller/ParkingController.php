<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parking;
use AppBundle\Repository\ParkingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/parking")
 */
class ParkingController extends Controller
{
    protected function getRepository(): ParkingRepository
    {
        return $this->getDoctrine()->getRepository(Parking::class);
    }

    /**
     * @Route("/", name="parking_index")
     */
    public function indexAction(Request $request)
    {
        $parkingList = $this->getRepository()->retrieveAll();
        return $this->render('parking/index.html.twig', [
            'parkingList' => $parkingList
        ]);
    }

    /**
     * @Route("/{id}/show", name="parking_show")
     */
    public function showAction(Request $request, int $id)
    {
        $parking = $this->getRepository()->retrieve($id);
        return $this->render('parking/show.html.twig', [
            'parking' => $parking
        ]);
    }
}
