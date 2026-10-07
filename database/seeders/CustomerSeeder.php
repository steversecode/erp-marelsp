<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Account\Database\Seeders\AccountSeeder;
use Webkul\Account\Enums\AccountType as AccountTypeEnum;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Partner;
use Webkul\Partner\Enums\AccountType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();
        $user = User::first();

        // Pastikan akun default tersedia. Jika belum ada, jalankan AccountSeeder terlebih dahulu.
        $receivableAccount = Account::where('account_type', AccountTypeEnum::ASSET_RECEIVABLE)->first();
        $payableAccount = Account::where('account_type', AccountTypeEnum::LIABILITY_PAYABLE)->first();

        if (! $receivableAccount || ! $payableAccount) {
            $this->call(AccountSeeder::class);

            $receivableAccount = Account::where('account_type', AccountTypeEnum::ASSET_RECEIVABLE)->first();
            $payableAccount = Account::where('account_type', AccountTypeEnum::LIABILITY_PAYABLE)->first();
        }

        $customers = [
            [
                'name'         => 'PT Cahaya Prima Abadi',
                'account_type' => AccountType::COMPANY,
                'email'        => 'sales@cahayaprima.co.id',
                'phone'        => '021-5551234',
                'mobile'       => '081234567890',
                'street1'      => 'Jl. Industri Raya No. 45',
                'city'         => 'Jakarta Barat',
                'zip'          => '11730',
            ],
            [
                'name'         => 'CV Berkah Mandiri Perkasa',
                'account_type' => AccountType::COMPANY,
                'email'        => 'info@berkahmandiri.com',
                'phone'        => '022-7772345',
                'mobile'       => '081298765432',
                'street1'      => 'Jl. Soekarno Hatta No. 120',
                'city'         => 'Bandung',
                'zip'          => '40286',
            ],
            [
                'name'         => 'PT Sinar Surya Kencana',
                'account_type' => AccountType::COMPANY,
                'email'        => 'purchasing@sinarsurya.co.id',
                'phone'        => '031-8883456',
                'mobile'       => '081311223344',
                'street1'      => 'Kawasan Rungkut Industri III No. 8',
                'city'         => 'Surabaya',
                'zip'          => '60293',
            ],
            [
                'name'         => 'Toko Sentosa Makmur',
                'account_type' => AccountType::COMPANY,
                'email'        => 'sentosamakmur@gmail.com',
                'phone'        => '024-6664567',
                'mobile'       => '081322334455',
                'street1'      => 'Jl. Pemuda No. 88',
                'city'         => 'Semarang',
                'zip'          => '50132',
            ],
            [
                'name'         => 'PT Mitra Logistik Nusantara',
                'account_type' => AccountType::COMPANY,
                'email'        => 'procurement@mitralogistik.com',
                'phone'        => '021-8905678',
                'mobile'       => '081333445566',
                'street1'      => 'Jl. Gatot Subroto Kav. 18',
                'city'         => 'Jakarta Selatan',
                'zip'          => '12930',
            ],
            [
                'name'         => 'Budi Santoso',
                'account_type' => AccountType::INDIVIDUAL,
                'email'        => 'budi.santoso@gmail.com',
                'phone'        => '021-7890123',
                'mobile'       => '081512345678',
                'street1'      => 'Jl. Flamboyan No. 12',
                'city'         => 'Tangerang',
                'zip'          => '15111',
            ],
            [
                'name'         => 'Siti Rahmawati',
                'account_type' => AccountType::INDIVIDUAL,
                'email'        => 'siti.rahmawati@yahoo.com',
                'phone'        => '021-6543210',
                'mobile'       => '081623456789',
                'street1'      => 'Jl. Melati Blok C No. 5',
                'city'         => 'Bekasi',
                'zip'          => '17145',
            ],
            [
                'name'         => 'Hendra Wijaya',
                'account_type' => AccountType::INDIVIDUAL,
                'email'        => 'hendra.wijaya@outlook.com',
                'phone'        => '031-7654321',
                'mobile'       => '081734567890',
                'street1'      => 'Jl. Dharmahusada Indah No. 22',
                'city'         => 'Surabaya',
                'zip'          => '60115',
            ],
            [
                'name'         => 'Dewi Anggraini',
                'account_type' => AccountType::INDIVIDUAL,
                'email'        => 'dewi.anggraini@gmail.com',
                'phone'        => '0274-512345',
                'mobile'       => '081845678901',
                'street1'      => 'Jl. Kaliurang Km 5.5',
                'city'         => 'Sleman',
                'zip'          => '55281',
            ],
            [
                'name'         => 'Agus Setiawan',
                'account_type' => AccountType::INDIVIDUAL,
                'email'        => 'agus.setiawan@gmail.com',
                'phone'        => '022-8765432',
                'mobile'       => '081956789012',
                'street1'      => 'Jl. Dago Asri No. 17',
                'city'         => 'Bandung',
                'zip'          => '40135',
            ],
        ];

        foreach ($customers as $data) {
            Partner::updateOrCreate(
                [
                    'email' => $data['email'],
                ],
                [
                    'name'                           => $data['name'],
                    'account_type'                   => $data['account_type'],
                    'phone'                          => $data['phone'],
                    'mobile'                         => $data['mobile'],
                    'street1'                        => $data['street1'],
                    'city'                           => $data['city'],
                    'zip'                            => $data['zip'],
                    'customer_rank'                  => 1,
                    'supplier_rank'                  => 0,
                    'company_id'                     => $company?->id,
                    'creator_id'                     => $user?->id,
                    'user_id'                        => $user?->id,
                    'property_account_receivable_id' => $receivableAccount?->id,
                    'property_account_payable_id'    => $payableAccount?->id,
                ]
            );
        }
    }
}
