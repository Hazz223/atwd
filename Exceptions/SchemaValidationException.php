<?php

/**
 * Description of SchemaValidationError
 *
 * @author hlp2-winser
 */
class SchemaValidationError extends Exception {

    public function __construct($message) {

        parent::__construct($message, 603);
    }

    public function __toString() {
        return "[" . $this->code . "]: " . $this->message;
    }
}
?>
