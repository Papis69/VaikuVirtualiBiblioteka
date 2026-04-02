<?php
// Vardų erdvė – pagrindinė aplikacijos vardų erdvė
namespace App;

// Importuojame MicroKernelTrait – sutrumpintas Symfony branduolys (atlieka konfigūraciją automatiškai)
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
// Importuojame BaseKernel – Symfony bazinė branduolio klasė
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Aplikacijos branduolys (Kernel) – tai Symfony aplikacijos širdis.
 * Jis įkelia visus bundle'us, konfigūraciją ir maršrutus.
 * MicroKernelTrait supaprastina konfigūraciją – viską atlieka automatiškai
 * pagal config/ ir src/ katalogų struktūrą.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait; // Naudojame sutrumpintą branduolio konfigūraciją
}
