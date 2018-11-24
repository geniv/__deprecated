<?php declare(strict_types=1);

namespace Comgate;

use Comgate\Exception\InvalidArgumentException;
use Comgate\Exception\LabelTooLongException;
use Comgate\Request\CreatePayment;
use Nette\Application\UI\Control;
use Nette\Utils\Callback;


/**
 * Class ComgatePaymentControl
 *
 * @author  geniv
 * @package Comgate
 */
class ComgatePaymentControl extends Control
{
    /** @var callable, signature: function(ComgatePaymentControl $control, CreatePaymentResponse $response): bool */
    public $onCheckout = [];
    /** @var callable, signature: function(ComgatePaymentControl $control, $transId, $status): bool */
    public $onStatus = [];
    /** @var callable, signature: function (ComgatePaymentControl $control, $id, $refId) */
    public $onResult = [];
    /** @var callable, signature: function (ComgatePaymentControl $control, Exception $exception) */
    public $onError = [];

    private $comgate, $createPayment;


    //TODO dodelat setTemplatePath(?) z implementace


    /**
     * ComgatePaymentControl constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function setComgate(Comgate $comgate, CreatePayment $createPayment)
    {
        $this->comgate = $comgate;
        $this->createPayment = $createPayment;
    }


    public function handleCheckout()
    {
        try {
            $this->createPayment->setPrepareOnly(true);

            $response = $this->comgate->sendResponse($this->createPayment);
            if ($response->isOk()) {
                $result = Callback::invokeSafe($this->onCheckout, [$this, $response], null);
                if ($result) {
                    $this->getPresenter()->redirectUrl($response->getRedirectUrl());
                }
            }
        } catch (InvalidArgumentException $e) {
            $this->errorHandler($e);
        } catch (LabelTooLongException $e) {
            $this->errorHandler($e);
        }
    }


    public function handleStatus()
    {
        $this->getPresenter()->getHttpResponse()->setContentType('application/javascript');
        //!!!! in startup must be condition for exclusion redirect on start this presenter !!!!
        $request = $this->getPresenter()->request;

        if ($request->isMethod('POST')) {
            $transId = $request->getPost('transId');
            $status = $request->getPost('status');

            if ($transId && $status) {
                $result = Callback::invokeSafe($this->onStatus, [$this, $transId, $status], null);
                if ($result) {
                    die('code=0&message=OK');
                } else {
                    die('code=1&message=STATUS_ERROR');
                }
            }
            die('code=1&message=MISSING_PARAMETERS');
        }
        die('code=1&message=FAIL');
    }


    public function handleResult($id, $refId)
    {
        $this->onResult($this, $id, $refId);
    }


    private function errorHandler(\Exception $exception)
    {
        if (!$this->onError) {
            throw $exception;
        }

        $this->onError($this, $exception);
    }
}








public function createComponentComgatePaymentButton(Comgate $comgate): ComgatePaymentControl
{
    $control = new ComgatePaymentControl();

    $paymentLabel = $this->translator->translate('kosik-summary-payment-label');
    $orderControl = $this->sessionOrder->orderControl;

    $idOrder = $this->sessionOrder->idOrder ?? 0;

    if ($idOrder) {
        $order = $this->orderModel->getOrder($idOrder);
        $payment = $comgate->createPayment((int) $orderControl->getPriceTotal(true) * 100, $order['order_id'], $order['email'], $paymentLabel);
        $control->setComgate($comgate, $payment);

        $control->onCheckout = function (ComgatePaymentControl $control, CreatePaymentResponse $response) use ($idOrder) {
            return $this->orderModel->editOrder($idOrder, ['checkout_id' => $response->getTransId()]);
        };
    }

    $control->onStatus = function (ComgatePaymentControl $control, $transId, $status) {
        $item = $this->orderModel->getOrderByCheckoutId($transId);
        if ($item) {
            return $this->orderModel->editOrder((int) $item['id'], ['status' => ($status == PaymentStatus::PAID ? Order::STATUS_PAYMENT_SUCCESS : Order::STATUS_PAYMENT_ERROR), 'checkout_date%sql' => 'NOW()']);
        }
        return false;
    };

    $control->onResult[] = function (ComgatePaymentControl $control, $id, $refId) {
        $item = $this->orderModel->getOrderByCheckoutId($id);
        if ($item) {
            if ($item['status'] == Order::STATUS_PAYMENT_SUCCESS) {
                $this->redirect('success');
            } else {
                $this->redirect('error');
            }
        }
        $this->redirect('Homepage:');
    };
    return $control;
}
