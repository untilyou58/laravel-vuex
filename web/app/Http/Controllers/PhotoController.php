<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Comment;
use App\Http\Requests\StoreComment;


class PhotoController extends Controller
{
    public function __construct()
    {
        // Authenticated
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * Upload image
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        // Get the extension of posted photos
        $extension = $request->photo->extension();

        $photo = new Photo();

        // Random ID value assigned at instantiation and
        // the original extension is combined to form the file name
        $photo->filename = $photo->id.'.'.$extension;

        // Save file to S3
        // The third argument 'public' is for saving the file in a public state
        Storage::cloud()
            ->putFileAs('', $request->photo, $photo->filename);

        // To delete the file when there is a database error
        // Use Transaction
        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // Remove uploaded file to avoid inconsistency with DB
            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }

        // Because a new resource created
        // Response 201 (CREATED)
        return response($photo, 201);
    }

    /**
     * List of photos
     */
    public function index()
    {
        $photos = Photo::with(['owner', 'likes'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return $photos;
    }

    /**
     * Download photo
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function download(Photo $photo)
    {
        // Check existence of photo
        if (! Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $disposition = 'attachment; filename="' . $photo->filename . '"';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => $disposition,
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    /**
     * Photo detail
     * @param string $id
     * @return Photo
     */
    public function show(string $id)
    {
        $photo = Photo::where('id', $id)->with(['owner', 'comments.author', 'likes'])->first();

        return $photo ?? abort(404);
    }

    /**
     * Add comment
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        // From comment to get author of comment
        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    /**
     * Like
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    /**
     * Unlike
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }
}
