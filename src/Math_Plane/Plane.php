<?php
namespace Math_Plane;

/**
 * Class Plane
 * @package Math_Plane
 */
class Plane
{
    const EPSILON_DISTANCE = 3.7539393815679E-9;
    
    const SIDE_ONPLANE = 1;
    const SIDE_FRONT = 2;
    const SIDE_BACK = 3;

    /**
     * @var \Math_Vector3
     */
    private $normalVectorNormalized = null;

    /**
     * @var float
     */
    private $distanceToOrigin = null;

    /**
     *
     */
    private function __construct()
    {
    }

    /**
     *
     * @param \Math_Vector3 $v1
     * @param \Math_Vector3 $v2
     * @param \Math_Vector3 $v3
     * @return Plane
     */
    public static function getInstanceByThreePositionVectors(\Math_Vector3 $v1, \Math_Vector3 $v2, \Math_Vector3 $v3)
    {
        $instance = new self;

        $instance->normalVectorNormalized = self::calculateNormalizedNormalVector($v1, $v2, $v3);
        $instance->distanceToOrigin = self::calculateOriginDistance($instance->normalVectorNormalized, $v2);
        return $instance;
    }

    /**
     * checked and working
     *
     * @param \Math_Vector3 $a
     * @param \Math_Vector3 $b
     * @param \Math_Vector3 $c
     * @return object
     * @throws InvalidArgumentException
     */
    private static function calculateNormalizedNormalVector(\Math_Vector3 $a, \Math_Vector3 $b, \Math_Vector3 $c)
    {
        $ba = new \Math_Vector3(\Math_VectorOp::substract($a, $b)->getTuple());
        $bc = new \Math_Vector3(\Math_VectorOp::substract($c, $b)->getTuple());
        $n = \Math_VectorOp::crossProduct($ba, $bc);
        if ($n->length() == 0) {
            throw new \InvalidArgumentException('all three vectors lie on the same line, they do not define a plane');
        }
        $n->normalize();
        return $n;
    }
    
    /**
     * Its important to be clear about on which side of the equation D is intended to be:
     *  - same side as normal vector: dotproduct has to be multiplied with -1
     *  - opposite side of normal vector: dotproduct is fine
     *
     * @return float
     */
    private static function calculateOriginDistance(\Math_Vector3 $n, \Math_Vector3 $a)
    {
        return \Math_VectorOp::dotProduct($n, $a);
    }

    /**
     * @param \Math_Vector3 $v
     * @return float
     */
    private function calculateDistanceToPointVector(\Math_Vector3 $v)
    {
        return \Math_VectorOp::dotProduct($this->normalVectorNormalized, $v) - $this->distanceToOrigin;
    }

    /**
     *
     * @param \Math_Vector3 $v
     * @return int
     */
    public function calculateSideOfPointVector(\Math_Vector3 $v)
    {
        $distance = $this->calculateDistanceToPointVector($v);
        if (-self::EPSILON_DISTANCE > $distance) {
            return self::SIDE_BACK;
        } elseif (self::EPSILON_DISTANCE < $distance) {
            if (false !== strpos((string)$distance, 'E-')) {
                trigger_error(sprintf('Found very small distance (%s) between point and plane, assuming point outside plane(=skipping), might be wrong', (string)$distance), E_USER_WARNING);
            }
            return self::SIDE_FRONT;
        } else {
            return self::SIDE_ONPLANE;
        }
    }

    /**
     * Calculates the intersection point bnetween the current and two other planes
     *
     * @param Plane $plane1
     * @param Plane $plane2
     * @return bool|\Math_Vector3
     * @throws InvalidArgumentException
     */
    public function calculateIntersectionPointWithTwoPlanes(self $plane1, self $plane2)
    {
        $n1 = $this->normalVectorNormalized;
        $n2 = $plane1->normalVectorNormalized;
        $n3 = $plane2->normalVectorNormalized;
        $d1 = $this->distanceToOrigin;
        $d2 = $plane1->distanceToOrigin;
        $d3 = $plane2->distanceToOrigin;
        $n2_x_n3 = \Math_VectorOp::crossProduct($n2, $n3);
        $n3_x_n1 = \Math_VectorOp::crossProduct($n3, $n1);
        $n1_x_n2 = \Math_VectorOp::crossProduct($n1, $n2);
        $p = new \Math_Vector3(\Math_VectorOp::add(\Math_VectorOp::add(\Math_VectorOp::scale($d1, $n2_x_n3), \Math_VectorOp::scale($d2, $n3_x_n1)), \Math_VectorOp::scale($d3, $n1_x_n2))->getTuple());
        $divisor = \Math_VectorOp::dotProduct($n1, $n2_x_n3);
        if ((float)0 === $divisor) {
            throw new \InvalidArgumentException('no point-intersection');
        }
        $p->scale(1 / $divisor);
        return $p;
    }

    /**
     * @return null
     */
    public function getNormalVectorNormalized()
    {
        return $this->normalVectorNormalized;
    }

    /**
     * @return null
     */
    public function getDistanceToOrigin()
    {
        return $this->distanceToOrigin;
    }
}
