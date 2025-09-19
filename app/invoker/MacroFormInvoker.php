<?php

class MacroFormInvoker {
    private MacroFormHandler $macroFormHandler;
    private MacroContainer $macroContainer;
    private CombinedMacroController $combinedMacroController;
    private Logger $logger;

    public function __construct(Container $globalContainer, MacroContainer $macroContainer, MacroFormHandler $macroFormHandler) {
        $this->macroFormHandler = $macroFormHandler;
        $this->macroController = $macroContainer->getMacroController();
        $this->logger = $globalContainer->getService('logger');
    }
}
