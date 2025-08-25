<?php

namespace App\Enums;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case CREDIT_CARD = 'credit_card';
    case INVESTMENT = 'investment';
}