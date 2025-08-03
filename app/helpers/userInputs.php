<?php

function normalize_input(string $input): string {
    $result = strtolower(trim(str_replace(" ", "_" , $input)));

    return $result;
}

function check_integer(string $userStr): bool {
    return filter_var($userStr, FILTER_VALIDATE_INT) !== false;
}

