<?php

declare(strict_types=1);

namespace Doctrine\Tests\Models\Taxi;

/**
 * @Entity
 * @Table(name="taxi_driver")
 */
class Driver
{
    /**
     * @var int
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @Column(type="string", length=255); */
    private $name;

    /** @OneToMany(targetEntity="Ride", mappedBy="driver") */
    private $freeDriverRides;

    /** @OneToMany(targetEntity="PaidRide", mappedBy="driver") */
    private $driverRides;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }
}
