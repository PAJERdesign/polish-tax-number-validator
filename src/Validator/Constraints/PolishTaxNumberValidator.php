<?php

declare(strict_types=1);

namespace PAJERdesign\PolishTaxNumberValidator\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Robert Pajer
 */
class PolishTaxNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PolishTaxNumber) {
            throw new UnexpectedTypeException($constraint, PolishTaxNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $nip = preg_replace('/^PL/', '', $value);

        if (!ctype_digit($nip)) {
            $this->context->buildViolation($constraint->invalidFormatMessage)
                ->setCode(PolishTaxNumber::INVALID_FORMAT_ERROR)
                ->addViolation();

            return;
        }

        if (strlen($nip) < 10) {
            $this->context->buildViolation($constraint->tooShortdMessage)
                ->setCode(PolishTaxNumber::TOO_SHORT_ERROR)
                ->addViolation();

            return;
        }

        if (strlen($nip) > 10) {
            $this->context->buildViolation($constraint->tooLongdMessage)
                ->setCode(PolishTaxNumber::TOO_LONG_ERROR)
                ->addViolation();

            return;
        }

        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];

        $sum = 0;
        for($i = 0; $i < 9; $i++)
            $sum += $nip[$i] * $weights[$i];

        if (($sum % 11) != $nip[9]) {
            $this->context->buildViolation($constraint->checkSumFailedMessage)
                ->setCode(PolishTaxNumber::CHECKSUM_FAILED_ERROR)
                ->addViolation();
        }
    }
}
