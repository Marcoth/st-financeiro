<?php
/**
 *
 * adicionando funções pertinentes que vão ser reutilizadas
 */

/**
 * data no formato do banco
 */
function dateParse($date)
{
    /**
     * dd/mm/yyyy -> yyyy-mm-dd
     */

    $dateArray = explode('/', $date);
    /**
     * [yyyy,mm,dd ]
     */
    $dateArray = array_reverse($dateArray);
    return implode('-', $dateArray);
}

/**
 *
 * valor no formato do banco
 */
function numberParse($number)
{
    /**
     * 1.999,50 -> 1000.50
     */
    $newNumber = str_replace('.', '', $number);
    $newNumber = str_replace(',', '.', $newNumber);
    return $newNumber;
}
