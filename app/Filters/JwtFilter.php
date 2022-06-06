<?php


namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Model;
use Config\Services;

class JwtFilter implements FilterInterface
{
    use ResponseTrait;
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['jwt','session']);
        $sess=session();
        $sess->start();
        if ($sess->has('token') && $sess->get('token') == $request->getJsonVar('token')) {
            $payload = (string) $sess->get('token');//convert session value(token) to string
            $sign_verify = (array) verify_jwt($payload);//verify token -->decode to php object
            $expiration = $sign_verify['exp'];
            $not_before = $sign_verify['nbf'];
            $u_id = $sign_verify['data']->id;

            $user = new UserModel();
            $verify=$user->selectCount('email')->where('id',$u_id)->limit(1)->get()->getRowArray();
            if ($expiration >= time() and $not_before <= time() and $verify['email']==1) {//if token not expired
                return true;
            } else {//if token expired
                return Services::response()->setJSON([ 'status'=>404,'error'=>404,'message' => 'Invalid token']);
            }
        } else {//if session not exists
            return Services::response()->setJSON([ 'status'=>404,'error'=>404,'message' => 'Invalid token']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}