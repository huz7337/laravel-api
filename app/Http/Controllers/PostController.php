<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\ListPostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Transformers\PostTransformer;
use App\Models\Post;
use App\Models\User;
use App\Queries\PostQuery;
use App\Services\PostsService;

class PostController extends Controller
{
    /**
     * @var PostTransformer
     */
    protected PostTransformer $_transformer;

    /**
     * @var PostsService
     */
    protected PostsService $_service;

    /**
     * Constructor.
     * @param PostsService $service
     */
    public function __construct(PostTransformer $transformer, PostsService $service)
    {
        $this->_transformer = $transformer;
        $this->_service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(ListPostRequest $request)
    {
        $params = $request->validated();

        $items = PostQuery::make($params)->query()->paginate(
            $params['per_page'] ?? config('filters.per_page')
        );

        return response()->success($this->_transformer->transform($items->load('category')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePostRequest $request
     * @return mixed
     */
    public function store(CreatePostRequest $request)
    {
        $params = $request->validated();

        $post = $this->_service->createPost($params);

        return response()->success($this->_transformer->transform($post->load('category')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostRequest $request
     * @param Post $post
     * @return mixed
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $params = $request->validated();

        $post = $this->_service->updatePost($post, $params);

        return response()->success($this->_transformer->transform($post->load('category')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return mixed
     */
    public function destroy(Post $post)
    {
        $this->authorize(User::PERMISSION_POST_DELETE);

        $post->delete();

        return response()->message(__('The post :id has been removed.', ['id' => $post->id]));
    }
}