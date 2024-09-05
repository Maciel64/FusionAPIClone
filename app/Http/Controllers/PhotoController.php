<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\StorePhotosRequest;
use App\Http\Requests\UpdatePhotoRequest;
use App\Models\Photo;
use App\Repositories\PhotoRepository;
use App\Services\PhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Photo'];
    }

    public function index(Request $request, PhotoRepository $repository)
    {
      $photos = $repository->listByModel($request->type, $request->uuid);
      return $this->response('index', $photos);
    }

    /**
     * show
     * 
     * Display the specified resource.
     *
     * @group Photo
     * @authenticated
     * 
     * @urlParam uuid required The UUID of the photo. Example: 07950d43-386c-4f0f-bb66-7b26778fc656
     * 
     * @response {
     *   "status": true,
     *   "message": "Photo retrieved successfully",
     *   "data": {
     *     "uuid": "07950d43-386c-4f0f-bb66-7b26778fc656",
     *     "name": "a408b342-df0d-4e8e-9b92-0fe163bcafc2.jpeg",
     *     "url": "http://localhost:9000/storage/avatar/a408b342-df0d-4e8e-9b92-0fe163bcafc2.jpeg"
     *   }
     * }
     * 
     * @param  string  $uuid Photo UUID
     * @return \Illuminate\Http\Response
     */
    public function show(string $uuid, PhotoRepository $repository)
    {
        $photo = $repository->findByUuid($uuid);
        return $this->response('show', $photo);
    }
    
  /**
   * 
   * bulk.store
   * 
   * Create a bulk photos for the specified user, coworking or room.
   * 
   * @group Photo
   * @authenticated
   * 
   * @urlParam uuid string required Coworking UUID. Example: 0b826f29-0c0f-4c4b-ae0f-5c54d4d4c214
   * @bodyParam type string required The type of resources. Example: avatar, coworking, room
   * @bodyParam photos object required List Photos of Coworking.
   * 
   * @response {
   *   "status": true,
   *   "message": "Photo created successfully",
   *   "data": [
   *     {
   *       "name": "kB8YDExGdjsdevV9YfN175LicYXZadvcuqVcN4iM.jpg",
   *       "url": "http://localhost:9000/storage/coworking/kB8YDExGdjsdevV9YfN175LicYXZadvcuqVcN4iM.jpg",
   *       "uuid": "dd211e3f-d9cf-4cdf-a5d2-7903786d844d"
   *     },
   *     {
   *       "name": "D30hCzWN1miUTqKW1aIKX6sCIGOhUTjUU3VP1WqR.jpg",
   *       "url": "http://localhost:9000/storage/coworking/D30hCzWN1miUTqKW1aIKX6sCIGOhUTjUU3VP1WqR.jpg",
   *       "uuid": "2de798dd-de44-48a6-9998-f2ea3d4821d8"
   *     },
   *     {
   *       "name": "k9HZdDlehCwICKNVmluhcEgl08ABCB8eDTO4PFoM.jpg",
   *       "url": "http://localhost:9000/storage/coworking/k9HZdDlehCwICKNVmluhcEgl08ABCB8eDTO4PFoM.jpg",
   *       "uuid": "266b8b26-0cb0-457a-946d-59e7721de2bb"
   *     }
   *   ]
   * }
   *
   * @param StorePhotoRequest $request
   * @param PhotoService $service
   * @return Response
   */
  public function bulkStore(StorePhotosRequest $request, PhotoService $service)
  {
    $repository = new PhotoRepository();

    $data = $request->validated();
    $photos = $service->storeBulkPhotos($data);
    $response = $repository->listByModel($request->type, $request->uuid);
    return $this->response('store', $response);
  }
    
  /**
   * 
   * store
   * 
   * Create a new photo for the specified user or update
   * 
   * @group Photo
   * @authenticated
   * 
   * @urlParam uuid required Coworking UUID. Example: 0b826f29-0c0f-4c4b-ae0f-5c54d4d4c214
   * @bodyParam type required The type of resources. Example: avatar, coworking, room
   * @bodyParam photo required List Photos of Coworking. 
   * 
   * @response {
   *  "status":true,
   *  "message":"Photo created successfully",
   *  "data":{
   *    "name":"8b2CkrABFCs9AZ3UZyPItDGliEXyJdu4G4uHRNd7.jpg",
   *    "url":"http:\/\/localhost:9000\/storage\/avatar\/8b2CkrABFCs9AZ3UZyPItDGliEXyJdu4G4uHRNd7.jpg",
   *    "uuid":"26c8dd89-572f-4971-b4fe-a6f8f5360e4c"
   *  }
   * }
   *
   * @param StorePhotoRequest $request
   * @param PhotoService $service
   * @return Response
   */
    public function store(StorePhotoRequest $request, PhotoService $service)
    {
      $data = $request->validated();
      $response = $service->createOrUpdatePhoto($data);
      return $this->response('store', $response);
    }

    /**
     * 
     * destroy
     * 
     * Remove the specified photo.
     * 
     * @group Photo
     * @authenticated
     * 
     * @urlParam uuid string required The uuid of the photo. Example: 4ec5605b-6f27-40c1-877c-2535ef682ce1
     * 
     * @response {
     *  "status": true,
     *  "message": "Photo deleted successfully",
     *  "data": []
     * }
     * 
     * @param  string $uuid The uuid of the photo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PhotoService $service)
    {
      $response = $service->destroy($request->type, $request->uuid);
      return $this->response('destroy', $response);
    }

}
