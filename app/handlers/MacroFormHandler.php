<?php

class MacroFormHandler {
    private readonly DateParser $dateParser;

    public function __construct(DateParser $dateParser) {
        $this->dateParser = $dateParser;
    }

    public function handle(array $postData): Macro {
        $macro = $_POST["macro"];
        $amount = $_POST["amount"];
        // Need to obtain the goals setted by the user in a future.

        if (!$macro || !$amount) {
            throw new Exception("Macro and amount must have a value");
        }
    }
}
