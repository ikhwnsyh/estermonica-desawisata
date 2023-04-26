<?php

namespace App\Constants;

class MidtransStatusConstant {
    const AUTHORIZE = 1;
    const CAPTURE = 2;
    const SETTLEMENT = 3;
    const DENY = 4;
    const PENDING = 5;
    const CANCEL = 6;
    const REFUND = 7;
    const PARTIAL_REFUND = 8;
    const CHARGEBACK = 9;
    const PARTIAL_CHARGEBACK = 10;
    const EXPIRE = 11;
    const FAILURE = 12;
}
