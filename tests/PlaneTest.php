<?php
use Math_Plane\Plane;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-08-16 at 20:53:10.
 */
class PlaneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plane
     */
    protected $plane;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->x1 = new Math_Vector3(new Math_Tuple(array(0, 128, 128)));
        $this->y1 = new Math_Vector3(new Math_Tuple(array(128, 128, 128)));
        $this->z1 = new Math_Vector3(new Math_Tuple(array(128, 0, 128)));

        $this->x2 = new Math_Vector3(new Math_Tuple(array(128, 128, 0)));
        $this->y2 = new Math_Vector3(new Math_Tuple(array(128, 0, 0)));
        $this->z2 = new Math_Vector3(new Math_Tuple(array(128, 0, 128)));

        $this->x3 = new Math_Vector3(new Math_Tuple(array(128, 128, 128)));
        $this->y3 = new Math_Vector3(new Math_Tuple(array(0, 128, 128)));
        $this->z3 = new Math_Vector3(new Math_Tuple(array(0, 128, 0)));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Verify that an exception is thrown when trying to get plane from three points on one line
     */
    public function testPointsOnLine()
    {
        $this->setExpectedException('InvalidArgumentException');
        Plane::getInstanceByThreePositionVectors($this->x1, $this->x1, $this->x1);
    }

    /**
     * Verify that the planes normalized normal vector is correctly calculated
     */
    public function testGetNormalVector()
    {
        $plane = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $nv = $plane->getNormalVectorNormalized();
        $this->assertEquals(new Math_Tuple(array(0,0,1)), $nv->getTuple());
    }

    /**
     * Verify that the planes distance to origin is correctly calculated
     */
    public function testGetDistanceToOrigin()
    {
        $plane = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $dist = $plane->getDistanceToOrigin();
        $this->assertEquals(128, $dist);
    }

    /**
     * Verify that the location of points relative to the plane is classified correctly
     */
    public function testCalculateSideOfPointVector()
    {
        $plane = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $sideOn = $plane->calculateSideOfPointVector($this->x1);
        $this->assertEquals(Plane::SIDE_ONPLANE, $sideOn);

        $sideFront = $plane->calculateSideOfPointVector(new Math_Vector3(new Math_Tuple(array(256, 256, 256))));
        $this->assertEquals(Plane::SIDE_FRONT, $sideFront);

        $sideBack = $plane->calculateSideOfPointVector(new Math_Vector3(new Math_Tuple(array(64, 64, 64))));
        $this->assertEquals(Plane::SIDE_BACK, $sideBack);
    }

    /**
     * Verify that the intersection point with two other non-collinear planes is correctly calculated
     */
    public function testCalculateIntersectionPointWithTwoPlanes()
    {
        $p1 = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $p2 = Plane::getInstanceByThreePositionVectors($this->x2, $this->y2, $this->z2);
        $p3 = Plane::getInstanceByThreePositionVectors($this->x3, $this->y3, $this->z3);
        $int = $p1->calculateIntersectionPointWithTwoPlanes($p2, $p3);
        $this->assertEquals(new Math_Tuple(array(128,128,128)), $int->getTuple());
    }

    /**
     * Verify that an exception is thrown when trying to get the intersection point with two other identical planes
     */
    public function testIntersectionAllSame()
    {
        $this->setExpectedException('InvalidArgumentException');

        $p1 = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $p2 = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $p3 = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);
        $p1->calculateIntersectionPointWithTwoPlanes($p2, $p3);
    }

    /**
     * Verify that an exception is thrown when trying to get the intersection point with two other planes,
     * from which one is parallel to the initial one
     */
    public function testIntersectionTwoParalel()
    {
        $this->setExpectedException('InvalidArgumentException');

        $p1 = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);

        $add = new Math_Vector3(new Math_Tuple(array(0, 0, 16)));
        $x1p = new Math_Vector3(Math_VectorOp::add($this->x1, $add)->getTuple());
        $y1p = new Math_Vector3(Math_VectorOp::add($this->y1, $add)->getTuple());
        $z1p = new Math_Vector3(Math_VectorOp::add($this->z1, $add)->getTuple());
        $p2 = Plane::getInstanceByThreePositionVectors($x1p, $y1p, $z1p);

        $p3 = Plane::getInstanceByThreePositionVectors($this->x3, $this->y3, $this->z3);
        $p1->calculateIntersectionPointWithTwoPlanes($p2, $p3);
    }

    /**
     * Verify that an exception is thrown when trying to get the intersection point with two other parallel planes
     */
    public function testIntersectionAllParalel()
    {
        $this->setExpectedException('InvalidArgumentException');

        $p1 = Plane::getInstanceByThreePositionVectors($this->x1, $this->y1, $this->z1);

        $add1 = new Math_Vector3(new Math_Tuple(array(0, 0, 16)));
        $x1p = new Math_Vector3(Math_VectorOp::add($this->x1, $add1)->getTuple());
        $y1p = new Math_Vector3(Math_VectorOp::add($this->y1, $add1)->getTuple());
        $z1p = new Math_Vector3(Math_VectorOp::add($this->z1, $add1)->getTuple());
        $p2 = Plane::getInstanceByThreePositionVectors($x1p, $y1p, $z1p);

        $add2 = new Math_Vector3(new Math_Tuple(array(0, 0, 32)));
        $x1p2 = new Math_Vector3(Math_VectorOp::add($this->x1, $add2)->getTuple());
        $y1p2 = new Math_Vector3(Math_VectorOp::add($this->y1, $add2)->getTuple());
        $z1p2 = new Math_Vector3(Math_VectorOp::add($this->z1, $add2)->getTuple());
        $p3 = Plane::getInstanceByThreePositionVectors($x1p2, $y1p2, $z1p2);

        $p1->calculateIntersectionPointWithTwoPlanes($p2, $p3);
    }

}