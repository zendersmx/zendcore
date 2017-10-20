<?php


class RS512 extends RSA
{
    public function __construct() {
        parent::__construct();
        $this->encryptionAlgorithm->setHash('sha512');
        $this->encryptionAlgorithm->setMGFHash('sha512');
    }
	/* (non-PHPdoc)
     * @see PublicKey::getHashingAlgorithm()
     */
    protected function getHashingAlgorithm()
    {
        // TODO Auto-generated method stub
        
    }

}
