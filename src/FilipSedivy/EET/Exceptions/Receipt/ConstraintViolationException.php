<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\Receipt;

use FilipSedivy\EET\Exceptions\RuntimeException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends RuntimeException implements ReceiptException
{
    /** @var ConstraintViolationListInterface */
    private $constraintViolationList;

    /** @var array */
    private $errors;

    /** @var array */
    private $properties;

    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        $this->constraintViolationList = $constraintViolationList;

        $errors = [];
        $properties = [];

        foreach ($constraintViolationList as $violation) {
            if ($violation instanceof ConstraintViolationInterface) {
                $properties[] = $violation->getPropertyPath();
                $errors[] = sprintf('[%s] %s', $violation->getPropertyPath(), $violation->getMessage());
            }
        }

        $this->errors = $errors;
        $this->properties = $properties;

        parent::__construct('Incorrect value in properties: ' . implode(', ', $properties), 0, null);
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
