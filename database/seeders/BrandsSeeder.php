<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultBrands = [
            [
                'name' => 'AITO',
                'slug' => 'aito',
            ],
            [
                'name' => 'Ambertruck',
                'slug' => 'ambertruck',
            ],
            [
                'name' => 'Aurus',
                'slug' => 'aurus',
            ],
            [
                'name' => 'Avatr',
                'slug' => 'avatr',
            ],
            [
                'name' => 'Avior',
                'slug' => 'avior',
            ],
            [
                'name' => 'BAIC',
                'slug' => 'baic',
            ],
            [
                'name' => 'BAW',
                'slug' => 'baw',
            ],
            [
                'name' => 'Belgee',
                'slug' => 'belgee',
            ],
            [
                'name' => 'Bestune',
                'slug' => 'bestune',
            ],
            [
                'name' => 'BYD',
                'slug' => 'byd',
            ],
            [
                'name' => 'Changan',
                'slug' => 'changan',
            ],
            [
                'name' => 'Chery',
                'slug' => 'chery',
            ],
            [
                'name' => 'Deepal',
                'slug' => 'deepal',
            ],
            [
                'name' => 'DFSK',
                'slug' => 'dfsk',
            ],
            [
                'name' => 'Dongfeng',
                'slug' => 'dongfeng',
            ],
            [
                'name' => 'ENA Auto',
                'slug' => 'ena-auto',
            ],
            [
                'name' => 'Esteo',
                'slug' => 'esteo',
            ],
            [
                'name' => 'Evolute',
                'slug' => 'evolute',
            ],
            [
                'name' => 'EXEED',
                'slug' => 'exeed',
            ],
            [
                'name' => 'Exlantix',
                'slug' => 'exlantix',
            ],
            [
                'name' => 'FAW',
                'slug' => 'faw',
            ],
            [
                'name' => 'Forthing',
                'slug' => 'forthing',
            ],
            [
                'name' => 'Foton',
                'slug' => 'foton',
            ],
            [
                'name' => 'GAC',
                'slug' => 'gac',
            ],
            [
                'name' => 'Geely',
                'slug' => 'geely',
            ],
            [
                'name' => 'Great Wall',
                'slug' => 'great-wall',
            ],
            [
                'name' => 'Haima',
                'slug' => 'haima',
            ],
            [
                'name' => 'Haval',
                'slug' => 'haval',
            ],
            [
                'name' => 'Hongqi',
                'slug' => 'hongqi',
            ],
            [
                'name' => 'Huanghai',
                'slug' => 'huanghai',
            ],
            [
                'name' => 'iCaur',
                'slug' => 'icaur',
            ],
            [
                'name' => 'Iran Khodro',
                'slug' => 'iran-khodro',
            ],
            [
                'name' => 'JAC',
                'slug' => 'jac',
            ],
            [
                'name' => 'Jaecoo',
                'slug' => 'jaecoo',
            ],
            [
                'name' => 'Jeland',
                'slug' => 'jeland',
            ],
            [
                'name' => 'Jetour',
                'slug' => 'jetour',
            ],
            [
                'name' => 'Jetta',
                'slug' => 'jetta',
            ],
            [
                'name' => 'JMC',
                'slug' => 'jmc',
            ],
            [
                'name' => 'Kaiyi',
                'slug' => 'kaiyi',
            ],
            [
                'name' => 'KGM',
                'slug' => 'kgm',
            ],
            [
                'name' => 'Knewstar',
                'slug' => 'knewstar',
            ],
            [
                'name' => 'LADA',
                'slug' => 'lada',
            ],
            [
                'name' => 'Li Auto',
                'slug' => 'li-auto',
            ],
            [
                'name' => 'Livan',
                'slug' => 'livan',
            ],
            [
                'name' => 'Luxeed',
                'slug' => 'luxeed',
            ],
            [
                'name' => 'M-Hero',
                'slug' => 'm-hero',
            ],
            [
                'name' => 'Maxus',
                'slug' => 'maxus',
            ],
            [
                'name' => 'MG',
                'slug' => 'mg',
            ],
            [
                'name' => 'NIO',
                'slug' => 'nio',
            ],
            [
                'name' => 'Nordcross',
                'slug' => 'nordcross',
            ],
            [
                'name' => 'Omoda',
                'slug' => 'omoda',
            ],
            [
                'name' => 'Ora',
                'slug' => 'ora',
            ],
            [
                'name' => 'Oting',
                'slug' => 'oting',
            ],
            [
                'name' => 'Rising',
                'slug' => 'rising',
            ],
            [
                'name' => 'Rox Motor',
                'slug' => 'rox-motor',
            ],
            [
                'name' => 'Senat',
                'slug' => 'senat',
            ],
            [
                'name' => 'SERES',
                'slug' => 'seres',
            ],
            [
                'name' => 'SKM',
                'slug' => 'skm',
            ],
            [
                'name' => 'Skywell',
                'slug' => 'skywell',
            ],
            [
                'name' => 'Solaris',
                'slug' => 'solaris',
            ],
            [
                'name' => 'Sollers',
                'slug' => 'sollers',
            ],
            [
                'name' => 'Soueast',
                'slug' => 'soueast',
            ],
            [
                'name' => 'SWM',
                'slug' => 'swm',
            ],
            [
                'name' => 'Tank',
                'slug' => 'tank',
            ],
            [
                'name' => 'Tenet',
                'slug' => 'tenet',
            ],
            [
                'name' => 'UMO',
                'slug' => 'umo',
            ],
            [
                'name' => 'UNI',
                'slug' => 'uni',
            ],
            [
                'name' => 'Venucia',
                'slug' => 'venucia',
            ],
            [
                'name' => 'VGV',
                'slug' => 'vgv',
            ],
            [
                'name' => 'Volga',
                'slug' => 'volga',
            ],
            [
                'name' => 'VinFast',
                'slug' => 'vinfast',
            ],
            [
                'name' => 'Voyah',
                'slug' => 'voyah',
            ],
            [
                'name' => 'Weltmeister',
                'slug' => 'weltmeister',
            ],
            [
                'name' => 'WEY',
                'slug' => 'wey',
            ],
            [
                'name' => 'XCITE',
                'slug' => 'xcite',
            ],
            [
                'name' => 'Zeekr',
                'slug' => 'zeekr',
            ],
            [
                'name' => 'Амберавто',
                'slug' => 'amberauto',
            ],
            [
                'name' => 'АТОМ',
                'slug' => 'atom',
            ],
            [
                'name' => 'Москвич',
                'slug' => 'moskvich',
            ],
            [
                'name' => 'УАЗ',
                'slug' => 'uaz',
            ],
            [
                'name' => '212',
                'slug' => '212',
            ],
        ];

        $leftFromRussiaBrands = [
            [
                'name' => 'Audi',
                'slug' => 'audi',
            ],
            [
                'name' => 'BMW',
                'slug' => 'bmw',
            ],
            [
                'name' => 'Cadillac',
                'slug' => 'cadillac',
            ],
            [
                'name' => 'Chevrolet',
                'slug' => 'chevrolet',
            ],
            [
                'name' => 'Citroen',
                'slug' => 'citroen',
            ],
            [
                'name' => 'Ford',
                'slug' => 'ford',
            ],
            [
                'name' => 'Genesis',
                'slug' => 'genesis',
            ],
            [
                'name' => 'Honda',
                'slug' => 'honda',
            ],
            [
                'name' => 'Hyundai',
                'slug' => 'hyundai',
            ],
            [
                'name' => 'Infiniti',
                'slug' => 'infiniti',
            ],
            [
                'name' => 'Jeep',
                'slug' => 'jeep',
            ],
            [
                'name' => 'KIA',
                'slug' => 'kia',
            ],
            [
                'name' => 'Land Rover',
                'slug' => 'land-rover',
            ],
            [
                'name' => 'Lexus',
                'slug' => 'lexus',
            ],
            [
                'name' => 'Mazda',
                'slug' => 'mazda',
            ],
            [
                'name' => 'Mercedes-Benz',
                'slug' => 'mercedes-benz',
            ],
            [
                'name' => 'MINI',
                'slug' => 'mini',
            ],
            [
                'name' => 'Mitsubishi',
                'slug' => 'mitsubishi',
            ],
            [
                'name' => 'Nissan',
                'slug' => 'nissan',
            ],
            [
                'name' => 'Opel',
                'slug' => 'opel',
            ],
            [
                'name' => 'Peugeot',
                'slug' => 'peugeot',
            ],
            [
                'name' => 'Porsche',
                'slug' => 'porsche',
            ],
            [
                'name' => 'Renault',
                'slug' => 'renault',
            ],
            [
                'name' => 'Skoda',
                'slug' => 'skoda',
            ],
            [
                'name' => 'Subaru',
                'slug' => 'subaru',
            ],
            [
                'name' => 'Suzuki',
                'slug' => 'suzuki',
            ],
            [
                'name' => 'Toyota',
                'slug' => 'toyota',
            ],
            [
                'name' => 'Volkswagen',
                'slug' => 'volkswagen',
            ],
            [
                'name' => 'Volvo',
                'slug' => 'volvo',
            ],
            [
                'name' => 'Zotye',
                'slug' => 'zotye',
            ],
        ];

        foreach ($defaultBrands as $brand) {
            Brand::query()->updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'leave_from_russian' => false,
                ],
            );
        }

        foreach ($leftFromRussiaBrands as $brand) {
            Brand::query()->updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'leave_from_russian' => true,
                ],
            );
        }
    }
}
