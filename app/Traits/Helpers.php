<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Helpers
{

  public function getDayOfWeek($date)
  {
    $timestamp = strtotime($date);
    $day_of_week = date('w', $timestamp);

    switch ($day_of_week) {
      case 0:
          return 'DOM';
      case 1:
          return 'SEG';
      case 2:
          return 'TER';
      case 3:
          return 'QUA';
      case 4:
          return 'QUI';
      case 5:
          return 'SEX';
      case 6:
          return 'SÁB';
      default:
          return 'Dia inválido';
    }
  }

  function convertDateToText($date,$withoutText = false){
    $parts = explode('-', $date);
    $year = $parts[0];
    $month = $parts[1];
    $day = $parts[2];

    $monthText = "";
    switch ($month) {
        case 1:
            $monthText = "JAN";
            break;
        case 2:
            $monthText = "FEV";
            break;
        case 3:
            $monthText = "MAR";
            break;
        case 4:
            $monthText = "ABR";
            break;
        case 5:
            $monthText = "MAI";
            break;
        case 6:
            $monthText = "JUN";
            break;
        case 7:
            $monthText = "JUL";
            break;
        case 8:
            $monthText = "AGO";
            break;
        case 9:
            $monthText = "SET";
            break;
        case 10:
            $monthText = "OUT";
            break;
        case 11:
            $monthText = "NOV";
            break;
        case 12:
            $monthText = "DEZ";
            break;
        default:
            $monthText = "mês inválido";
            break;
    }

    $dayName = $this->getDayOfWeek($date);
    if ($withoutText == true){
      return $day . ' de ' . $monthText . ' de ' . $year;
    } 

    return $dayName . ', ' . $day . ' de ' . $monthText . ' de ' . $year;
  }

  
  protected function getIdByUuid(string $uuid, $repository): int
  {
    $repository = new $repository;
    $data = $repository->findByUuid($uuid);
    if(!$data) abort(404);
    return $data->id;
  }

  public function pagarmeDateDecode($date)
  {
    if(is_null($date)) return null;
    $date = Carbon::parse($date);
    return $date->format('Y-m-d');
  }

  public function pagarmeDateEncode(string $date): string
  {
    $date = Carbon::parse($date);
    return $date->format('Y-m-d');
  }

  /**
   * 
   *
   * @param float|int $value
   * @param string|null $currency [BRL, USD, EUR]
   * @return void
   */
  public function formatMoney($value, $currency = null, $toInteger = false)
  {
    if($toInteger) {
      $value = str_replace('.', '', $value);
      $value = str_replace(',', '.', $value);
      return $value;
    }
    
    switch ($currency) {
      case 'BRL':
        return "R$ ".number_format($value, 2, ',', '.');
        break;
      
      case 'USD':
        return "$ ".number_format($value, 2, '.', ',');
        break;

      case 'EUR':
        return "€ ".number_format($value, 2, ',', '.');
        break;

      default:
        return number_format($value, 2, '', '');
        break;
    }
  }
}