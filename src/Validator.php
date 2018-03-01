<?php
/**
 * Validator.php
 */

namespace Es;


class Validator
{
    private $errors = array();

    private static $typeMethodMap = array(
        'string' => 'checkString',
        'integer' => 'checkNumeric',
        'boolean' => 'checkBoolean',
    );

    private $constraints;

    public function __construct($validations)
    {
        $this->constraints = $validations;
    }

    public function validate($data)
    {
        if (!empty($this->constraints['required'])) {
            $this->checkRequirements($data);
        }

        // todo 参数类型 and pattern校验

        if ($this->errors) {
            $message = sprintf(
                "Found %d error%s while validating the input: ",
                count($this->errors),
                count($this->errors) > 1 ? 's' : ''
            );
            foreach ($this->errors as $errorInfo) {
                $funcName = $errorInfo['function'];
                $errorMsg = $errorInfo['message'];
                $message .= "\n" . ucfirst($funcName) . ' : ' . $errorMsg . '.';
            }
            throw new \InvalidArgumentException($message);
        }
    }

    public function checkRequirements($data)
    {
        $missingArgs = array_diff($this->constraints['required'], array_keys($data));
        foreach ($missingArgs as $missingArg) {
            $this->addError(__FUNCTION__,  "$missingArg is missing and is a required parameter");
        }
    }

    private function checkNumeric($value)
    {
        if (!is_numeric($value)) {
            $this->addError(__FUNCTION__, 'must be numeric. Found '
                . $this->describeType($value));
            return;
        }
    }

    private function checkString($value)
    {

    }

    private function checkBoolean()
    {

    }

    private function addError($funcName, $message)
    {
        $this->errors[] = array('function' => $funcName, 'message' => $message);
    }
    
    private function describeType($input)
    {
        switch (gettype($input)) {
            case 'object':
                return 'object(' . get_class($input) . ')';
            case 'array':
                return 'array(' . count($input) . ')';
            default:
                ob_start();
                var_dump($input);
                // normalize float vs double
                return str_replace('double(', 'float(', rtrim(ob_get_clean()));
        }
    }
}