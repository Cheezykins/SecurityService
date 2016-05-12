<?php


namespace App\Helpers;


class IncrementalString
{
    const DEFAULT_ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    protected $alphabet;
    protected $current;
    protected $alphabetLength;
    protected $position;

    /**
     * IncrementalString constructor.
     * @param string $start
     * @param string|null $alphabet
     */
    public function __construct($start = '', $alphabet = null)
    {
        if ($alphabet === null) {
            $alphabet = self::DEFAULT_ALPHABET;
        }
        $this->setAlphabet($alphabet);
        $this->setStartString($start);
    }

    /**
     * @param string $alphabet
     */
    public function setAlphabet($alphabet)
    {
        $this->alphabet = \str_split($alphabet);
        $this->alphabetLength = \count($this->alphabet);
    }

    /**
     * @param string $start
     */
    public function setStartString($start)
    {
        if ($start === '') {
            $start = $this->alphabet[0];
        }
        $this->current = \str_split($start);
        $this->setPosition();
    }

    /**
     *
     */
    protected function setPosition()
    {
        $this->position = \array_search(end($this->current), $this->alphabet);
    }

    /**
     * @param array|null $array
     * @return array
     */
    protected function incrementArray($array)
    {
        $length = \count($array);

        if ($length == 0) {
            return [$this->alphabet[0]];
        }

        $value = \array_pop($array);
        $key = \array_search($value, $this->alphabet);
        $key++;
        if (!array_key_exists($key, $this->alphabet)) {
            $key = 0;
            $array = $this->incrementArray($array);
        }
        $array[] = $this->alphabet[$key];
        return $array;
    }

    public function increment()
    {
        $this->current = $this->incrementArray($this->current);
    }

    public function padOutput($length)
    {
        return \str_pad(\implode('', $this->current), $length, $this->alphabet[0], STR_PAD_LEFT);
    }


    public function __toString()
    {
        return \implode('', $this->current);
    }

}