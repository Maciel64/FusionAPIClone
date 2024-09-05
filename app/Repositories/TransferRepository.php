<?php 

namespace App\Repositories;

use App\Models\Transfer;
use Illuminate\Support\Facades\Date;

class TransferRepository extends BaseRepository
{
    public function __construct()
    {
      parent::__construct(Transfer::class);
    }

    public function getTransfersByMonthAndYear(int $month, int $year)
    {
      return $this->model->whereMonth('created_at', $month)->whereYear('created_at', $year);
    }

    public function getTransfersByMonthAndYearAndPartner(int $month, int $year)
    {
      return $this->model
              ->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->where('partner_id', auth()->user()->id);
    }

    public function getTransfersByMonthAndYearAndStatus(int $month, int $year, string $status)
    {
      return $this->model
              ->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->where('status', $status);
    }

    public function getTransfersByMonthAndYearAndPartnerAndStatus(int $month, int $year, int $partnerId, string $status)
    {
      return $this->model
              ->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->where('partner_id', $partnerId)
              ->where('status', $status);
    }

    public function getTransfersByDateRange(string $startDate, string $endDate)
    {
      $dateInit = Date::parse($startDate)->startOfDay();
      $dateEnd  = Date::parse($endDate)->endOfDay();
      return $this->model->whereBetween('created_at', [$dateInit, $dateEnd]);
    }

    public function getTransfersByDateRangeAndStatus(string $startDate, string $endDate, string $status)
    {
      $data = $this->getTransfersByDateRange($startDate, $endDate);
      return $data->where('status', $status);
    }

    public function getTransfersByStatus($status)
    {
      return $this->model->where('status', $status);
    }

    public function getTransfersByPartner(int $partnerId)
    {
      return $this->model->where('partner_id', $partnerId);
    }
    
    public function getTransfersByPartnerAndStatus(int $partnerId, string $status)
    {
      return $this->model->where('partner_id', $partnerId)->where('status', $status);
    }

    public function getTransfersByPartnerAndDateRange(int $partnerId, string $startDate, string $endDate)
    {
      $data = $this->getTransfersByDateRange($startDate, $endDate);
      return $data->where('partner_id', $partnerId);
    }

    public function getTransfersByPartnerAndDateRangeAndStatus(int $partnerId, string $startDate, string $endDate, string $status)
    {
      $data = $this->getTransfersByPartnerAndDateRange($partnerId, $startDate, $endDate);
      return $data->where('status', $status);
    }



}