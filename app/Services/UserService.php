<?php

namespace App\Services;

use App\Facades\PagarmeFacade;
use App\Facades\ScheduleServiceFacade;
use App\Mail\FirstAccessCustomer;
use App\Mail\NewCustomer;
use App\Mail\VerifyCode;
use App\Mail\VerifyCodeCustomer;
use App\Models\Appointment;
use App\Models\Card;
use App\Models\Contact;
use App\Models\HealthAdviceHasUser;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserVerifyCode;
use App\Notifications\AccountCancelledNotification;
use App\Repositories\AddressRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserVerifyCodeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Helpers\TimeHelper;

class UserService 
{
  private string $password = '';

  public function completeData(string $user_uuid, array $data){
    //tem que ser update ao invés de create
    
    $user = (new UserRepository())->updateByUuid($user_uuid, $data);

    $this->createAddressToCustomer($user->uuid, $data['address']);

    $this->createContactsToCustomer($user->id, $data['phones']);
    
    $this->customerRegisterInPagarme($user);

    if(!$user) return false;

    $user->assignRole('customer');
    
    return $user->fresh(); 

  }

  
  public function customerUpdateInPagarme(User $user)
  {
    $user = $user->makeHidden([
      'uuid',
      'last_access',
      'role_name',
      'photo',
      'address.uuid',
      'schedule',
      'advice',
      'email_verified_at',
      'account_active',
      'account_deactivated_at',
      'account_activated_at',
      'customer_id',
      'status',
      'gender'
      
    
    ]);
    
    $data = $user->toArray();
    $this->prepareDataToPagarme($data, $user->uuid);
    // dd($data);
    // Obs, quando o customer é cadastrado com sucesso no pagarme 
    return PagarmeFacade::editCustomer($user->customer_id, $data);
  }
  

  public function specialistInformations($dateInit, $dateEnd)
  {
    $userRepository = new UserRepository;
    $appointmentRepository = new AppointmentRepository;

    $specialistCount = $userRepository->getSpecialistCount($dateInit, $dateEnd);
    $totalOfAppointments = $appointmentRepository->getAppointmentCount($dateInit, $dateEnd);
    $appointmentCountByDistinctUser = $appointmentRepository->getAppointmentCountByDistinctUser($dateInit, $dateEnd);    
    $revenuePerHourByDate = $appointmentRepository->getRevenuePerHourByDate($dateInit, $dateEnd);
    $totalOfCanceledAppointments = $appointmentRepository->getCanceledAppointmentCount($dateInit, $dateEnd);
    // calculando a média de reservas formatado em horas
    $averageAppointmentsHours = ($totalOfAppointments == 0) ? 0 : $totalOfAppointments / $appointmentCountByDistinctUser;
    $hours = floor($averageAppointmentsHours); // Obtém a parte inteira (horas)
    $minutes = round(($averageAppointmentsHours - $hours) * 60); // Obtém os minutos
    $minutesFormatted = str_pad($minutes, 2, '0', STR_PAD_LEFT);
    $averageAppointmentsTime = "$hours:$minutesFormatted";

    $revenueAveragePerHour = ($totalOfAppointments == 0) ? 0 : $revenuePerHourByDate / $totalOfAppointments;    
    $cancellationFee = ($totalOfAppointments == 0) ? 0 : ($totalOfCanceledAppointments/$totalOfAppointments)*100;   
    
    $data = [
      'specialistCount' => $specialistCount,
      'averageHoursOfAppointments' => $averageAppointmentsTime,
      'revenueAveragePerHour' => number_format($revenueAveragePerHour, 2, ',', ''),
      'cancellationFee' => number_format($cancellationFee, 2, ',', ''),
    ];
    
    return $data;
  }


  public function updateBasicDataCustomer($user_uuid, $request)
  {
    $repository = new UserRepository;
    $response = $repository->updateByUuid($user_uuid, $request);
    $user = $repository->getByUuid($user_uuid)->first();
    // dd($user);
    $pagarmeResponse = $this->customerUpdateInPagarme($user);
    // dd($pagarmeResponse);
    return $response;
  }

  

  public function storeVerifyCode(User $user)
  {
    $repository = new UserVerifyCodeRepository();
    $data = [
      'user_id' => $user->id,
      'email'   => $user->email,
      'name'    => $user->name,
      'code'    => $this->generateRandomCode(),
    ];

    $code = $repository
      ->where('email', $user->email)
      ->where('user_id', $user->id)
      ->first();

    if($code){
      $code->update($data);
      return $code->fresh();
    }

    return $repository->create($data);
  }

  public function createAndSendNotificationToVerifyCode(User $user)
  {
    try {
        $code = $this->storeVerifyCode($user);
        
        if($user->role_name == 'customer'){
          $this->sendCodeVerificationByCustomer($user, $code);
        } else {
          $this->sendCodeVerification($user, $code);
        }

        return $user->fresh();
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function sendCodeVerificationByCustomer(User &$user, UserVerifyCode &$code)
  {
    try{
      $url        = config('settings.url.verify_code_email').'?email='.$user->email;
      $notifyData = [
        ...$code->toArray(), 
        'name'     => $user->name,
        'url'      => $url,
      ];

      Mail::to($user->email)->queue(new VerifyCodeCustomer($notifyData));
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }

  public function sendCodeVerification(User &$user, UserVerifyCode &$code)
  {
    try {
      if(!strlen($this->password)) {
        $password = $this->generatePassword();
        $user->update(['password' => $password]);
      }

      $url        = config('settings.url.verify_code_email').'?email='.$user->email;
      $notifyData = [
        ...$code->toArray(), 
        'email'    => $user->email,
        'name'     => $user->name,
        'url'      => $url,
        'password' => $this->password
      ];

      Mail::to($user->email)->queue(new VerifyCode($notifyData));
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }

  private function generateRandomCode()
  {
    $code = '';
    for($i = 0; $i < 6; $i++)
      $code .= rand(0, 9);
    return $code;
  }

  public function forceVerification(string $email)
  {
    $user = (new UserRepository())->findByEmail($email);
    if(!$user) return false;
    // $user->password =  Hash::make($password);
    $user->email_verified_at = now();
    $user->account_active = true;
    $user->account_activated_at = now();
    $user->save();
    (new UserVerifyCodeRepository())->destroyCodeVerificationByUser($user);
    return true;
  }

  public function verifyEmail(string $email, string $code)
  {
    $repository = new UserVerifyCodeRepository();
    $repositoryUser = new UserRepository();
    $user = $repositoryUser->findByEmail($email);
    $verification = $repository->where('email', $email)->where('code', $code)->first();
    Log::info('UserService@verifyEmail', [$verification]);
    if(!$verification) return false;
    $user->email_verified_at = now();
    $user->account_active = true;
    $user->account_activated_at = now();
    $user->save();
    $verification->delete();
    return ['verified' => true];
  }

  public function assignRole(User $user, string $type)
  {
    if(auth()->check() && auth()->user()->hasRole('owner')) {
      $user->assignRole('admin');
      $this->assignSchedule($user);
    }else{
      $user->assignRole('customer'); 
    }
  }

  public function assignSchedule($user)
  {
    $repository = new ScheduleRepository();
    $repository->create(['user_id' => $user->id]);
  }

  private function generatePassword()
  {
    $this->password = Str::random(8);
    return Hash::make($this->password);
  }

  public function store(array $data) 
  {
    $password         = $data['email'];
    $data['password'] =  Hash::make($password);
    // dd($data);
    $repository       = new UserRepository();
    $user             = $repository->create($data);
    $user->assignRole($data['user_type']);
    ScheduleServiceFacade::store($user);
    //return $this->createAndSendNotificationToVerifyCode($user);
    return $user->fresh();
  }

  public function customerStore(array $data)
  {
    try {
      $user = (new UserRepository())->create($data);
      Photo::create([
        'model_id'   => $user->id, 
        'model_type' => User::class,
        'url'        => "https://api.dicebear.com/6.x/initials/svg?seed=".$user->name,
        'name'       => $user->name,
      ]);

      // $this->createAddressToCustomer($user->uuid, $data['address']);
      $this->createContactsToCustomer($user->id, $data['phones']);
      if(!$user) return false;
      $user->assignRole('customer');

      // if(auth()->check() && (auth()->user()->hasRole('owner') or auth()->user()->hasRole('admin'))) {
      //   return $this->customerCheck($user->uuid) ? true : false;
      // }

      // Mail::to($user->email)->queue(new NewCustomer($user->id));
      $user->healthAdvice()->create($data);

      // $this->customerRegisterInPagarme($user);
      return $user->fresh(); 
    } catch (\Throwable $th) {
      throw $th; 
    }
  }

  private function createAddressToCustomer(string $userUuid, array $data)
  {
    $data['uuid'] = $userUuid;
    $data['type'] = 'user';
    $addressRepository = new AddressRepository();
    return $addressRepository->store($data);
  }

  private function createContactsToCustomer($userId, array $data)
  {
    $contacts = [];
    foreach($data as $key => $contact) {
      $contact['model_id'] = $userId;
      $contact['model_type'] = User::class;
      $contact['type']    = $key;
      $contacts[] = Contact::create($contact);
    }

    return $contacts;
  }

  // quando o customer é chacado pelo admin, 
  // o registro do customer é criando junto ao pagarme
  public function customerCheck(string $uuid)
  {
    $repository = new UserRepository();
    $user = $repository->findByUuid($uuid);
    if(!$user) return false;
    if(!$user->hasRole('customer')) return false;

    $user->email_verified_at =  now();
    $user->save();

    Mail::to($user->email)->queue(new FirstAccessCustomer($user->id));
    return true;
  }

  private function customerRegisterInPagarme(User $user)
  {
    $user = $user->makeHidden([
      'uuid',
      'last_access',
      'role_name',
      'photo',
      'address.uuid',
      'schedule',
      'advice',
    ]);

    $data = $user->toArray();
    $this->prepareDataToPagarme($data, $user->uuid);

    // Obs, quando o customer é cadastrado com sucesso no pagarme 
    return PagarmeFacade::createCustomer($data, $user->id);
  }

  private function prepareDataToPagarme(&$data, $userUuid)
  {
    $phones = [];
    foreach ($data['contacts'] as $contact) {
      $type = $contact['type'];
      $phones[$type] = [
        'country_code' => $contact['country_code'],
        'area_code'    => $contact['area_code'],
        'number'       => $contact['number'],
      ];
    }

    $address = $data['address'];
    unset($address['uuid'], $data['contacts']);

    $data['code']   = $userUuid;
    $data['type']   = 'individual';
    $data['address'] = $address;
    $data['phones']  = $phones;
    
  }

  public function getAllPartners()
  {
    return User::whereHas('roles', function($query) {
      $query->where('name', 'partner');
    })->get();
  }

  public function cancelAccount(string $uuid)
  {
    $user                   = (new UserRepository())->findByUuid($uuid);
    $data = [
      'account_active'         => false,
      'account_activated_at'   => null,
      'account_deactivated_at' => now(),
    ];

    if($user->update($data)){
      if($user->subscription)
        (new SubscriptionService())->cancel($user->subscription->uuid);
      $user->notify(new AccountCancelledNotification());
    }

    return $user->fresh();
  }

  public function activeAccount(string $uuid)
  {
    $user                   = (new UserRepository())->findByUuid($uuid);
    $user->update([
      'account_active'         => true,
      'account_activated_at'   => now(),
      'account_deactivated_at' => null,
    ]);

    return $user->save();
  }

  public function setActiveStatus(string $uuid, bool $activation)
  {
    $user = (new UserRepository())->findByUuid($uuid);

    if ($activation == 0){   
      if ($user->account_active == 0) return response()->json([
        'Status' => false,
        'Message' => 'Usuário já está desativado',
      ],400);

      $user->update([
        'account_active'         => false,
        'account_deactivated_at' => now(),
      ]);
      $user->save();

      return response()->json([
        'Status' => true,
        'Message' => 'Usuário desativado com sucesso',
        'Data' => $user->fresh()
      ]);
    }
    if ($user->account_active == 1) return response()->json([
      'Status' => false,
      'Message' => 'Usuário já está ativo',
    ],400);

    $user->update([
      'account_active'         => true,
      'account_activated_at'   => now(),
      'account_deactivated_at' => null,
    ]);

    return response()->json([
      'Status' => true,
      'Message' => 'Usuário ativado com sucesso',
      'Data' => $user->fresh()
    ]);    
  }



  public function getAllCustomers(array $data)
  {
    $user = User::select([
      'users.uuid', 
      'users.id', 
      'users.name', 
      'users.email',
      'users.email_verified_at',
    ])->whereHas('roles', function($query) {
      $query->where('name', 'customer');
    });
    
    $user = ($data['verified'])?
      $user->whereNotNull('email_verified_at'):
      $user->whereNull('email_verified_at');

    switch (true) {
      case (isset($data['name']) && isset($data['email']) && $data['email'] != '' && $data['name'] != ''):
          $user = $user->where('name', 'like', "%{$data['name']}%")
          ->where('email', 'like', "%{$data['email']}%");
        break;
      case (isset($data['name']) && $data['name'] != ''):
        $user = $user->where('name', 'like', "%{$data['name']}%");
        break;
      case (isset($data['email']) && $data['email'] != ''):
        $user = $user->where('email', 'like', "%{$data['email']}%");
        break;
      default:
        $user = $user;
        break;
    }

    return $user->paginate();
  }
}