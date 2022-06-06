<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Model;
use CodeIgniter\RESTful\ResourceController;

class Comments extends ResourceController
{
    use ResponseTrait;
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $comment = new CommentModel();
        $data['comments'] = $comment->orderBy('id', 'DESC')->paginate(10);
        return $this->respond($data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $comment = new CommentModel();
        $data = $comment->where('id', $id)->first();
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No comments found');
        }
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $comment = new CommentModel();
        $data = [
            'name' => $this->request->getJsonVar('name'),
            'email'  => $this->request->getJsonVar('email'),
            'body'  => $this->request->getJsonVar('body'),
        ];
        $comment->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Comment created successfully'
            ]
        ];
        return $this->respondCreated($response);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {

    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $commentModel = new CommentModel();
        $comment = $commentModel->find($id);
        if($comment) {
            $data = [
                'name' => $this->request->getJsonVar('name'),
                'email' => $this->request->getJsonVar('email'),
                'body' => $this->request->getJsonVar('body'),
            ];
            $commentModel->update($id, $data);
            $response = [
                'status' => 200,
                'error' => null,
                'messages' => [
                    'success' => 'Comment updated successfully'
                ]
            ];
            return $this->respond($response);
        }else{
            return $this->failNotFound('Id Not found');
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function destroy($id = null)
    {
        $commentModel = new CommentModel();
        $comment = $commentModel->find($id);
        if($comment){
            $commentModel->delete($comment);
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Comment successfully deleted'
                ]
            ];
            return $this->respondDeleted($response);
        }else{
            return $this->failNotFound('No comment found');
        }
    }
}
