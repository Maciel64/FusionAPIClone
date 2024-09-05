<?php

namespace App\Services;

use App\Facades\AppointmentFacade;
use App\Facades\RoomFacade;
use App\Services\BillingService;
use App\Models\Appointment;
use App\Models\PagarMeLog;
use App\Repositories\AppointmentRepository;
use App\Repositories\BlockedScheduleRepository;
use App\Repositories\RoomRepository;
use App\Repositories\BillingRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewAppointment;
use App\Mail\NewAppointments;
use App\Mail\NewAppointmentsPartner;
use App\Mail\GenericMail;
use DateTime;
use App\Traits\Helpers;
use Illuminate\Support\Facades\Date;

class AppointmentService
{
  use Helpers;

  public  function getSalesTurnoverData($dateInit, $dateEnd){
    $appointmentRepository = new AppointmentRepository();
    $billingRepository = new BillingRepository();
    $billingValue = $billingRepository->getBillingsValue($dateInit, $dateEnd);
    $billingCountByDate = $billingRepository->getBillingCountByDate($dateInit, $dateEnd);
    $totalOfAppointments = $appointmentRepository->getTotalOfAppointments($dateInit, $dateEnd);
    $totalOfCanceled = $appointmentRepository->getTotalOfCanceled($dateInit, $dateEnd);
    $billingValuePerDay = $billingRepository->getBillingValuePerDay($dateInit, $dateEnd);
    $totalOfCanceledValue = $appointmentRepository->getTotalOfCanceledValue($dateInit, $dateEnd);

    $data = [
      'billingValue' => $billingValue,
      'billingCountByDate' => $billingCountByDate,
      'totalOfAppointments' => $totalOfAppointments,
      'totalOfCandeled' => $totalOfCanceled,
      'billingValuePerDay' => $billingValuePerDay,
      'totalOfCanceledValue' => $totalOfCanceledValue


    ];
    
    return $data;

  }

  public function listAll($request)
  {
    $repository = new AppointmentRepository();
    $appointments = $repository->indexOrdered($request);
    $response = [];

    $total_appoitnments_value_sum = 0;
    $total_appoitnments_time_sum = 0;
    foreach($appointments as $appointment){
      $total_appoitnments_value_sum += $appointment->value_total;
      $total_appoitnments_time_sum += $appointment->time_total / 60;
    }
    
    $response['date_init'] = $request->dateInit;
    $response['date_end'] = $request->dateEnd;
    $response['total_appointments'] = count($appointments);
    $response['total_appoitnments_value_sum'] = $total_appoitnments_value_sum;
    $response['total_appoitnments_time_sum'] = $total_appoitnments_time_sum ;
    $response['appointments'] = $appointments;
    
    return $response;
  }  


  public function storeBulk(array $data)
  {
    DB::beginTransaction();
    try {

      $user = (new UserRepository())->findByUuid($data['customer_uuid']);
      $room = (new RoomRepository())->findByUuid($data['room_uuid']);
      $appointmentRepository = new AppointmentRepository();
      
      $appointments = $dataArray = [];
      foreach ($data['appointments'] as $appointment) {
        $appointment['time_end'] = Carbon::parse($appointment['time_init'])->addHour(); // forçar que o appointment tenha 1 hora.
        $this->checkAppointmentTimeIfAvailable($room->opening_hours, $appointment['time_init'], $appointment['time_end']);
        $this->checkIfThereIsConflict($appointmentRepository, $room->id, $appointment['time_init'], $appointment['time_end']);
        $timeTotal      = $this->calcDiffTime($appointment['time_init'], $appointment['time_end']);
        $pricePerMinute = $room->price_per_minute;
        $priceTotal     = $pricePerMinute; // pricePerMinute agora é o preço de 1 agendamento de 1 hora.
        $data = [
          'time_total'       => $timeTotal,
          'value_per_minute' => $pricePerMinute,
          'value_total'      => $priceTotal,
          'customer_id'      => $user->id,
          'room_id'          => $room->id,
          'status'           => Auth::user()->role_name == 'customer' ? 'paid' : 'pending',
          'time_init'        => Carbon::parse($appointment['time_init']),
          'time_end'         => Carbon::parse($appointment['time_end']),
          'schedule_id'      => $room->schedule_id,
        ];
        $dataArray[] = $data;
        Log::debug('AppointmentService::storeBulk - Creating appointment', $data);
        $appointments[] = $appointmentRepository->create($data);
      }
      if(Auth::user()->role_name == 'customer')
      {
        foreach($appointments as $appointment){
          $restrictionHour = $room->appointment_restriction_hour; 
          $currentDateTime = Carbon::now();
          $timeInitToconvert = $appointment->time_init->copy();
          $timeInitWithRestricHours = $timeInitToconvert->subHours($restrictionHour);
          if ($appointment->time_init < $currentDateTime){ 
            throw ValidationException::withMessages(['error' => 'Não é possível agendamento retroativo', 'details' => $appointment]);
          }
          if ($timeInitWithRestricHours < $currentDateTime){       
            throw ValidationException::withMessages(['error' => 'Não é possível agendar um horário anterior ao prazo de reserva, para essa sala o agendamento tem que ser feito com até '. $restrictionHour . 'h de antecedência.', 'details' => $appointment]);
          }
        }       
        
        $billingService = new BillingService();
        $payload = $billingService->assembleAppointmentPayload($dataArray, $user, $room, true); //salvando cópia do payload
        $order = $billingService->generateAppointmentOrder($dataArray, $user, $room, true);
        
        if(isset($order['status']) && $order['status'] == 'paid'){
          //Tá funcionando
        } else {
          if(isset($order['charges'][0]['last_transaction']['gateway_response']['errors'][0]['message'])){
            $message = $order['charges'][0]['last_transaction']['gateway_response']['errors'][0]['message'];
          }else{
            $message = 'Erro ao processar pagamento '.json_encode($order);
          }
          DB::rollBack();
          
          PagarMeLog::create([
            'user_id' => $user->id,
            'logged_user_name' =>Auth()->user()->name,
            'action' => 'generateAppointmentOrder - rollBack',
            'request' => json_encode($payload),
            'response' => $order,
          ]);

          throw ValidationException::withMessages(['error' => $message, 'details' => $order]);
        }
        $billing = $billingService->store($user->id, 'Appointment', $appointments[0]->id, $order);        

        PagarMeLog::create([
          'user_id' => $user->id,
          'logged_user_name' =>Auth()->user()->name,
          'action' => 'generateAppointmentOrder - commit',
          'request' => json_encode($payload),
          'response' => $order,
        ]);

      }
      DB::commit();      
      $emailAppointments = [];
      $valueTotalAppointments = 0;
      foreach($appointments as $appointment){
        $valueTotalAppointments = $valueTotalAppointments + $appointment->value_total;
        $emailAppointment = new \stdClass;

        $time_init = $appointment->time_init;
        $time_end = $appointment->time_end;
        list($date, $hour) = explode(' ', $time_init); 
        list($dateEnd, $hourEnd) = explode(' ', $time_end); 
        $formatedDay = $this->convertDateToText($date);
        
        $emailAppointment->formatedDay = $formatedDay;
        $emailAppointment->formattedTime = date('d/m/Y', strtotime($date));
        $emailAppointment->formattedDateInit = date('H:i', strtotime($hour));
        $emailAppointment->formattedDateEnd = date('H:i', strtotime($hourEnd));
        $emailAppointment->value_total = $this->formatMoney($appointment->value_total,'BRL');

        $rawDate = str_replace('-', '', $date);
        $rawDateEnd = str_replace('-', '', $dateEnd);
        $rawHourInit = str_replace(':', '', $hour);  
        $rawHourEnd = str_replace(':', '', $hourEnd);  
        $emailAppointment->googleCalendar = "https://calendar.google.com/calendar/u/0/r/eventedit?text=Reserva Fusion - {$appointment->room->name}, {$appointment->room->number} - {$appointment->room->coworking->name}&dates={$rawDate}T{$rawHourInit}/{$rawDateEnd}T{$rawHourEnd}&location={$appointment->room->coworking->name}";
        
        $emailAppointment->appointment = $appointment;
        $emailAppointments[] = $emailAppointment;
      }

      $addressToGoogle = $room->address->line_1 . ', ' . $room->address->line_2;
      $encodedAddress = str_replace(' ', '+', $addressToGoogle);
      $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query={$encodedAddress}"; 



      
      $role = auth()->user()->role_name;
      $valueTotalAppointments = $this->formatMoney($valueTotalAppointments,'BRL');  
      $parts = explode(' ', $user->name);
      $firstName = $parts[0];
      $uniqueValue = $this->formatMoney($emailAppointments[0]->appointment->value_total,'BRL');

      if (isset($room->photo->url) && !is_null($room->photo->url)) { 
        $display = "flex";
        $photo = $room->photo->url;
      } else{
        $display = "none";
        $photo = 'imagem.png';
      }

      NewAppointments::sendMail(
        $user->email,
        ['marialuisa@fusionclinic.com.br'],
        'Fusion Clinic - Confirmação de agendamento',
        'emails.newAppointments',
        compact('emailAppointments', 'room', 'display', 'photo', 'valueTotalAppointments', 'user','firstName', 'uniqueValue','role', 'addressToGoogle'),
      );  
      
      $partner = (new UserRepository())->find($room->coworking->user_id);
      $parts = explode(' ', $partner->name);
      $partnerFirstName = $parts[0];
      
      if(isset($user->photo->url)){
        $customerPhoto = $user->photo->url;
      } else {
        $customerPhoto = 'https://previews.123rf.com/images/hugok1000/hugok10001905/hugok1000190500198/123291745-ilustra%C3%A7%C3%A3o-padr%C3%A3o-do-avatar-do-perfil-em-azul-e-branco-nenhuma-pessoa.jpg';
      }
      
      NewAppointmentsPartner::sendMail(
        $partner->email,
        ['marialuisa@fusionclinic.com.br'],
        'Fusion Clinic - Confirmação de agendamento',
        'emails.newAppointments',
        compact('emailAppointments', 'room', 'display', 'photo', 'valueTotalAppointments', 'partner','user','partnerFirstName', 'uniqueValue','customerPhoto'),
      );
      return $appointments;

    } catch (\Throwable $th) {
      DB::rollBack();
      throw ValidationException::withMessages(['error' => $th->getMessage()]);
    }
  }

  public function store(array $data)
  {
    $user = (new UserRepository())->findByUuid($data['customer_uuid']);
    $room = (new RoomRepository())->findByUuid($data['room_uuid']);
    $appointmentRepository = new AppointmentRepository();
    $data['time_end'] = Carbon::parse($data['time_init'])->addHour(); // forçar que o appointment tenha 1 hora.
    $this->checkAppointmentTimeIfAvailable($room->opening_hours, $data['time_init'], $data['time_end']);
    $this->checkIfThereIsConflict($appointmentRepository, $room->id, $data['time_init'], $data['time_end']);

    $timeTotal      = $this->calcDiffTime($data['time_init'], $data['time_end']);
    $pricePerMinute = $room->price_per_minute;
    $priceTotal     = $pricePerMinute; // pricePerMinute agora é o preço de 1 agendamento de 1 hora.

    $data = [
      'patient_name'     => $data['patient_name'],
      'patient_phone'    => $data['patient_phone'],
      'time_total'       => $timeTotal,
      'value_per_minute' => $pricePerMinute,
      'value_total'      => $priceTotal,
      'customer_id'      => $user->id,
      'room_id'          => $room->id,
      'status'           => 'pending',
      'time_init'        => Carbon::parse($data['time_init']),
      'time_end'         => Carbon::parse($data['time_end']),
      'schedule_id'      => $room->schedule_id,
    ];

    
    $billingService = new BillingService();
    $order = $billingService->generateAppointmentOrder($data, $user, $room);
    if($order['status'] == 'paid'){
      $data['status'] = 'paid';
    } else {
      $message = $order['charges'][0]['last_transaction']['gateway_response']['errors'][0]['message'];
      throw ValidationException::withMessages(['error' => $message]);
    }

    $appointment = $appointmentRepository->create($data);

    $billing = $billingService->store($user->id, 'Appointment', $appointment->id, $order);

    $appointment->billing = $billing; // Só para o retorno

    return $appointment;
  }

  private function checkAppointmentTimeIfAvailable($openingHours, $timeInit, $timeEnd)
  {
    $config   = $this->getConfigDayOfWeek($timeInit, $openingHours);
    $timeInit = Carbon::parse($timeInit);
    $timeEnd  = Carbon::parse($timeEnd);
   
    if (!($timeInit->between($config->opening, $config->closing) && $timeEnd->between($config->opening, $config->closing))){
      $message = "The appointment time is not within the configured period of {$config->opening->format('H:i')} to {$config->closing->format('H:i')}";
      throw ValidationException::withMessages(['error' => $message]);
    }
  }

  private function getConfigDayOfWeek($date, $openingHours)
  {
    $dayOfWeek = config('settings.days_of_week.'.Carbon::parse($date)->dayOfWeek);
    foreach ($openingHours as $key => $value) {
      if($value['day_of_week'] == $dayOfWeek) {
        return (object)[
          'opening' => $this->dateParse($date, $value->opening),
          'closing' => $this->dateParse($date, $value->closing)
        ];
      }
    }
  }

  private function dateParse($date, $time)
  {
    if(strpos($time, ' ')) {
      $time = explode(' ', $time);
      $time = explode(':', $time[1]);
    }else{
      $time = explode(':', $time);
    }

    return Carbon::parse($date)->hour($time[0])->minute($time[1]);
  }

  private function calcDiffTime($timeInit, $timeEnd)
  {
    $timeInit = Carbon::parse($timeInit);
    $timeEnd = Carbon::parse($timeEnd);
    return $timeEnd->diffInMinutes($timeInit);
  }

  private function checkIfThereIsConflict(AppointmentRepository $appointmentRepository, int $roomId, string $timeInit, string $timeEnd)
  {
    $date = Carbon::parse($timeInit)->format('Y-m-d');
    $timeInit = Carbon::parse($timeInit);
    $timeEnd  = Carbon::parse($timeEnd);

    $availabilities = $this->getRoomAvailability($roomId, $timeInit);
    $check = false;
    foreach ($availabilities as $availability)  { 
      $opening  = $this->dateParse($date, $availability->opening);
      $closing  = $this->dateParse($date, $availability->closing);
      if($timeInit->between($opening, $closing) && $timeEnd->between($opening, $closing)) {
        $check = true;
        break;
      }
    } 

    if(!$check) {
      $message = "The appointment time is not within the configured period";
      throw ValidationException::withMessages(['error' => $message]);
    }
  }

  public function getRoomAvailabilityBulk($roomId, array $dateBulk)
  {
    $availabilities = [];
    foreach ($dateBulk as $date) {
      $availabilities[] = [
        'date'         => $date,
        'availability' => $this->getRoomAvailability($roomId, $date),
      ];
    }

    return $availabilities;
  }

  public function getRoomAvailability($roomId, $date)
  {
    $room = RoomFacade::find($roomId);
    if(count($room->opening_hours) == 0) return false; //Opening_hour do coworking
    $config = $this->getConfigDayOfWeek($date, $room->opening_hours); //Horário de abertura e fechamento do dia da semana enviado na verificação.
    $appointments = AppointmentFacade::getAppointmentsByRoomIdAndDateWithoutCanceled($roomId, $date)->toArray();
    
    $operating_hours = $this->getConfiguratedOperatingHours($room, $date);      
    $mergedArray = $this->mergeArraysWithSameTime($appointments, $operating_hours); 

    $blockedRepository = new BlockedScheduleRepository();
    $blocked_schedules = $blockedRepository->getBlockedsByRoomIdAndDate($roomId, $date)->toArray();

    $mergedArray = $this->mergeArraysWithSameTime($mergedArray, $blocked_schedules);

    $availability = [];
    $aux = $config->opening;
    foreach($mergedArray as $array) {
      $opening = Carbon::parse($array['time_init']);
      $closing = Carbon::parse($array['time_end']);
      if($aux < $opening) $availability[] = $this->checkAvailability($aux, $opening);
      $aux = $closing;
    }

    if($aux < $config->closing) 
      $availability[] = $this->checkAvailability($aux, configTime: $config->closing);
    return $availability;
  }

  public function mergeArraysWithSameTime($array1, $array2): array
  {
    if ($array2 == null) return $array1;
    $mergedArray = array_merge(
        array_filter($array1, function ($array) use ($array2) {
            foreach ($array2 as $otherArray) {
                if ($array['time_init'] === $otherArray['time_init'] && $array['time_end'] === $otherArray['time_end']) {
                    return false;
                }
            }
            return true;
        }),
        $array2
    );

    usort($mergedArray, function ($a, $b) {
        return strtotime($a['time_init']) - strtotime($b['time_init']);
    });

    return $mergedArray;
  }

  private function getConfiguratedOperatingHours($room, $date){     
    $date = Carbon::parse($date)->toDateString();
    $operating_array = $operating_hours = json_decode($room->operating_hours);    
    $dayOfWeek = config('settings.days_of_week.'.Carbon::parse($date)->dayOfWeek);
    if (!isset($operating_hours[0]->$dayOfWeek)) return null;
    if (gettype($operating_hours[0]->$dayOfWeek) == 'object') {
      $operating_array = get_object_vars($operating_hours[0]->$dayOfWeek);
    }
    
    $configuredOperatingHours = [];
    foreach ($operating_array as $hour => $value) {
      $opening = $date.' '.(str_pad((int)$hour, 2, '0', STR_PAD_LEFT)).':00:00';
      $closing = $date.' '.(str_pad((int)$hour + 1, 2, '0', STR_PAD_LEFT)).':00:00';
      if ($hour == '23') $closing = $date.' 23:59:00'; 
      // dd($opening, $closing);  
      $configuredOperatingHours[] = [
        'time_init' => $opening,
        'time_end' => $closing
      ];      
    }
    return $configuredOperatingHours;
  }

  private function checkAvailability($aux, Carbon $time = null, Carbon $configTime = null)
  {
    return (object)[
      'opening' => $aux->format('H:i'),
      'closing' => ($configTime)? $configTime->format('H:i') : $time->format('H:i')
    ];
  }

  public function updateStatus(string $uuid, array $data)
  {
    $status      = $data['status'];
    $dateTime    = $data['date_time'];

    $appointment = AppointmentFacade::findByUuid($uuid);
    $customerRepository = new UserRepository();
    $customer = $customerRepository->find($appointment->customer_id);
    if(!$appointment) throw new \Exception("Appointment not found", 404);
    // dd($appointment);
    $this->validateStatus($appointment, $status);

    $dataToUpdate = [];
    $statusCanceled = false;
    switch ($status) {
      case 'canceled':
        $dataToUpdate['canceled_at'] = Carbon::parse($dateTime);
        $statusCanceled = true;
        break;
      case 'finished':
        $dataToUpdate['finished_at'] = Carbon::parse($dateTime);
        break;
      case 'checkin':
        $dataToUpdate['checkin_at'] = Carbon::parse($dateTime);
        break;
      case 'checkout':
        $dataToUpdate['checkout_at'] = Carbon::parse($dateTime);
        break;
    }

    $dataToUpdate['status'] = $status;
    
    if($statusCanceled){  
      // dd(date('Y-m-d H:i:s'));
      $dataAppointment = Carbon::parse($appointment->time_init);
      $now = Carbon::now();
      $diff = $dataAppointment->diffInHours($now);
      // dd($diff, $now, $dataAppointment);
      if (($diff < 24 || $dataAppointment < $now ) && (auth()->user()->hasRole('customer') || auth()->user()->hasRole('partner'))) {  
          throw ValidationException::withMessages(['error' => "Faltam menos de 24h para a data do agendamento, não é possível realizar o cancelamento."]);
      }
      // exit($diff);
    }

    $return = $appointment->update($dataToUpdate) ? $appointment->fresh(): false;

    if($statusCanceled){
      // dd($return);
      $dateTime = $appointment->time_init;
      $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);
      $formattedDateTime = $carbon->format('d/m/Y \à\s H:i');

      $itens = GenericMail::sendMail(
        $customer->email,
        'Cancelamento - Fusion Clinic',
        'emails.canceledAppointment',
        compact(['appointment', 'formattedDateTime']),
      );
    }

    return $return;
  }

  private function validateStatus(Appointment $appointment, string $status): void
  {
    switch ($status) {
      case 'canceled':
        $statusList = ['canceled', 'finished', 'checkin', 'checkout'];
        if(in_array($appointment->status, $statusList)) 
          $message[] = [$appointment->status => "The appointment is already {$appointment->status} and cannot be canceled"];
        break;
      
      case 'finished':
        $statusList = ['canceled', 'finished', 'checkin'];
        if(in_array($appointment->status, $statusList)) 
          $message[] = [$appointment->status => "The appointment is already {$appointment->status} and cannot be finished"];
        break;
      
      case 'checkin':
        $statusList = ['canceled', 'finished', 'checkin', 'checkout'];
        if(in_array($appointment->status, $statusList)) 
          $message[] = [$appointment->status => "The appointment is already {$appointment->status} and cannot be checkin"];
        break;
      
      case 'checkout':
        $statusList = ['canceled', 'finished', 'checkout'];
        if(in_array($appointment->status, $statusList)) 
          $message[] = [$appointment->status => "The appointment is already {$appointment->status} and cannot be checkout"];
        break;
    }

    if(isset($messages)) throw ValidationException::withMessages($messages);
  }

  public function searchByScheduleAndPartner(array $data)
  {
    $partner = auth()->user();
    $scheduleRepository = new ScheduleRepository();
    $appointmentRepository = new AppointmentRepository();
    $schedule = $scheduleRepository->findByUuid($data['schedule_uuid']);
    if(!$schedule) throw new \Exception("Schedule not found", 404);

    $appointment = $appointmentRepository->getModelWithFilterDate($data['date'], $data['date'])->where('schedule_id', $schedule->id);
    switch ($data['filter']) {
      case 'room':
        return $this->getAppointmentsByRoomUuid($appointment, $data['uuid']);
        break;
      default:
        return $this->getAppointmentsByStatus($appointment, $data['filter']);
        break;
    }
  }

  private function getAppointmentsByRoomUuid($appointment, $roomUuid)
  {
    $room = RoomFacade::findByUuid($roomUuid);
    return $appointment->where('room_id', $room->id)->paginate(config('settings.paginate'));
  }

  private function getAppointmentsByStatus($appointment, $filter)
  {
    $statuses = ['canceled', 'finished', 'checkin', 'checkout', 'scheduled'];
    if($filter == 'all') return $appointment->paginate(config('settings.paginate'));
    if(in_array($filter, $statuses)) return $appointment->where('status', $filter)->paginate(config('settings.paginate'));
    throw new \Exception("Status ".$filter ."not exist", 404);
  }

  public function getAppointmentsFinishedByPartner(int $partnerId, int $month, int $year)
  {
    $model = new Appointment();
    $appointments = $model->select('appointments.id','appointments.uuid','appointments.status','appointments.value_total','appointments.value_per_minute','appointments.time_total','billings.order_id')
      ->join('schedules', 'schedules.id', '=', 'appointments.schedule_id')
      ->join('billings', 'billings.model_id', '=', 'appointments.id')
      ->where('schedules.user_id', $partnerId)
      ->where('appointments.status', 'finished')
      ->where('billings.model_type', get_class($model))
      ->where('billings.paid', 'paid')
      ->whereMonth('billings.payment_at', $month)
      ->whereYear('billings.payment_at', $year);

    return $appointments ?? [];
  }

  public function getTotalValueOfAppointmentsByPartner(int $partnerId, int $month, int $year)
  {
    $appointments = $this->getAppointmentsFinishedByPartner($partnerId, $month, $year);
    return [
      'amount' => $appointments->sum('billings.amount'),
      'order_id' => $appointments->first()->order_id
    ];
  }
}