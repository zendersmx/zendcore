<?php

class RSA extends PublicKey
{
    public function __construct() {
        $this->encryptionAlgorithm = new Crypt_RSA();
    }
	/* (non-PHPdoc)
     * @see PublicKey::getHashingAlgorithm()
     */
    protected function getHashingAlgorithm()
    {
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see PublicKey::getSupportedPrivateKeyType()
     */
    protected function getSupportedPrivateKeyType()
    {
        // TODO Auto-generated method stub
        
    }

}
