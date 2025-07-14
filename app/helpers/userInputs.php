<?php

function normalize_input(string $input): string {
    $result = strtolower(trim(str_replace(" ", "_" , $input)));

    return $result;
}
