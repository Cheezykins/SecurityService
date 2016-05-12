<?php


namespace App\Api\V1;

use App\HashSigner;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function secure(Request $request)
    {
        return [
            'saved' => HashSigner::create($request->input('password'))->getHash()
        ];
    }

    public function changeif(Request $request)
    {
        $oldSignature = $request->input('signature');

        $old = new HashSigner($oldSignature);

        if ($old->validate($request->input('password'))) {

            $newHash = HashSigner::create($request->input('newpassword'));
            $old->invalidate();
            return [
                'saved' => $newHash->getHash()
            ];

        } else {

            return [
                'verified' => false
            ];

        }
    }

    public function verify(Request $request)
    {
        $hash = new HashSigner($request->input('signature'));

        return [
            'verified' => $hash->validate($request->input('password'))
        ];
    }
}