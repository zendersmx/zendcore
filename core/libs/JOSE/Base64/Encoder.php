<?php

interface Encoder
{

    /**
     * @param string $data
     * @return string
     */
    public function encode($data);

    /**
     * @param string $data
     * @return string
     */
    public function decode($data);

}
