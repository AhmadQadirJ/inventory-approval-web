<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definisikan Peta Kode Kota (Sesuai Request)
        // Bandung: B, Jakarta: J, Surabaya: S
        $branchMap = [
            'Bandung' => 'B',
            'Jakarta' => 'J',
            'Surabaya' => 'S'
        ];

        // 2. Definisikan Peta Kode Item (Sesuai Request)
        // Non Elektronik: F, Elektronik: E, Ruangan: G
        $categoryMap = [
            'Elektronik'     => 'E',
            'Non Elektronik' => 'F',
            'Ruangan'        => 'G'
        ];

        // ==========================================
        // DATA KATALOG BARANG
        // ==========================================
        $itemCatalog = [
            // --- KATEGORI: ELEKTRONIK ---
            [
                'category' => 'Elektronik',
                'items' => [
                    [
                        'name' => 'Lenovo ThinkPad E14 Gen 4',
                        'brand' => 'Lenovo', 'price' => 16499000, 'vendor' => 'Lenovo Official',
                        'link' => 'https://www.tokopedia.com/find/thinkpad-e14-gen-4',
                        'desc' => 'Laptop bisnis i5-1235U, 8GB RAM untuk staff.'
                    ],
                    [
                        'name' => 'Epson EcoTank L3210',
                        'brand' => 'Epson', 'price' => 2300000, 'vendor' => 'Epson Official',
                        'link' => 'https://www.tokopedia.com/find/printer-epson-l3210',
                        'desc' => 'Printer multifungsi (Print, Scan, Copy) hemat tinta.'
                    ],
                    [
                        'name' => 'Smart TV Samsung 50 Inch 4K',
                        'brand' => 'Samsung', 'price' => 5299000, 'vendor' => 'Samsung Electronics',
                        'link' => 'https://www.blibli.com/jual/samsung-smart-tv-50',
                        'desc' => 'TV Monitor untuk display data dashboard & meeting.'
                    ],
                    [
                        'name' => 'Proyektor Epson EB-X51',
                        'brand' => 'Epson', 'price' => 6885000, 'vendor' => 'Bhinneka',
                        'link' => 'https://www.tokopedia.com/find/eb-x51',
                        'desc' => 'Proyektor XGA 3800 Lumens untuk presentasi.'
                    ]
                ]
            ],
            // --- KATEGORI: NON ELEKTRONIK ---
            [
                'category' => 'Non Elektronik',
                'items' => [
                    [
                        'name' => 'Kursi Kerja Ergotec LX 930',
                        'brand' => 'Ergotec', 'price' => 1811000, 'vendor' => 'Kantorku',
                        'link' => 'https://www.kantorku.co.id/kursi-ergotec',
                        'desc' => 'Kursi staff ergonomis, jaring, hidrolik.'
                    ],
                    [
                        'name' => 'Meja Staff Modera 140cm',
                        'brand' => 'Modera', 'price' => 1221000, 'vendor' => 'Subur Furniture',
                        'link' => 'https://suburfurniture.com/modera-140',
                        'desc' => 'Meja kerja standar 140x70cm warna beech.'
                    ],
                    [
                        'name' => 'Filing Cabinet Lion 4 Laci',
                        'brand' => 'Lion', 'price' => 3300000, 'vendor' => 'Master Kantor',
                        'link' => 'https://www.masterkantor.com/filing-cabinet-lion',
                        'desc' => 'Lemari arsip besi tebal untuk dokumen rahasia.'
                    ],
                    [
                        'name' => 'Whiteboard Magnetic Stand',
                        'brand' => 'Sakana', 'price' => 1485000, 'vendor' => 'Tokojadi',
                        'link' => 'https://www.tokojadi.net/whiteboard',
                        'desc' => 'Papan tulis kaki roda ukuran 120x240cm.'
                    ]
                ]
            ]
        ];

        // ==========================================
        // DATA KATALOG RUANGAN
        // ==========================================
        $roomCatalog = [
            [
                'name' => 'Meeting Room Alpha (Large)',
                'qty' => 1,
                'desc' => 'Ruang rapat utama kapasitas 20 orang. Fasilitas: Projector, AC Central, Sound System.'
            ],
            [
                'name' => 'Meeting Room Beta (Small)',
                'qty' => 2,
                'desc' => 'Ruang diskusi privat kapasitas 4-6 orang. Fasilitas: Whiteboard, Smart TV.'
            ],
            [
                'name' => 'Townhall / Aula Serbaguna',
                'qty' => 1,
                'desc' => 'Ruang besar kapasitas 100 orang untuk acara gathering atau general meeting.'
            ],
            [
                'name' => 'Creative Studio / Podcast Room',
                'qty' => 1,
                'desc' => 'Ruangan kedap suara untuk produksi konten kreatif dan rekaman podcast.'
            ]
        ];

        // ==========================================
        // EKSEKUSI LOOPING
        // ==========================================
        foreach ($branchMap as $branchName => $branchCode) {
            
            // A. INSERT BARANG (Elektronik & Non-Elektronik)
            foreach ($itemCatalog as $catGroup) {
                // Ambil Kode Kategori (E atau F)
                $catCode = $categoryMap[$catGroup['category']];

                foreach ($catGroup['items'] as $item) {
                    // GENERATE KODE: [KOTA]-[ITEM]-[000000]
                    // sprintf '%06d' artinya angka dipad dengan nol sampai 6 digit
                    $generatedKode = sprintf('%s-%s-%06d', $branchCode, $catCode, rand(1, 999999));

                    Inventory::create([
                        'kode'          => $generatedKode,
                        'nama'          => $item['name'], // Sesuaikan dengan kolom migration Anda (nama/nama_barang)
                        'kategori'      => $catGroup['category'],
                        'branch'        => $branchName,
                        'brand'         => $item['brand'],
                        'harga'         => $item['price'],
                        'tahun_beli'    => rand(2022, 2024),
                        'nama_vendor'   => $item['vendor'],
                        'vendor_link'   => $item['link'],
                        'qty'           => rand(10, 50),
                        'deskripsi'     => $item['desc'],
                        'gambar'        => null,
                    ]);
                }
            }

            // B. INSERT RUANGAN
            // Ambil Kode Kategori Ruangan (G)
            $catCode = $categoryMap['Ruangan'];

            foreach ($roomCatalog as $room) {
                // GENERATE KODE RUANGAN
                $generatedKode = sprintf('%s-%s-%06d', $branchCode, $catCode, rand(1, 999999));

                Inventory::create([
                    'kode'          => $generatedKode,
                    'nama'          => $room['name'],
                    'kategori'      => 'Ruangan',
                    'branch'        => $branchName,
                    'qty'           => $room['qty'],
                    'deskripsi'     => $room['desc'] . " (Lokasi: Cabang {$branchName})",
                    'gambar'        => null,
                    // Field NULLABLE
                    'brand'         => null,
                    'harga'         => null,
                    'tahun_beli'    => null,
                    'nama_vendor'   => null,
                    'vendor_link'   => null,
                ]);
            }
        }
    }
}