<?php

namespace App\Helpers;

use App\Entity\{Food, Products};

class CalorieCalculator {
    public function calorieCalc(Food|Products $food): int {
        return $food->getProtein() * 4 + $food->getCarbs() * 4 + $food->getFats() * 9 + $food->getFiber() * 4;
    }
}
