<?php

namespace Erp\CoreBundle\Formatter;

class InternalTypeFormatter
{
    public function format($value)
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        $result = '';
        switch ($value) {
            case "late_rent_payment":
                $result = "Late rent payment";
                break;
            case "fee":
                $result = "Fee";
                break;
            case "rent_payment":
                $result = "Rent payment";
                break;
        }

        return $result;
    }
}
