<?php 

namespace App\Services;

use App\Jobs\GenerateTransferOrderByPartnerJob;
use App\Models\Appointment;
use App\Models\Billing;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransferService
{

    public function uploadReceipt(string $uuid, $file)
    {
      $transfer = Transfer::where('uuid', $uuid)->first();
      if(!$transfer) abort(404, 'Transfer not found');
      $data = $this->putReceiptsFile($file);
      return ($transfer->update($data))? $transfer->fresh(): false;
    }

    private function putReceiptsFile($file)
    {
      $receipt = Storage::putFile('receipts', $file);
      $name    = explode('/', $receipt);
      return [
        'receipt_name' => end($name),
        'receipt_url'  => Storage::disk('receipts')->url(end($name)),
      ];
    }
  
    public function downloadReceipt(string $uuid)
    {
      $transfer = Transfer::where('uuid', $uuid)->first();
      if(!$transfer) abort(404, 'Transfer not found');
      return (Storage::disk('receipts')->download($transfer->receipt_name))? true:false;
    }

    public function store(array $data)
    {
      return Transfer::create($data);
    }

    public function generateTransfers()
    {
      $paymentOrders = DB::table('jobs')->where('queue', 'payment-order')->count();
      $transfers = Transfer::select()->join('billings', 'transfers.order_id', '=', 'billings.order_id')
      ->whereMonth('transfers.created_at', now()->month)->where('billings.model_type', Appointment::class)->get();

      $userService   = new UserService();
      $partners      = $userService->getAllPartners();
      if($paymentOrders and $transfers) return false;
      foreach($partners as $partner) GenerateTransferOrderByPartnerJob::dispatch($partner->id);
      return true;
    }

    public function generateTransferOrderByPartner(int $partnerId)
    {
      $transfers = Transfer::whereMonth('created_at', now()->month)->where('partner_id', $partnerId)->count();
      if(!$transfers) {
        $appointmentService = new AppointmentService();
        $total              = $appointmentService->getTotalValueOfAppointmentsByPartner($partnerId, now()->month, now()->year);
        $data = [
          'partner_id' => $partnerId,
          'order_id'   => $total['order_id'],
          'amount'     => $total['amount'],
          'status'     => 'pending',
          'total'      => $total['amount'],
        ];
        $this->store($data);
      }
    }

   public function search(array $data)
   {
    if(isset($data['partner_uuid'])){
      $userRepository = new UserRepository();
      $partner = $userRepository->findByUuid($data['partner_uuid']);
    }

    $repository = new TransferRepository();
    switch ($data) {
      case isset($data['start_date']) and isset($data['end_date']) and !isset($date['status']):
        $response = $repository->getTransfersByDateRange($data['start_date'], $data['end_date']);
        break;
      case (!isset($data['start_date']) and !isset($data['end_date'])) and isset($date['status']):
        $response = $repository->getTransfersByStatus($data['status']);
        break;
      case isset($data['start_date']) and isset($data['end_date']) and isset($date['status']):
        $response = $repository->getTransfersByDateRangeAndStatus($data['start_date'], $data['end_date'], $data['status']);
        break;
      case isset($data['partner_uuid']) and !isset($data['start_date']) and !isset($data['end_date']) and !isset($date['status']):
        $response = $repository->getTransfersByPartner($partner->id);
        break;
      case isset($data['partner_uuid']) and isset($data['start_date']) and isset($data['end_date']) and !isset($date['status']):
        $response = $repository->getTransfersByPartnerAndDateRange($partner->id, $data['start_date'], $data['end_date']);
        break;
      case isset($data['partner_uuid']) and (!isset($data['start_date']) and !isset($data['end_date'])) and isset($date['status']):
        $response = $repository->getTransfersByPartnerAndStatus($partner->id, $data['status']);
        break;
      case isset($data['partner_uuid']) and isset($data['start_date']) and isset($data['end_date']) and isset($date['status']):
        $response = $repository->getTransfersByPartnerAndDateRangeAndStatus($partner->id, $data['start_date'], $data['end_date'], $data['status']);
        break;
      case !isset($data['partner_uuid']) and isset($data['month']) and isset($data['year']) and !isset($date['status']):
        $response = $repository->getTransfersByMonthAndYear($data['month'], $data['year']);
        break;
      case !isset($data['partner_uuid']) and isset($data['month']) and isset($data['year']) and isset($date['status']):
        $response = $repository->getTransfersByMonthAndYearAndStatus($data['month'], $data['year'], $data['status']);
        break;
      case isset($data['partner_uuid']) and isset($data['month']) and isset($data['year']) and isset($date['status']):
        $response = $repository->getTransfersByMonthAndYearAndPartnerAndStatus($data['month'], $data['year'], $partner->id, $data['status']);
        break;
      default:
        $response = $repository->getTransfersByMonthAndYear(now()->month, now()->year);
        break;
    }

    return $response->paginate(config('settings.pagination'));
   }

}