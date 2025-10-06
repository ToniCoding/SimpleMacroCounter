<?php

namespace App\Invoker;

use Config\Container;
use App\Controller\CombinedMacroController;
use App\Logging\Logger;
use App\Handlers\MacroFormHandler;
use App\Containers\MacroContainer;

class MacroFormInvoker {
    private MacroFormHandler $macroFormHandler;
    private MacroContainer $macroContainer;
    private CombinedMacroController $combinedMacroController;
    private Logger $logger;

    public function __construct(Container $globalContainer, MacroContainer $macroContainer, MacroFormHandler $macroFormHandler) {
        $this->macroFormHandler = $macroFormHandler;
        $this->logger = $globalContainer->getService('logger');
    }
}
