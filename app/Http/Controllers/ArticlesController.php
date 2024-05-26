<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataFormated = [];
        $articles = Articles::with('user')->get()->toArray();

        foreach ($articles as $key => $article) {
            array_push($dataFormated, [
                'type' => 'articles',
                'id' => $article['id'],
                'Attribute' => [
                    'title' => $article['title'],
                    'description' => $article['description'],
                    'created_at' => $article['created_at']
                ],
                'relationships' => [
                    'user' => [
                        'id' => $article['user']['id'],
                        'Attribute' => [
                            'name' => $article['user']['name'],
                            'picture' => $article['user']['picture'],
                        ]
                    ]
                ]
            ]);
        }

        return response()->json([
            'data' => $dataFormated
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $data = Validator::make($req->all(),[
            'title' => 'required|string',
            'description' => 'required|string',
            'fk_user' =>'required|integer',
        ]);    

        if($data->fails()){
            $errors = $data->errors()->toArray();
            $formatedErrors = [];

            foreach($errors as $field => $message){
                array_push($formatedErrors,[
                    'status' => '422',
                    'title' => 'Unprocessable Entity',
                    'detail' => $message[0],
                    'field' => $field
                ]);
            }

            return response()->json([
                'errors' => $formatedErrors
            ]);
        }else{
            $article = Articles::create([
                "title" => $req->title,
                "description" => $req->description,
                "fk_user" => $req->fk_user
            ]);

            return response()->json([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->id,
                    'attributes' => [
                        'title' => $article->title,
                        'description' => $article->description,
                        'created_at' => $article->created_at
                    ]
                ]
            ]);
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
        $article = Articles::with('user')->where('id','=', $id)->first();

        if(!empty($article)){
            return response()->json([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->id,
                    'attributes' => [
                        'title' => $article->title,
                        'description' => $article->description,
                        'created_at' => $article->created_at
                    ],
                    'relationships' => [
                        'user' => [
                            'id' => $article->user->id,
                            'attributes' => [
                                'name' => $article->user->name,
                                'picture' => $article->user->picture
                            ]
                        ]
                    ]
                ]
            ]);
        }else{
            return response()->json([
                'data' => [
                    'type' => 'articles',
                    'attributes' => []
                ]
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $data = Validator::make($req->all(),[
            'id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'fk_user' =>'required|integer',
        ]);   

        if($data->fails()){
            $errors = $data->errors()->toArray();
            $formatedErrors = [];

            foreach($errors as $field => $message){
                array_push($formatedErrors,[
                    'status' => '422',
                    'title' => 'Unprocessable Entity',
                    'detail' => $message[0],
                    'field' => $field
                ]);
            }

            return response()->json([
                'errors' => $formatedErrors
            ]);
        }else{
            Articles::where('fk_user','=',$req->fk_user)
                    ->where('id','=',$req->id)
                    ->update([
                        'title' => $req->title,
                        'description' => $req->description,
                    ]);

            return response()->json([
                'data' => [
                    'type' => 'articles',
                    'attributes' => []
                ]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $req)
    {
        
        $req->all();

        $data = Validator::make($req->all(),[
            'id' => 'required|integer',
            'fk_user' =>'required|integer',
        ]);   

        if($data->fails()){
            $errors = $data->errors()->toArray();
            $formatedErrors = [];

            foreach($errors as $field => $message){
                array_push($formatedErrors,[
                    'status' => '422',
                    'title' => 'Unprocessable Entity',
                    'detail' => $message[0],
                    'field' => $field
                ]);
            }

            return response()->json([
                'errors' => $formatedErrors
            ]);
        }else{
            Articles::where('fk_user','=',$req->fk_user)
                    ->where('id','=',$req->id)
                    ->delete();

            return response()->json([
                'data' => [
                    'type' => 'articles',
                    'attributes' => []
                ]
            ]);
        }
    }
}
