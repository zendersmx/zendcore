<?php


class RS256 extends RSA 
{
    public function __construct() {
        parent::__construct();
        $this->encryptionAlgorithm->setHash('sha256');
        $this->encryptionAlgorithm->setMGFHash('sha256');
    }
	/* (non-PHPdoc)
     * @see PublicKey::getHashingAlgorithm()
     */
    protected function getHashingAlgorithm()
    {
        // TODO Auto-generated method stub
        
    }

}
