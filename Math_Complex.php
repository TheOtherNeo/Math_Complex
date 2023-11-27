<?php
/**
 * Created by Neil Bezuidenhout
 * 24 November 2023
 * Some math functions required for the rest of the program to function.
 * These will follow the conventions of other libraries so that it can be swapped if needed.
 */

// Was unable to figure out how to install the pear libraries on HERD, so will just use this my own similar implementation for now following the same constructors and returns.
namespace Math_Complex{
    // https://www.mathsisfun.com/numbers/complex-numbers.html
    class Math_Complex {
        public float $real;
        public float $im;
        private string $suffix;
        /**
         * @param float $real
         * Real part of the number
         * @param float $im
         * Imaginary part of the number
         * @param string $suffix
         * Suffix to denominate the imaginary number as i or j
         */
        public function __construct(?float $real = 0, ?float $im = 0, ?string $suffix = "i",)
        {
            $this->real = $real;
            $this->im = $im;
            // Only string values of "i" and "j" should be accepted.
            if ($suffix === "i" | $suffix === "j") {
                $this->suffix = $suffix;
            } else throw new \Exception("Incorrect suffix value.");
        }

        public function __serialize(): array
        {
            return [
                'real' => $this->real,
                'im' => $this->im,
                'suffix' => $this->suffix,
            ];
        }

        public function __unserialize(?array $data): void
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
    } // end - class Math_Complex

    class Math_ComplexOp extends Math_Complex {

        /**
         * Group the real part of the complex numbers and the imaginary part of the complex numbers.
         * @param Math_Complex &$c1
         * @param Math_Complex &$c2
         * @return Math_Complex $z
         */ 
        public static function add( ?Math_Complex &$c1 , ?Math_Complex &$c2, ): Math_Complex
        {
            $z = new Math_Complex();
            
            $z->real = $c1->real + $c2->real;
            $z->im = $c1->im + $c2->im;
            return $z;
        }

        /** A conjugate is where we change the sign in the middle
         * @param Math_Complex &$c1
         * @return Math_Complex $z
         * */ 
        public static function conjugate( ?Math_Complex &$c1, ): Math_Complex
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
        public static function createFromPolar( ?float $r , ?float $theta, ): Math_Complex
        {
            $z = new Math_Complex();
            // Remember that theta is in radians, not degrees. So use deg2rad in normal use.
            $z->real = $r * cos($theta);
            $z->im   = $r * sin($theta);
            return $z;
        } // function createFromPolar


        // https://www.mathwarehouse.com/algebra/complex-number/multiply-complex-number.php
        /**
         * Returns the product of two complex numbers
         * @param Math_Complex &$c1
         * @param Math_Complex &$c2
         * @return Math_Complex $z
         */
        public static function mult( ?Math_Complex &$c1 , ?Math_Complex &$c2, ): Math_Complex
        {
            $z = new Math_Complex();
            // (a+bi)(c+di) = (ac−bd) + (ad+bc)i
            $z->real = ($c1->real * $c2->real) - ($c1->im * $c2->im);
            $z->im = ($c1->real * $c2->im) + ($c1->im * $c2->real);
            return $z;
        }
        /**
         * Multiplies a complex number by a real number
         * @param Math_Complex &$c1
         * @param float $real
         * @return Math_Complex $z
         *  */        
        public static function multReal( ?Math_Complex &$c1 , ?float $real, ): Math_Complex
        {
            $z = new Math_Complex();
            // No need for special calculations, just multiply each item with the value
            $z->real = $real * $c1->real;
            $z->im = $real * $c1->im;
            return $z;
        }
    }
} // Namespace
