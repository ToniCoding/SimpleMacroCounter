<?php

namespace src\Helpers;

use src\Entity\Food;
use src\Entity\Products;

function calorieCalc(Food|Products $food): int {
    return $food->getProtein() * 4 + $food->getCarbs() * 4 + $food->getFats() * 9 + $food->getFiber() * 4;
}
