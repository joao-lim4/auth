<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class GitHubController extends Controller
{

    protected $branches = [
        "master",
        "teste",
    ];

    private function getHash(array $headers): ?string
    {
        if(count($headers) > 0) return $headers[0];
        return null;
    }

    private function isValid(Request $request, string $sha256): bool
    {
        $signature = Str::after($sha256, 'sha256=');

        if(!$signature) {
            return false;
        }

        $signingSecret = env("GIT_HUB_SECRET");

        if(empty($signingSecret)) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $signingSecret);

        return hash_equals($signature, $computedSignature);
    }

    private function verifyBranch(string $ref): bool
    {
        $branch = Str::after($ref, 'refs/heads/');

        if(!$branch) {
            return false;
        }

        for( $i = 0; $i < sizeof($this->$branches); $i++ ) {
            if($branch === $this->$branches[$i]) {
                return true;
            }
        }

        return false;
    }

    public function gitHubPullRequest(Request $request):void
    {

        $sha256 = $this->getHash($request->headers->all("x-hub-signature-256"));

        if(!is_null($sha256)) {
            if($this->verifyBranch($request->input("ref"))) {
                if($this->isValid( $request, $sha256 )) {
                    exec("sudo cd /var/www/authentication && ./pull.sh");
                }
            }
        }

    }

}
