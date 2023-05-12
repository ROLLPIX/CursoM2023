<?php
namespace Rollpix\CustomerRegistration\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Psr\Log\LoggerInterface;

class AccountManagementPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AccountManagementPlugin constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function aroundCreateAccount(
        AccountManagementInterface $subject,
        \Closure $proceed,
        $customer,
        $password = null,
        $redirectUrl = ''
    ) {

        // Seteamos las variables
        $firstName  = $customer->getFirstname();
        $middleName = $customer->getMiddlename();

        // Concatena el primer y segundo nombre y se blanque el campo del segundo nombre
        // Esto ocurre si tiene seguno nombre
        if(isset($middleName)){
            $fullName = $firstName . '-' . $middleName;
            $customer->setFirstname($fullName);
            $customer->setMiddlename('');
        }else{
            $fullName = $firstName;
        }

        // Se registra el en un log
        $this->logger->info('Se ha dado de alta el cliente: ' . $fullName);

        // Se llama a la funcion original con los datos modificados
        return $proceed($customer, $password, $redirectUrl);
    }
}
