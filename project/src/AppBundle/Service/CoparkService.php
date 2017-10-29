<?php

namespace AppBundle\Service;


use AppBundle\Entity\Parking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CoparkService
{
    protected $container;
    protected $em;

    /**
     * CoparkService constructor.
     * @param EntityManagerInterface $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function retrieveAllParkingFromApi()
    {
        // call API
        $client   = $this->container->get('eight_points_guzzle.client.copark');
        $response = $client->get('/ws/parking/lpa/list', [
            'headers' => [
                'X-Auth-Token'     => getenv('API_KEY')]
        ]);

        // get JSON object from api response
        $body = $response->getBody();
        $jsonObject = json_decode($body);

        if (!$jsonObject) {
            throw new \Exception('Invalid data object');
        }

        return $jsonObject->list_parking;
    }

    public function synchronizeParkingFromApi()
    {
        $parkingRepository = $this->em->getRepository(Parking::class);
        $parkingList = $this->retrieveAllParkingFromApi();

        foreach($parkingList as $parking)
        {
            $parkingToSave = $this->mapParking($parking);
            $parkingRepository->save($parkingToSave);
        }
    }

    private function mapParking($data) : Parking
    {
        $parking = new Parking();

        return $parking->setApiId($data->id)
            ->setAddress($data->address)
            ->setCity($data->city)
            ->setLatitude($data->latitude)
            ->setLongitude($data->longitude)
            ->setName($data->name)
            ->setPoi($data->poi)
            ->setZipCode($data->zipcode);
    }

}
