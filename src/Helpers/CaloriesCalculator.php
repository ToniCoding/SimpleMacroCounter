<?php

namespace src\Helpers;

use src\Entity\Food;

function calorieCalc(Food $food): int {
    return $food->getProtein() * 4 + $food->getCarbs() * 4 + $food->getFats() * 9 + $food->getFiber() * 4;
}
