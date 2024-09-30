<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BattleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('battles')->insert([
            [
                'name' => 'การโจมตี',
                'description' => 'กด [□] เพื่อทำการโจมตีปกติ ในขณะที่สร้างความเสียหายให้กับศัตรู คุณยังสามารถเติมเกจ ATB ที่จำเป็นต่อการใช้ความสามารถได้ ฯลฯ',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item1_pic.jpg',
            ],
            [
                'name' => 'คำสั่งการต่อสู้',
                'description' => 'หากคุณเปิดเมนูคำสั่งด้วย [×] มันจะเข้าสู่ "โหมดรอ" ซึ่งจะทำให้เวลาผ่านไปช้าลง คุณสามารถเลือกได้อย่างอิสระว่าจะวางแผนกลยุทธ์ของคุณอย่างรอบคอบและดำเนินการตามคำสั่ง หรือลงทะเบียนให้เป็นทางลัดและใช้งานตามสัญชาตญาณ',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item2_pic.jpg',
            ],
            [
                'name' => 'ความสามารถ',
                'description' => 'คุณสามารถใช้ความสามารถต่างๆ ได้ขึ้นอยู่กับอาวุธที่ติดตั้งและวัสดุคำสั่งที่ตั้งไว้ เมื่อใช้ความสามารถ เกจ ATB ที่สะสมเมื่อเวลาผ่านไปและการโจมตีจะถูกใช้ไป',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item3_pic.jpg',
            ],
            [
                'name' => 'เวทย์มนต์',
                'description' => 'ด้วยการตั้งค่าวัสดุเวทย์มนตร์ให้กับอาวุธและชุดเกราะ คุณสามารถใช้เวทย์มนตร์เช่น "ไฟ" และ "การรักษา" ได้ การใช้เวทมนตร์ต้องใช้เกจ ATB และ MP',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item4_pic.jpg',
            ],
            [
                'name' => 'ความสามารถอันเป็นเอกลักษณ์',
                'description' => 'กด [△] เพื่อเปิดใช้งานความสามารถพิเศษที่แตกต่างกันไปตามตัวละครแต่ละตัว บางอันไม่จำเป็นต้องใช้เกจ ATB แต่ต้องใช้เวลาในการชาร์จแทน',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item5_pic.jpg',
            ],
            [
                'name' => 'แบ่งขีดจำกัด',
                'description' => 'ขีดจำกัดเกจจะเต็มเมื่อได้รับความเสียหายหรือระเบิดศัตรู ด้วยการปล่อยขีดจำกัดเต็ม คุณสามารถปลดปล่อยเทคนิคขีดจำกัดอันทรงพลังที่เตรียมไว้สำหรับตัวละครแต่ละตัวได้',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item6_1_pic.jpg',
            ],
            [
                'name' => 'หนังสือทักษะ',
                'description' => 'คุณสามารถอ่าน "หนังสือทักษะ" เช่น หนังสือคู่มือการใช้ดาบและหนังสือลับเกี่ยวกับศิลปะการต่อสู้ได้ที่ร้านหนังสือเวทมนตร์ทั่วประเทศ ในการถอดรหัสมัน คุณจะต้องมีคะแนนทักษะที่สามารถได้รับเป็นรางวัลภารกิจ และโดยการบริโภคพวกมัน ความสามารถของตัวละครของคุณจะถูกเสริมความแข็งแกร่งและปลดล็อค',
                'image'=> 'https://www.jp.square-enix.com/ffvii_rebirth/battle/_img/basic/item/item7_1_pic.jpg',
            ],
        ]);
    }
}
