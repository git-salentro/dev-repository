<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PropertyBundle\Entity\PropertyRentHistory;
use Erp\PropertyBundle\Entity\Property;
use Erp\StripeBundle\Entity\Invoice;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Stripe\BankAccount;
use Stripe\Card;

//TODO Refactor preparing chart data
class DashboardController extends BaseController
{
    public function dashboardAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('ErpUserBundle:Dashboard:index.html.twig', [
            'user' => $user,
        ]);
    }

    public function showPropertiesAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 months');
        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);

        $propertyRentHistoryRepo = $this->getDoctrine()->getManagerForClass(PropertyRentHistory::class)->getRepository(PropertyRentHistory::class);
        $history = $propertyRentHistoryRepo->getHistory($user, $sixMonthsAgo, $now);

        $availableProperties = [];
        $rentedProperties = [];
        /** @var PropertyRentHistory $record */
        foreach ($history as $record) {
            $interval = $record->getCreatedAt()->format('Y-n');
            switch ($record->getStatus()) {
                case Property::STATUS_DRAFT:
                case Property::STATUS_AVAILABLE:
                    $availableProperties[$interval] = isset($availableProperties[$interval]) ? ++$availableProperties[$interval] : 1;
                    break;
                case Property::STATUS_RENTED:
                    $rentedProperties[$interval] = isset($rentedProperties[$interval]) ? ++$rentedProperties[$interval] : 1;
                    break;
            }
        }

        $intervals = array_keys($labels);
        $labels = array_values($labels);

        $preparedAvailableProperties = [];
        $preparedRentedProperties = [];
        foreach ($intervals as $interval) {
            $preparedAvailableProperties[] = isset($availableProperties[$interval]) ? $availableProperties[$interval] : 0;
            $preparedRentedProperties[] = isset($rentedProperties[$interval]) ? $rentedProperties[$interval] : 0;
        }

        return $this->render('ErpUserBundle:Dashboard:properties_history.html.twig', [
            'available_properties' => $preparedAvailableProperties,
            'rented_properties' => $preparedRentedProperties,
            'labels' => $labels,
            'intervals' =>$intervals,
        ]);
    }

    public function showCashflowsAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $items = [];
        if ($stripeAccount) {
            $transactionRepo = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $items = $transactionRepo->getGroupedTransactions($stripeAccount, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $months = array_keys($labels);
        $labels = array_values($labels);
        $cashIn = $this->getPreparedItems($items, $months);
        $cashOut =  $this->getPreparedItems($items, $months);

        return $this->render('ErpUserBundle:Dashboard:cashflows.html.twig', [
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'labels' => $labels,
        ]);
    }

    public function showInvoicesAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $items = [];
        if ($stripeAccount) {
            $invoicesRepo = $this->getDoctrine()->getManagerForClass(Invoice::class)->getRepository(Invoice::class);
            $items = $invoicesRepo->getGroupedInvoices($stripeAccount, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $months = array_keys($labels);
        $labels = array_values($labels);
        $invoices = $this->getPreparedItems($items, $months);

        return $this->render('ErpUserBundle:Dashboard:invoices.html.twig', [
            'labels' => $labels,
            'invoices' => $invoices,
        ]);
    }

    public function showTransactionsAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $items = [];
        if ($stripeAccount) {
            $transactionRepo = $this->getDoctrine()->getManagerForClass(Invoice::class)->getRepository(Transaction::class);
            $items = $transactionRepo->getGroupedTransactions($stripeAccount, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $months = array_keys($labels);
        $labels = array_values($labels);
        $transactions = $this->getPreparedItems($items, $months);

        return $this->render('ErpUserBundle:Dashboard:transactions.html.twig', [
            'transactions' => $transactions,
            'labels' => $labels,
        ]);
    }

    public function showPaymentDetailsAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        //TODO Add cache layer (APC or Doctrine)
        $stripeUserManager = $this->get('erp.payment.stripe.manager.user_manager');
        /** @var BankAccount $bankAccount */
        $bankAccount = $stripeUserManager->getBankAccount($user);
        /** @var Card $creditCard */
        $creditCard = $stripeUserManager->getCreditCard($user);

        return $this->render(
            'ErpPaymentBundle:Stripe/Widgets:payment-details.html.twig',
            [
                'creditCard' => $creditCard,
                'bankAccount' => $bankAccount,
            ]
        );
    }

    private function getMonthsLabels(\DateTime $dateFrom, \DateTime $dateTo)
    {
        $dateFrom = \DateTimeImmutable::createFromMutable($dateFrom);
        $dateTo = \DateTimeImmutable::createFromMutable($dateTo);

        $diff = $dateFrom->diff($dateTo);
        $count = ($diff->format('%y') * 12) + $diff->format('%m') +1;

        $labels = [];
        for ($i=1; $i<=$count; $i++) {
            $labels[$dateFrom->format('Y-n')] = $dateFrom->format('F');
            $dateFrom = $dateFrom->modify('+1 month');
        }

        return $labels;
    }

    private function getPreparedItems(array $items, array $months)
    {
        $results = [];
        $existingMonth = array_column($items, 'gMonth');
        foreach ($months as $month) {
            if (false !== $key = array_search($month, $existingMonth)) {
                $results[] = $items[$key]['gAmount'];
            } else {
                $results[] = 0;
            }
        }

        return $results;
    }
}
