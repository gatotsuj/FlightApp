<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\FlightClass;
use App\Models\PromoCode;
use App\Models\Transaction;
use App\Models\TransactionPassenger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getTransactionDataFromSession()
    {
        return session()->get('transaction');
    }

    public function saveTransactionDataToSession($data)
    {
        $transaction = session()->get('transaction', []);

        foreach ($data as $key => $value) {
            $transaction[$key] = $value;
        }

        session()->put('transaction', $transaction);
    }

    public function saveTransaction($data)
    {
        return DB::transaction(function () use ($data) {

            $data['code'] = $this->generateTransactionCode();
            $data['number_of_passengers'] = $this->countPassengers($data['passengers']);

            $data['subtotal'] = $this->calculateSubtotal(
                $data['flight_class_id'],
                $data['number_of_passengers']
            );

            $data['grandtotal'] = $data['subtotal'];

            if (! empty($data['promo_code'])) {
                $data = $this->applyPromoCode($data);
            }

            $data['grandtotal'] = $this->addPPN($data['grandtotal']);

            $transaction = $this->createTransaction($data);
            $this->savePassengers($data['passengers'], $transaction->id);

            session()->forget('transaction');

            return $transaction;
        });
    }

    public function getTransactionByCode($code)
    {
        return Transaction::where('code', $code)->first();
    }

    public function getTransactionByCodeEmailPhone($code, $email, $phone)
    {
        return Transaction::where('code', $code)
            ->where('customer_email', $email)
            ->where('customer_phone', $phone)
            ->first();
    }

    private function generateTransactionCode()
    {
        return 'TXT-FL-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
    }

    private function countPassengers(array $passengers)
    {
        return count($passengers);
    }

    private function calculateSubtotal($flightClassId, $numberOfPassengers)
    {
        $price = FlightClass::findOrFail($flightClassId)->price;

        return $price * $numberOfPassengers;
    }

    private function applyPromoCode($data)
    {
        $promo = PromoCode::where('code', $data['promo_code'])
            ->where('valid_until', '>=', now())
            ->where('is_used', false)
            ->first();

        if (! $promo) {
            return $data;
        }

        if ($promo->discount_type === 'percentage') {
            $data['discount'] = $data['grandtotal'] * ($promo->discount / 100);
        } else {
            $data['discount'] = $promo->discount;
        }

        $data['grandtotal'] -= $data['discount'];
        $data['promo_code_id'] = $promo->id;

        $promo->update(['is_used' => true]);

        return $data;
    }

    private function addPPN($grandTotal)
    {
        $ppn = $grandTotal * 0.11; // 11% PPN

        return $grandTotal + $ppn;
    }

    private function createTransaction($data)
    {
        return Transaction::create([
            'code' => $data['code'],
            'flight_class_id' => $data['flight_class_id'],
            'number_of_passengers' => $data['number_of_passengers'],
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'] ?? 0,
            'grandtotal' => $data['grandtotal'],
            'promo_code_id' => $data['promo_code_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
        ]);
    }

    private function savePassengers(array $passengers, $transactionId)
    {
        foreach ($passengers as $passenger) {
            TransactionPassenger::create([
                'transaction_id' => $transactionId,
                'name' => $passenger['name'],
                'age' => $passenger['age'],
                'passport_number' => $passenger['passport_number'],
            ]);
        }
    }
}
