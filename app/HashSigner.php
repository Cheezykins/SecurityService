<?php


namespace App;


use Illuminate\Support\Facades\Redis;

class HashSigner
{

    const HASH_DIVIDER = '|';
    protected $hash;
    protected $reference;
    protected $secret;

    /**
     * HashSigner constructor.
     * @param string|null $hash
     */
    public function __construct($hash = null)
    {
        if ($hash !== null) {
            $this->setHash($hash);
        }
    }

    /**
     * @param string $password
     * @return HashSigner
     */
    public static function create($password)
    {
        $secret = \base64_encode(\random_bytes(32));
        $hash = \hash_hmac('sha256', $password, $secret);
        $hash = \password_hash($hash, PASSWORD_BCRYPT, ['cost' => 12]);

        $reference = Redis::spop('hashes');

        if ($reference === null) {
            throw new \LogicException('Hash Error');
        }

        Redis::set('hash_' . $reference, $secret);
        return new self($hash . self::HASH_DIVIDER . $reference);
    }

    /**
     *
     */
    public function invalidate()
    {
        Redis::del('hash_' . $this->reference);
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash . self::HASH_DIVIDER . $this->reference;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        if (!$this->validateHash($hash)) {
            throw new \InvalidArgumentException('Invalid Hash');
        }

        list($hash, $reference) = \explode(self::HASH_DIVIDER, $hash);
        $this->hash = $hash;
        $this->reference = $reference;
        $secret = Redis::get('hash_' . $reference);

        if ($secret === null) {
            throw new \InvalidArgumentException('Invalid Hash');
        }
        $this->secret = $secret;
    }

    /**
     * @param string $pass
     * @return bool
     */
    public function validate($pass)
    {
        $checkHash = \hash_hmac('sha256', $pass, $this->secret);
        return \password_verify($checkHash, $this->hash);
    }

    /**
     * @param string $hash
     * @return bool
     */
    protected function validateHash($hash)
    {
        if (preg_match('/^.+\|[a-zA-Z0-9]+$/', $hash) == 1) {
            return true;
        }
        return false;
    }

}