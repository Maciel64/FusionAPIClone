<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{

    public function __construct()
    {
      $this->resource = ['resource' => 'Category'];
    }

    /**
     * index 
     * 
     * Display a listing of the categories resource.
     * 
     * @group Fusion
     * @subgroup Categories
     * @authenticated
     * 
     * @response {
     *   "status": true,
     *   "message": "Category list retrieved successfully",
     *   "data": [
     *     {
     *       "uuid": "63b02269-4599-4d23-b6da-517b0d20cdd3",
     *       "name": "Mckenna Langosh",
     *       "description": "Iure beatae aliquid maxime sunt. Quis similique nobis non assumenda qui. Voluptatum consequatur est optio eos inventore voluptatem."
     *     },
     *     {
     *       "uuid": "5612f17f-6250-4fd0-bc7f-b71e821ea9d5",
     *       "name": "Dr. Napoleon Weissnat",
     *       "description": "Quis ut est nulla quae aspernatur dignissimos dicta. Tempore exercitationem veniam provident beatae soluta. Dolores ab qui cumque quis. Alias iure sed corrupti soluta magnam sint commodi."
     *     }
     *   ]
     * }
     * 
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function index(Request $request, CategoryRepository $repository)
    {
      $response = $repository->getAllCategories();
      return $this->response('list', $response);
    }

    /**
     * store 
     * 
     * Store a newly created resource in storage.
     * 
     * @group Fusion
     * @subgroup Categories
     * @authenticated
     * 
     * @bodyParam name string required The name of the category. Example: test
     * @bodyParam description string required The description of the category. Example: test
     * 
     * @response {
     * "status":true,
     * "message":"Category created successfully",
     * "data":{
     *   "name":"test",
     *   "description":"test",
     *   "uuid":"e63251c9-7fa2-455a-aa52-b39ba2b7e635"
     *  }
     * }
     * 
     * @param StoreCategoryRequest  $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function store(StoreCategoryRequest $request, CategoryRepository $repository)
    {
      Gate::authorize('create', Category::class);
      $data = $request->validated();
      $response = $repository->create($data);
      return $this->response('store', $response);
    }

    /**
     * show
     * 
     * Display the specified resource.
     * 
     * @group Fusion
     * @subgroup Categories
     * @authenticated
     * 
     * @urlParam uuid string required The uuid of the category. Example: 14807356-3f60-4194-8643-25db86cd580c
     * 
     * @response {
     *  "status":true,
     *  "message":"Category retrieved successfully",
     *  "data":{
     *    "uuid":"14807356-3f60-4194-8643-25db86cd580c",
     *    "name":"Prof. Arely Mayer Jr.",
     *    "description":"Sed provident placeat ratione excepturi. Iure fuga maxime qui ullam. Voluptatem nam necessitatibus et labore maxime qui. Non blanditiis maxime aut iure. Perspiciatis explicabo molestiae deleniti."
     *  }
     * }
     * 
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function show(Request $request, CategoryRepository $repository)
    {
      $response = $repository->findByUuid($request->uuid);
      return $this->response('show', $response);
    }

    /**
     * update
     * 
     * Update the specified resource in storage.
     *      
     * @group Fusion
     * @subgroup Categories
     * @authenticated
     * 
     * @queryParam uuid string required The uuid of the category. Example: 14807356-3f60-4194-8643-25db86cd580c
     * 
     * @bodyParam name string required The name of the category. Example: test
     * @bodyParam description string required The description of the category. Example: test
     * 
     * @response {
     *    "status":true,
     *    "message":"Category updated successfully",
     *    "data":{
     *       "uuid":"59039695-273f-4a4f-897c-750a89ec2733",
     *       "name":"test updated",
     *       "description":"test"
     *    }
     * }
     * @param UpdateCategoryRequest  $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function update(UpdateCategoryRequest $request, CategoryRepository $repository)
    {
      $data = $request->validated();
      $response = $repository->updateByUuid($request->uuid, $data);
      return $this->response('update', $response);
    }

    /**
     * 
     * destroy
     * 
     * Remove the specified resource from storage.
     * 
     * @group Fusion
     * @subgroup Categories
     * @authenticated
     * 
     * @urlParam uuid string required The uuid of the category. Example: 14807356-3f60-4194-8643-25db86cd580c
     * 
     * @response {
     *    "status":true,
     *    "message":"Category deleted successfully",
     *    "data":[]
     * }
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
     */
    public function destroy(Request $request, CategoryRepository $repository)
    {
      $response = $repository->deleteByUuid($request->uuid);
      return $this->response('destroy', $response);
    }
}
