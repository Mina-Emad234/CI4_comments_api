<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Model;

class User extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $helper_arrays = ['form', 'session', 'cookie', 'jwt'];
        helper($helper_arrays);
    }
    public function userRegister(): \CodeIgniter\HTTP\Response
    {
        $validation =  \Config\Services::validation();//validation library
        $validation->setRules([//validation rules
            'email' => ['label' => 'E-mail', 'rules' => 'required|valid_email|is_unique[users.email]'],
            'name' => ['label' => 'Name', 'rules' => 'required|alpha'],
            'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]'],
            'confirm_password' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]'],
        ]);
        $session = \Config\Services::session();//session library
        $session->start();//session open
        $user = new UserModel();
            if ($validation->withRequest($this->request)->run() === FALSE ) {
                $errors=$validation->getErrors();
                return $this->fail($errors, 410);
            } else {//if validation is true
                $hash_pass = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
                $data = array(
                    'name' => esc($this->request->getVar('name')),
                    'email' => esc($this->request->getVar('email')),
                    'password' => $hash_pass,
                );
                $user->insert($data);
                $response = [
                    'status'   => 200,
                    'error'    => null,
                    'messages' => [
                        'success' => 'User Registered successfully'
                    ]
                ];
                return $this->respond($response);
            }
    }

    public function userLogin(){
        $validation =  \Config\Services::validation();//validation library
        $session = session();
        $session->start();//open session
        $user = new UserModel();
        $validation->setRules([//validation rules
            'email' => ['label' => 'E-mail', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]'],
        ]);
        $email = $this->request->getVar('email');
        $clear_password = $this->request->getVar('password');
        $verify = $user->select('password')->where('email',$email)->limit(1)->get()->getRowArray();
        if ($validation->withRequest($this->request)->run() === FALSE ) {
            $errors=$validation->getErrors();
            return $this->fail($errors, 411);
        }
        if (password_verify($clear_password, $verify['password'])) {
                $user_data = $user->select('id,name')->where('email',$email)->limit(1)->get()->getRowArray();
                $user_data['email']=$email;
                $token = create_jwt($user_data);//get token using rand_id
                $session->set('token', $token);//set token as session_id
            $response = [
                    'status'   => 201,
                    'error'    => null,
                    'messages' => [
                        'success' => 'User Login successfully',
                        'token' => $token
                    ]
                ];
                return $this->respond($response);
            } else {
                return $this->fail('Authentication Error!', 400);
            }
    }

     public function userLogout(){
         $sess = session();
         $sess->destroy();
         $response = [
             'status'   => 202,
             'error'    => null,
             'messages' => [
                 'success' => 'User Logout successfully',
             ]
         ];
         return $this->respond($response);
     }
}
