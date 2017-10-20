<?php


class RS384 extends RSA
{
    public function __construct() {
        parent::__construct();
        $this->encryptionAlgorithm->setHash('sha384');
        $this->encryptionAlgorithm->setMGFHash('sha384');
    }
	/* (non-PHPdoc)
     * @see PublicKey::getHashingAlgorithm()
     */
    protected function getHashingAlgorithm()
    {
        // TODO Auto-generated method stub
        
    }

}
