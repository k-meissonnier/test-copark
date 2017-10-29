<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParkingRepository")
 */
class Parking
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $apiId;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="float")
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float")
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string")
     */
    protected $address;

    /**
     * @ORM\Column(type="string")
     */
    protected $zipCode;

    /**
     * @ORM\Column(type="string")
     */
    protected $city;

    /**
     * @ORM\Column(type="text")
     */
    protected $poi;

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Parking
     */
    public function setId($id): Parking
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiId(): string
    {
        return $this->apiId;
    }

    /**
     * @param mixed $apiId
     * @return Parking
     */
    public function setApiId($apiId): Parking
    {
        $this->apiId = $apiId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Parking
     */
    public function setName($name): Parking
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     * @return Parking
     */
    public function setLatitude($latitude): Parking
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     * @return Parking
     */
    public function setLongitude($longitude): Parking
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return Parking
     */
    public function setAddress($address): Parking
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     * @return Parking
     */
    public function setZipCode($zipCode): Parking
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return Parking
     */
    public function setCity($city): Parking
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPoi(): string
    {
        return $this->poi;
    }

    /**
     * @param mixed $poi
     * @return Parking
     */
    public function setPoi($poi): Parking
    {
        $this->poi = $poi;
        return $this;
    }
}
