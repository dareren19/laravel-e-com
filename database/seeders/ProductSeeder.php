<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $products = [
            [
                'name' => 'Sakura',
                'description' => '5x7 Tri-Fold design invitation cart cover.',
                'price' => 50.00,
                'image' => 'https://m.media-amazon.com/images/I/71XY1bN+ycL.jpg',
                'category' => 'Christening',
                'brand' => 'Tri-Fold',
                'stock' => 50,
                'is_featured' => true,
                'is_new' => false,
            ],
            [
                'name' => 'Floral',
                'description' => '5x7 Gate-Fold design invitation cart cover.',
                'price' => 45.00,
                'image' => 'https://m.media-amazon.com/images/I/91sD2llOd2L.jpg',
                'category' => 'Birthday',
                'brand' => 'Gate-Fold',
                'stock' => 60,
                'is_featured' => true,
                'is_new' => false,
            ],
            [
                'name' => 'Vintage',
                'description' => '5x7 Gate-Fold design invitation cart cover.',
                'price' => 25.00,
                'image' => 'https://www.dokkidesign.com/cdn/shop/products/IMG_4475_507a4fb3-fc63-4ab7-9274-106e843ef8d3_1024x1024.JPG?v=1505871138',
                'category' => 'Wedding',
                'brand' => 'Gate-Fold',
                'stock' => 20,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Ribbon',
                'description' => '5x6 Gate-Fold design invitation cart cover.',
                'price' => 30.00,
                'image' => 'https://www.elegantweddinginvites.com/wedding-blog/wp-content/uploads/2016/01/elegant-navy-blue-laser-cut-wedding-invitations-with-ribbon-EWWS034.jpg',
                'category' => 'Christening',
                'brand' => 'Gate-Fold',
                'stock' => 50,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Bloosom',
                'description' => '5x7 Sliding design invitation cart cover.',
                'price' => 20.00,
                'image' => 'https://down-ph.img.susercontent.com/file/ph-11134207-7rase-m6rf2khzwumjb3.webp',
                'category' => 'Chirstening',
                'brand' => 'Sliding',
                'stock' => 25,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Infinity',
                'description' => '5x6 Gate-Fold design invitation cart cover.',
                'price' => 50.00,
                'image' => 'https://m.media-amazon.com/images/I/81omyizWqBL._AC_UF1000,1000_QL80_.jpg',
                'category' => 'Christening',
                'brand' => 'Gate-Fold',
                'stock' => 25,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'GateWay',
                'description' => '5x6 Gate-Fold design invitation cart cover.',
                'price' => 25.00,
                'image' => 'https://image.made-in-china.com/202f0j00rlHpAhzWgeoU/Navy-Blue-Laser-Cut-Invitations-Laser-Cut-Wedding-Invitations-Card-Kit-with-Blank-Printable-Paper-and-Envelopes-for-Wedding-Birthday-Parties-Baby.webp',
                'category' => 'Birthday',
                'brand' => 'Gate-Fold',
                'stock' => 30,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Heart',
                'description' => '5x6 Gate-Fold design invitation cart cover.',
                'price' => 15.00,
                'image' => 'https://s.alicdn.com/@sc04/kf/H94b920a46a044f3c92340d9dec18c9fag/Ychon-Laser-Cut-Wedding-Invitation-Card-Kit-Blank-Printable-Paper-and-Envelopes-Invitation-Card-for-50th-Birthday-Party.jpg',
                'category' => 'Wedding',
                'brand' => 'Gate-Fold',
                'stock' => 12,
                'is_featured' => true,
                'is_new' => false,
            ],
            [
                'name' => 'Tree',
                'description' => '5x6 Gate-fold design invitation cart cover.',
                'price' => 20.00,
                'image' => 'https://s.alicdn.com/@sc04/kf/Hb737aa381d754569aeb0b7865394dfa8s.jpg',
                'category' => 'Wedding',
                'brand' => 'Gate-Fold',
                'stock' => 45,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Gate v2',
                'description' => '5x6 Gate-fold design invitation cart cover.',
                'price' => 100.00,
                'image' => 'https://amazepaperie.com/cdn/shop/products/Elegant-navy-blue-laser-cut-wedding-invitations-with-equisite-engraving-LC010_1_740x.jpg?v=1740452500',
                'category' => 'Wedding',
                'brand' => 'Gate-Fold',
                'stock' => 15,
                'is_featured' => true,
                'is_new' => false,
            ],
             [
                'name' => 'Broadleaf',
                'description' => '5x7 Tri-Fold design invitation cart cover.',
                'price' => 100.00,
                'image' => 'https://amazepaperie.com/cdn/shop/products/Elegant-navy-blue-laser-cut-wedding-invitations-with-equisite-engraving-LC010_1_740x.jpg?v=1740452500',
                'category' => 'Wedding',
                'brand' => 'Tri-Fold',
                'stock' => 25,
                'is_featured' => true,
                'is_new' => false,
            ],
            [
                'name' => 'Butterfly',
                'description' => '5x6 Gate-fold design invitation cart cover.',
                'price' => 40.00,
                'image' => 'https://img.kwcdn.com/product/fancy/aae063cf-57c5-4d3f-bc95-c1d2c0b8518b.jpg?imageMogr2/auto-orient%7CimageView2/2/w/800/q/70/format/webp',
                'category' => 'Wedding',
                'brand' => 'Gate-Fold',
                'stock' => 75,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Quinceañera',
                'description' => '5x6 Gate-fold design invitation cart cover.',
                'price' => 40.00,
                'image' => 'https://i.etsystatic.com/37795867/r/il/fe560b/5554622450/il_794xN.5554622450_o2mv.jpg',
                'category' => 'Wedding',
                'brand' => 'Gate-Fold',
                'stock' => 60,
                'is_featured' => false,
                'is_new' => true,
            ],
            [
                'name' => 'Flower',
                'description' => '5x6 Tri-fold design invitation cart cover.',
                'price' => 90.00,
                'image' => 'https://www.dhresource.com/webp/m/0x0/f2/albu/g9/M01/55/25/rBVaWF3p8ymAfxR9AA0hBrMTsno022.jpg',
                'category' => 'Wedding',
                'brand' => 'Tri-Fold',
                'stock' => 60,
                'is_featured' => true,
                'is_new' => false,
            ],
        ];

        foreach($products as $product){
            Product::create([
                ...$product,
                'slug' => Str::slug($product['name']),
            ]);
        }
    }

    
}
