<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 * Class contains api test cases for "civicrm_payment_processor"
 *
 * @group headless
 */
class api_v3_PaymentProcessorTest extends CiviUnitTestCase {
  protected $_paymentProcessorType;
  protected $_apiversion = 3;
  protected $_params;

  public function setUp() {
    parent::setUp();
    $this->useTransaction(TRUE);
    // Create dummy processor
    $params = array(
      'name' => 'API_Test_PP_Type',
      'title' => 'API Test Payment Processor Type',
      'class_name' => 'CRM_Core_Payment_APITest',
      'billing_mode' => 'form',
      'is_recur' => 0,
    );
    $result = $this->callAPISuccess('payment_processor_type', 'create', $params);
    $this->_paymentProcessorType = $result['id'];
    $this->_params = array(
      'name' => 'API Test PP',
      'payment_processor_type_id' => $this->_paymentProcessorType,
      'class_name' => 'CRM_Core_Payment_APITest',
      'is_recur' => 0,
      'domain_id' => 1,
    );
  }

  /**
   * Check with no name.
   */
  public function testPaymentProcessorCreateWithoutName() {
    $payProcParams = array(
      'is_active' => 1,
    );
    $this->callAPIFailure('payment_processor', 'create', $payProcParams);
  }

  /**
   * Create payment processor.
   */
  public function testPaymentProcessorCreate() {
    $params = $this->_params;
    $result = $this->callAPIAndDocument('payment_processor', 'create', $params, __FUNCTION__, __FILE__);
    $this->assertNotNull($result['id']);
    $this->assertDBState('CRM_Financial_DAO_PaymentProcessor', $result['id'], $params);
    return $result['id'];
  }

  /**
   * Test  using example code.
   */
  public function testPaymentProcessorCreateExample() {
    require_once 'api/v3/examples/PaymentProcessor/Create.php';
    $result = payment_processor_create_example();
    $expectedResult = payment_processor_create_expectedresult();
    $this->assertAPISuccess($result);
  }

  /**
   * Check payment processor delete.
   */
  public function testPaymentProcessorDelete() {
    $id = $this->testPaymentProcessorCreate();
    $params = array(
      'id' => $id,
    );

    $this->callAPIAndDocument('payment_processor', 'delete', $params, __FUNCTION__, __FILE__);
  }

  /**
   * Check with valid params array.
   */
  public function testPaymentProcessorsGet() {
    $params = $this->_params;
    $params['user_name'] = 'test@test.com';
    $this->callAPISuccess('payment_processor', 'create', $params);

    $params = array(
      'user_name' => 'test@test.com',
    );
    $results = $this->callAPISuccess('payment_processor', 'get', $params);

    $this->assertEquals(1, $results['count']);
    $this->assertEquals('test@test.com', $results['values'][$results['id']]['user_name']);
  }

}
