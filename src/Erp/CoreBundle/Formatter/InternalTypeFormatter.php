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
            case "charge":
                $result = "Charge";
                break;
            case "rent_payment":
                $result = "Rent payment";
                break;
            case "late_rent_payment":
                $result = "Late rent payment";
                break;
            case "tenant_screening":
                $result = "Tenant Screening";
                break;
            case "annual_service_fee":
                $result = "Annual Service Fee";
                break;
        }

        return $result;
    }
}
