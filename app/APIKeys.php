<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APIKeys extends Model
{
    protected $guarded = ['key','secret'];

    protected $table = "api_keys";

    public function generate() {
        $api = new APIKeys();
        $api->generate_key(true);
        $api->generate_secret();
        return $api;
    }

    public function generate_key(bool $dontsave = false) {
        $this->key = sha1(base64_encode(microtime(true)));
        if (!$dontsave) $this->save();
    }

    public function generate_secret(bool $dontsave = false) {
        $fp = fopen("/dev/urandom", "rb");
        $entropy = fread($fp, 32);
        fclose($fp);
        $entropy .= uniqid(mt_rand(), true);
        $hash = hash_hmac("sha512", $entropy);
        $hash = gmp_strval(gmp_init($hash, 16), 62);
        $this->secret = substr($hash, 24, 48);
        if (!$dontsave) $this->save();
        return $this->secret;
    }
}
