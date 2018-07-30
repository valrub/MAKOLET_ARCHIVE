<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Comment;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\CreateCommentRequest;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCommentRequest $request)
    {
        
        $comment = Comment::create([
            'level' => $request->level,
            'comment' => $request->comment,
            'shop_id' => $request->shop_id,
            'customer_id' => $request->customer_id,
            'order_id' => $request->order_id
        ]);

        if ($request->shop_id) {
            return redirect()->route('admin.shops.show', $request->shop_id);
        }

        if ($request->customer_id) {
            return redirect()->route('admin.customers.show', $request->customer_id);
        }

        if ($request->order_id) {
            return redirect()->route('admin.orders.show', $request->order_id);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if ($comment->shop_id) {
            $route = 'admin.shops.show';
            $id = $comment->shop_id;
        } else if ($comment->customer_id) {
            $route = 'admin.customers.show';
            $id = $comment->customer_id;
        } else if ($comment->order_id) {
            $route = 'admin.orders.show';
            $id = $comment->order_id;
        }

        $comment->delete();

        return redirect()->route($route, $id);

    }
}
