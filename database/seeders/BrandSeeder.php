<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // cloth brands

        $brand = [
            [
                'name' => 'Zara',
                'slug' => 'zara',
                'description' => 'Zara is a Spanish apparel retailer based in Arteixo in Galicia. The company specializes in fast fashion, and products include clothing, accessories, shoes, swimwear, beauty, and perfumes.',
                'logo' => 'zara.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ],
            [
                'name' => 'H&M',
                'slug' => 'h-m',
                'description' => 'Hennes & Mauritz AB is a Swedish multinational clothing-retail company known for its fast-fashion',
                'logo' => 'h-m.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ],
            [
                'name' => 'Uniqlo',
                'slug' => 'uniqlo',
                'description' => 'Uniqlo Co., Ltd. is a Japanese casual wear designer, manufacturer and retailer.',
                'logo' => 'uniqlo.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ],
            [
                'name' => 'Levi\'s',
                'slug' => 'levis',
                'description' => 'Levi Strauss & Co. is an American clothing company known worldwide for its Levi\'s brand of denim jeans.',
                'logo' => 'levis.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Nike, Inc. is an American multinational corporation that is engaged in the design, development, manufacturing, and worldwide marketing and sales of footwear, apparel, equipment, accessories, and services.',
                'logo' => 'nike.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Adidas AG is a German multinational corporation, founded and headquartered in Herzogenaurach, Germany, that designs and manufactures shoes, clothing, and accessories.',
                'logo' => 'adidas.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ],
            [
                'name' => 'Puma',
                'slug' => 'puma',
                'description' => 'Puma SE, branded as Puma, is a German multinational corporation that designs and manufactures athletic and casual footwear, apparel and accessories.',
                'logo' => 'puma.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'Converse',
                'slug' => 'converse',
                'description' => 'Converse is an American shoe company that designs, distributes, and licenses sneakers, skating shoes, lifestyle brand footwear, apparel, and accessories.',
                'logo' => 'converse.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'Vans',
                'slug' => 'vans',
                'description' => 'Vans is an American manufacturer of skateboarding shoes and related apparel, based in Santa Ana, California and owned by VF Corporation.',
                'logo' => 'vans.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'New Balance',
                'slug' => 'new-balance',
                'description' => 'New Balance Athletics, Inc. is an American multinational corporation based in Boston, Massachusetts.',
                'logo' => 'new-balance.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'Reebok',
                'slug' => 'reebok',
                'description' => 'Reebok International Limited is an American-inspired global brand with a deep fitness heritage and a clear mission: To be the best fitness brand in the world.',
                'logo' => 'reebok.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'Under Armour',
                'slug' => 'under-armour',
                'description' => 'Under Armour, Inc. is an American sports equipment company that manufactures footwear, sports and casual apparel.',
                'logo' => 'under-armour.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'The North Face',
                'slug' => 'the-north-face',
                'description' => 'The North Face is an American outdoor recreation product company.',
                'logo' => 'the-north-face.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'Columbia',
                'slug' => 'columbia',
                'description' => 'Columbia Sportswear Company is a company that produces and distributes outerwear, sportswear, and footwear, as well as headgear, camping equipment, ski apparel, and outerwear accessories.',
                'logo' => 'columbia.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ], [
                'name' => 'Patagonia',
                'slug' => 'patagonia',
                'description' => 'Patagonia, Inc. is an American clothing company that markets and sells outdoor clothing.',
                'logo' => 'patagonia.jpg',
                'created_by' => '1',
                'updated_by' => '1',
            ]

        ];

        Brand::Insert($brand);

    }
}
