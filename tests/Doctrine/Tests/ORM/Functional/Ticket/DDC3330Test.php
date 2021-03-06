<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Tests\OrmFunctionalTestCase;

use function count;
use function iterator_to_array;

/**
 * Functional tests for paginator with collection order
 */
class DDC3330Test extends OrmFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpEntitySchema(
            [
                DDC3330_Building::class,
                DDC3330_Hall::class,
            ]
        );
    }

    public function testIssueCollectionOrderWithPaginator(): void
    {
        $this->createBuildingAndHalls();
        $this->createBuildingAndHalls();
        $this->createBuildingAndHalls();

        $this->_em->clear();

        $query = $this->_em->createQuery(
            'SELECT b, h' .
            ' FROM Doctrine\Tests\ORM\Functional\Ticket\DDC3330_Building b' .
            ' LEFT JOIN b.halls h' .
            ' ORDER BY b.id ASC, h.name DESC'
        )
        ->setMaxResults(3);

        $paginator = new Paginator($query, true);

        $this->assertEquals(3, count(iterator_to_array($paginator)), 'Count is not correct for pagination');
    }

    /**
     * Create a building and 10 halls
     */
    private function createBuildingAndHalls(): void
    {
        $building = new DDC3330_Building();

        for ($i = 0; $i < 10; $i++) {
            $hall       = new DDC3330_Hall();
            $hall->name = 'HALL-' . $i;
            $building->addHall($hall);
        }

        $this->_em->persist($building);
        $this->_em->flush();
    }
}

/**
 * @Entity @Table(name="ddc3330_building")
 */
class DDC3330_Building
{
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    public $id;

    /** @OneToMany(targetEntity="DDC3330_Hall", mappedBy="building", cascade={"persist"}) */
    public $halls;

    public function addHall(DDC3330_Hall $hall): void
    {
        $this->halls[]  = $hall;
        $hall->building = $this;
    }
}

/**
 * @Entity @Table(name="ddc3330_hall")
 */
class DDC3330_Hall
{
    /**
     * @var int
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var DDC3330_Building
     * @ManyToOne(targetEntity="DDC3330_Building", inversedBy="halls")
     */
    public $building;

    /**
     * @var string
     * @Column(type="string", length=100)
     */
    public $name;
}
