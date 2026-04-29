<?php

namespace App\Http\Controllers\API\Customers;

use App\Http\Controllers\Controller;
use App\Repositories\Customer\CustomerRepositoryInterface;
use Illuminate\Http\Request;

class CustomerAPIController extends Controller
{
    //
    protected $cusRepo;
    public function __construct(CustomerRepositoryInterface $cusRepo)
    {
        $this->cusRepo = $cusRepo;
    }

    public function getCustomerDataByUserApp()
    {
        $customer = $this->cusRepo->customerProfileData();
    }

    public function updateCustomerAddress(int $id, Request $request)
    {
        $customer = $this->cusRepo->customerAddressUpdate($id, $request->all());
    }

    public function createCustomerAddress(Request $request)
    {
        $customer = $this->cusRepo->createCustomerAddress($request->all());
    }

    public function defaultCustomerAddress(int $id)
    {
        $customer = $this->cusRepo->defaultCustomerAddress($id);
    }

    public function customerProfileEdit(Request $request)
    {
        $customer = $this->cusRepo->customerProfileEdit($request->all());
    }

    public function getCustomerAddressByUserApp()
    {
        $customerAddress = $this->cusRepo->customerAddressList();
    }

    public function deleteCustomerAddress(int $id)
    {
        $customerAddress = $this->cusRepo->deleteCustomerAddress($id);
    }

    public function changePhoneNumberOTP(Request $request)
    {
        $customer = $this->cusRepo->changePhoneNumberOTP($request);
    }

    public function changePhoneNumber(Request $request)
    {
        $customer = $this->cusRepo->changePhone($request);
    }
}
