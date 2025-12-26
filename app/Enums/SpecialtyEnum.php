<?php

namespace App\Enums;

use App\Traits\EnumFeatures;

enum SpecialtyEnum: string
{
    use EnumFeatures;

    case Cardiology = 'cardiology'; // قلب
    case Dermatology = 'dermatology'; //جلديه
    case Neurology = 'neurology'; // اعصاب
    case Pediatrics = 'pediatrics'; // اطفال
    case Orthopedics = 'orthopedics'; // عظام
    case Psychiatry = 'psychiatry'; // نفسي
    case Dentistry = 'dentistry'; // اسنان
    case Ophthalmology = 'ophthalmology'; // عيون
}
