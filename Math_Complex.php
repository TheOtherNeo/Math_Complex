<?php
/**
 * Created by TheOtherNeo
 * 24 November 2023
 * Some math functions required for the rest of the program to function.
 * These will follow the conventions of other libraries so that it can be swapped if needed.
 */

// Was unable to figure out how to install the pear libraries on HERD, so will just use this my own similar implementation for now following the same constructors and returns.
namespace Math_Complex{
    // https://www.mathsisfun.com/numbers/complex-numbers.html
    class Math_Complex {
        protected float $real;
        protected float $im;
        private string $suffix;
        /**
         * @param float $real
         * Real part of the number
         * @param float $im
         * Imaginary part of the number
         * @param string $suffix
         * Suffix to denominate the imaginary number as i or j
         */
        public function __construct( float $real = 0, float $im = 0, string $suffix = "i",)
        {
            $this->real = $real;
            $this->im = $im;
            // Only string values of "i" and "j" should be accepted.
            if ($suffix === "i" | $suffix === "j") {
                $this->suffix = $suffix;
            } else throw new \ValueError("Incorrect suffix value.");
        }

        public function __serialize(): array
        {
            return [
                'real' => $this->real,
                'im' => $this->im,
                'suffix' => $this->suffix,
            ];
        }

        public function __unserialize( array $data ): void
        {
            $this->real = $data["real"];
            $this->im = $data["im"];
            $this->suffix = $data["suffix"];
        }
        // Output a string representation of the complex number
        public function __toString(): string 
        {
            if( $this->im >= 0) {
                return (string) $this->real." + ".$this->im.$this->suffix;
            } else {
                return (string) $this->real." - ".abs($this->im).$this->suffix;
            }
        }

        /**
         * Returns the magnitude (also referred as norm) of the number.
         * https://www.houseofmath.com/encyclopedia/numbers-and-quantities/numbers/complex-numbers/introduction/what-is-the-norm-and-the-argument-of-a-complex-number
         * @return float absolute value.
         */
        public function abs():float
        {
            return sqrt( pow($this->real,2) + pow($this->im,2) );
        }

        /**
         * Returns the argument of the complex number.
         * https://www.houseofmath.com/encyclopedia/numbers-and-quantities/numbers/complex-numbers/introduction/what-is-the-norm-and-the-argument-of-a-complex-number
         * @return float theta in radians.
         */
        public function arg():float
        {
            // Check for divide by zero.
            if (!$this->real) {
                throw new \Exception('Division by zero.');
            }
            return atan($this->im / $this->real);
        }

        /**
         * Returns the imaginary part of the complex number
         */
        public function getIm():float
        {
            return $this->im;
        }

        /**
         * Returns the real part of the complex number
         */
        public function getReal():float
        {
            return $this->real;
        }
    } // end - class Math_Complex

    class Math_ComplexOp extends Math_Complex {

        /**
         * Group the real part of the complex numbers and the imaginary part of the complex numbers.
         * @param Math_Complex &$c1
         * @param Math_Complex &$c2
         * @return Math_Complex $z
         */ 
        public static function add( Math_Complex &$c1 , Math_Complex &$c2, ): Math_Complex
        {
            $z = new Math_Complex();
            
            $z->real = $c1->getReal() + $c2->getReal();
            $z->im = $c1->getIm() + $c2->getIm();
            return $z;
        }

        /** A conjugate is where we change the sign in the middle
         * @param Math_Complex &$c1
         * @return Math_Complex $z
         * */ 
        public static function conjugate( Math_Complex &$c1, ): Math_Complex
        {
            // $z = unserialize(serialize($c1));
            // Create a shallow copy of the supplied object
            $z = clone $c1;
            // Only inverse the imaginary number
            $z->im *= -1;
            return $z;  
        } // function conjugate

        /**
         * Calculate the complex number from provided polar coordinates in radius and radians
         * @param float $r
         * Radius
         * @param float $theta
         * Angle in radians
         * @return Math_Complex $z
         */
        public static function createFromPolar( float $r , float $theta, ): Math_Complex
        {
            $z = new Math_Complex();
            // Remember that theta is in radians, not degrees. So use deg2rad in normal use.
            $z->real = $r * cos($theta);
            $z->im   = $r * sin($theta);
            return $z;
        } // function createFromPolar

        /**
         * Checks if a given object is an instance of Math_Complex
         * @param mixed $c1
         * @return bool
         */
        public static function isComplex( mixed &$c1 ):bool
        {
            return $c1 instanceof Math_Complex;
        }

        // https://www.mathwarehouse.com/algebra/complex-number/multiply-complex-number.php
        /**
         * Returns the product of two complex numbers
         * @param Math_Complex &$c1
         * @param Math_Complex &$c2
         * @return Math_Complex $z
         */
        public static function mult( Math_Complex &$c1 , Math_Complex &$c2, ): Math_Complex
        {
            $z = new Math_Complex();
            // (a+bi)(c+di) = (ac−bd) + (ad+bc)i
            $z->real = ($c1->getReal() * $c2->getReal()) - ($c1->getIm() * $c2->getIm());
            $z->im = ($c1->getReal() * $c2->getIm()) + ($c1->getIm() * $c2->getReal());
            return $z;
        }
        /**
         * Multiplies a complex number by a real number
         * @param Math_Complex &$c1
         * @param float $real
         * @return Math_Complex $z
         *  */        
        public static function multReal( Math_Complex &$c1 , float $real, ): Math_Complex
        {
            $z = new Math_Complex();
            // No need for special calculations, just multiply each item with the value
            $z->real = $real * $c1->getReal();
            $z->im = $real * $c1->getIm();
            return $z;
        } //public static function multReal

        /**
         * Returns the exponentiation of a complex numbers to a real power: z = c1^(real) 
         * @param Math_Complex $c1
         * @param float $real
         * @return Math_Complex $z
         */
        public static function powReal( Math_Complex $c1 , float $real ): Math_Complex
        {
            $magnitude = pow($c1->abs(),$real);     // Multiply the magnitudes
            $angle = $c1->arg() * $real;            // Add the angles
            return Math_ComplexOp::createFromPolar($magnitude, $angle);
        } //public static function powReal
    } //class Math_ComplexOp extends Math_Complex

    /**
     * Functions that are not part of the PEAR library, but useful in relation to Excel functions
     */
    class Math_ComplexEx extends Math_ComplexOp {

        /**
         * Returns the sum of two or more complex numbers in x + yi or x + yj
         */
        public static function sum(&...$c): Math_Complex
        {
            // Object into which values will be placed.
            $z = new Math_Complex();
            // Ensuring that the start values are zero.
            $z->real = 0;
            $z->im = 0;

            // First need to seperate the real from the complex numbers
            foreach ($c as $k => $v) {
                $z->real += $v->getReal();
                $z->im += $v->getIm();
            }

            // Retrun the sum product as a new complex number
            return $z;
        } //public static function sum
    } // class Math_ComplexEx extends Math_ComplexOp
} // Namespace
