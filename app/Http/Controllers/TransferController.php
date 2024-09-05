<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Transfer;
use App\Repositories\TransferRepository;
use App\Services\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Transfer'];
    }

    /**
     * search
     * 
     * @group Partner
     * @subgroup Transfer
     * @authenticated
     * 
     * @bodyParam month integer required The month of the transfer. Example: 1
     * @bodyParam year integer required The year of the transfer. Example: 2021
     * 
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "uuid": "30e088d0-b096-4f12-afc3-7e5b348abf89",
     *       "partner_id": 2,
     *       "order_id": "be9eed6c-ba8c-3f54-8356-adf250de8f6b",
     *       "status": "paid",
     *       "note": "Accusamus rerum et amet et velit nisi. Molestiae et est officiis libero impedit voluptatem et. Reiciendis et porro reprehenderit.",
     *       "amount": 765.17,
     *       "discount": 466.17,
     *       "total": 698.39,
     *       "receipt_name": "Tevin Cruickshank",
     *       "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/0022ee?text=nostrum",
     *       "updated_by": null,
     *       "paid_at": null
     *     },
     *     {
     *       "uuid": "1cadef8e-89b1-4dc1-b50e-2765418b9689",
     *       "partner_id": 2,
     *       "order_id": "aa1a6e71-03cd-3cf3-988c-bbd99dc9e8ac",
     *       "status": "pending",
     *       "note": "Ex soluta eveniet nulla voluptas dolorum commodi. Qui sunt ea quasi consequatur quasi. Reiciendis aliquam quae voluptatem aliquam. Id eligendi optio vel velit est sed.",
     *       "amount": 222.93,
     *       "discount": 714.57,
     *       "total": 587.1,
     *       "receipt_name": "Idell Batz III",
     *       "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/0066ff?text=magni",
     *       "updated_by": null,
     *       "paid_at": null
     *     },
     *     {
     *       "uuid": "8756b1ea-d97b-4af5-8ed4-67b52756c08a",
     *       "partner_id": 2,
     *       "order_id": "7d123e08-da35-3ef6-b07a-efed18fdfe87",
     *       "status": "pending",
     *       "note": "Est ipsa veniam esse eaque consectetur. Iste placeat aperiam eos. Perferendis culpa ratione sit unde ut omnis sit aut. Eum delectus quo maxime cupiditate minima.",
     *       "amount": 114.99,
     *       "discount": 34.19,
     *       "total": 497.2,
     *       "receipt_name": "Jena Prosacco",
     *       "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/00ffbb?text=corrupti",
     *       "updated_by": null,
     *       "paid_at": null
     *     }
     *   ],
     *   "first_page_url": "http:\/\/localhost:9000\/api\/partner\/transfer\/search?page=1",
     *   "from": 1,
     *   "last_page": 1,
     *   "last_page_url": "http:\/\/localhost:9000\/api\/partner\/transfer\/search?page=1",
     *   "links": [
     *     {
     *       "url": null,
     *       "label": "&laquo; Previous",
     *       "active": false
     *     },
     *     {
     *       "url": "http:\/\/localhost:9000\/api\/partner\/transfer\/search?page=1",
     *       "label": "1",
     *       "active": true
     *     },
     *     {
     *       "url": null,
     *       "label": "Next &raquo;",
     *       "active": false
     *     }
     *   ],
     *   "next_page_url": null,
     *   "path": "http:\/\/localhost:9000\/api\/partner\/transfer\/search",
     *   "per_page": 30,
     *   "prev_page_url": null,
     *   "to": 3,
     *   "total": 3
     * }
     * 
     * @param Request $request
     * @param TransferRepository $repository
     * @return void
     */
    public function index(Request $request, TransferRepository $repository)
    {
      $request->validate([
        'month' => 'required|integer',
        'year' => 'required|integer',
      ]);

      $response = $repository->getTransfersByMonthAndYear($request->month, $request->year)->paginate(config('settings.paginate'));
      return response()->json($response);
    }

    /**
     * search
     * 
     * Search transfers.
     * 
     * @group Fusion
     * @subgroup Transfer
     * @authenticated
     * 
     * @bodyParam start_date date optional The start date of the transfer. Example: 2021-01-01
     * @bodyParam end_date date optional The end date of the transfer. Example: 2021-01-01
     * @bodyParam status string optional The status of the transfer. Example: pending
     * @bodyParam partner_uuid string optional The uuid of the partner. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * @bodyParam month integer optional The month of the transfer. Example: 1
     * @bodyParam year integer optional The year of the transfer. Example: 2021
     * 
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "uuid": "8eae0162-0a78-406d-930e-a0f9d7f02100",
     *       "partner_id": 2,
     *       "order_id": "30e6917f-c0c5-355c-9c72-2eb0b4bd267f",
     *       "status": "pending",
     *       "note": "Voluptates et voluptatum maxime consectetur exercitationem eveniet dolor. Est aperiam iusto dolorem modi reiciendis. Sunt quia dignissimos in.",
     *       "amount": 409.06,
     *       "discount": 900.02,
     *       "total": 429.05,
     *       "receipt_name": "Ms. Courtney Dach Sr.",
     *       "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/00cc33?text=laborum",
     *       "updated_by": null,
     *       "paid_at": null
     *     },
     *     {
     *       "uuid": "e409845c-0846-470e-8bc7-3d9679eb322f",
     *       "partner_id": 2,
     *       "order_id": "77db7f2d-9da5-306f-8409-04f3a355d283",
     *       "status": "paid",
     *       "note": "Perferendis qui illo voluptates ut velit. Vero deleniti magni qui veritatis. Facilis vel sit totam.",
     *       "amount": 853.46,
     *       "discount": 309,
     *       "total": 600.77,
     *       "receipt_name": "Harry Shields",
     *       "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/0077ff?text=ex",
     *       "updated_by": null,
     *       "paid_at": null
     *     },
     *     {
     *       "uuid": "3006c6be-aedd-43f1-8b78-88d4b69ff4e0",
     *       "partner_id": 2,
     *       "order_id": "07bc9c81-9ba1-369b-9511-56ca802f4a09",
     *       "status": "paid",
     *       "note": "Non quia ad aut earum. Et repudiandae accusantium saepe et. Blanditiis culpa officiis dolorem numquam mollitia.",
     *       "amount": 299.43,
     *       "discount": 75.77,
     *       "total": 238.27,
     *       "receipt_name": "Frieda Kub V",
     *       "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/00cc00?text=laboriosam",
     *       "updated_by": null,
     *       "paid_at": null
     *     }
     *   ],
     *   "first_page_url": "http:\/\/localhost:9000\/api\/fusion\/transfer\/search?page=1",
     *   "from": 1,
     *   "last_page": 1,
     *   "last_page_url": "http:\/\/localhost:9000\/api\/fusion\/transfer\/search?page=1",
     *   "links": [
     *     {
     *       "url": null,
     *       "label": "&laquo; Previous",
     *       "active": false
     *     },
     *     {
     *       "url": "http:\/\/localhost:9000\/api\/fusion\/transfer\/search?page=1",
     *       "label": "1",
     *       "active": true
     *     },
     *     {
     *       "url": null,
     *       "label": "Next &raquo;",
     *       "active": false
     *     }
     *   ],
     *   "next_page_url": null,
     *   "path": "http:\/\/localhost:9000\/api\/fusion\/transfer\/search",
     *   "per_page": 15,
     *   "prev_page_url": null,
     *   "to": 3,
     *   "total": 3
     * }
     *
     * @param Request $request
     * @param TransferService $service
     * @return void
     */
    public function search(Request $request, TransferService $service)
    {
      $request->validate([
        'start_date'   => 'sometimes|date',
        'end_date'     => 'sometimes|date',
        'status'       => 'sometimes|in:pending,paid,all',
        'partner_uuid' => 'sometimes|uuid',
        'month'        => 'sometimes|integer',
        'year'         => 'sometimes|integer',
      ]);

      $response = $service->search($request->all());
      return response()->json($response);
    }

    /**
     * show
     * 
     * Display the specified resource.
     *
     * @group Fusion
     * @subgroup Transfer
     * @authenticated
     * 
     * @queryParam uuid string required The uuid of the transfer. Example: 1a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p
     * 
     * @response {
     *   "status": true,
     *   "message": "Transfer retrieved successfully",
     *   "data": {
     *     "uuid": "42aba195-b7c8-4b05-824f-2829a4bbcc6f",
     *     "partner_id": 2,
     *     "order_id": "0f223180-0a97-3639-a0f2-8595be8e84df",
     *     "status": "pending",
     *     "note": "Qui ut quas sit molestias. Culpa facilis eum expedita qui soluta aut mollitia reprehenderit. Totam libero ut pariatur aut aliquid qui corporis ea.",
     *     "amount": 587.71,
     *     "discount": 741.14,
     *     "total": 695.7,
     *     "receipt_name": "Maurine Bahringer V",
     *     "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/0000dd?text=possimus",
     *     "updated_by": null,
     *     "paid_at": null
     *   }
     * }
     * 
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, TransferRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show',$response);
    }

    /**
     * update
     * 
     * Update the specified resource in storage.
     * 
     * @group Fusion
     * @subgroup Transfer
     * @authenticated
     * 
     * @queryParam status string The status of the transfer. Example: paid
     * @queryParam note string The note of the transfer. Example: Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quisquam, quod.
     * @queryParam discount numeric The discount of the transfer. Example: 100
     * 
     * @response{
     *   "status": true,
     *   "message": "Transfer updated successfully",
     *   "data": {
     *     "uuid": "7a543787-9ca6-48b1-92da-dd3ce75cb09c",
     *     "partner_id": 2,
     *     "order_id": "9610da37-f129-3f04-bfeb-a45ddf7b7899",
     *     "status": "paid",
     *     "note": "At explicabo repudiandae similique. Magni voluptas ut nostrum nulla eveniet. Magni repellat deleniti quos blanditiis et distinctio commodi laborum.",
     *     "amount": 178.71,
     *     "discount": 10,
     *     "total": 549.23,
     *     "receipt_name": "Madeline Davis",
     *     "receipt_url": "https:\/\/via.placeholder.com\/640x480.png\/00bb77?text=sint",
     *     "updated_by": null,
     *     "paid_at": null
     *   }
     * }
     * 
     * @param  \App\Http\Requests\UpdateTransferRequest  $request
     * @param  \App\Models\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransferRequest $request, TransferRepository $repository)
    {
      $data = $request->validated();
      $response = $repository->updateByUuid($request->uuid, $data);
      return $this->response('update',$response);
    }


    /**
     * upload.receipt
     * 
     * Upload receipt for the specified transfer.
     * 
     * @group Fusion
     * @subgroup Transfer
     * 
     * @authenticated
     * 
     * @urlParam uuid required The UUID of the transfer. Example: 07950d43-386c-4f0f-bb66-7b26778fc656
     * 
     * @response {
     * "status": true,
     * "message": "Transfer uploaded successfully",
     * "data": {
     *   "uuid": "f0ff58d8-f2b5-487e-bf89-4964389cbee9",
     *   "partner_id": 3,
     *   "order_id": "7f411775-bd3b-3256-8327-9b477d3d01df",
     *   "status": "paid",
     *   "note": "Dolorum natus aut et quisquam in repellat ut assumenda. Et dolor esse dolorum dolorum totam provident est culpa. Iste aut laborum et iste nobis autem id sunt.",
     *   "amount": 805.91,
     *   "discount": 913.84,
     *   "total": 359.15,
     *   "receipt_name": "DGrEDg9ECmzsEYK7kfUm5ACRvyhye8MK4zmqIRwf.jpg",
     *   "receipt_url": "http:\\localhost:9000\storage\receipts\DGrEDg9ECmzsEYK7kfUm5ACRvyhye8MK4zmqIRwf.jpg",
     *   "updated_by": null,
     *   "paid_at": null
     *   }
     * }
     * 
     * @param  string  $uuid Photo UUID
     * @return \Illuminate\Http\Response
     */
    public function uploadReceipt(Request $request, TransferService $service)
    {
      $request->validate(['receipt' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048']);
      $response = $service->uploadReceipt($request->uuid, $request->file('receipt'));
      return $this->response('upload',$response);
    }

    /**
     * 
     * download.receipt
     * 
     * Download receipt for the specified transfer.
     * 
     * @group Partner
     * @subgroup Transfer
     * 
     * @authenticated
     * 
     * @urlParam uuid required The UUID of the transfer. Example: 07950d43-386c-4f0f-bb66-7b26778fc656
     * 
     * @response {
     * "status":true,
     * "message":"Transfer downloaded successfully",
     * "data":[]
     * }
     * 
     * @param  string  $uuid Photo UUID
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt(Request $request, TransferService $service)
    {
      $response = $service->downloadReceipt($request->uuid);
      return $this->response('download',$response);
    }
}
