<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'phone' => '+10000000001',
                'password' => 'password',
            ]
        );
        $admin->syncRoles(['admin']);

        $manager = User::query()->updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Manager User',
                'phone' => '+10000000002',
                'password' => 'password',
            ]
        );
        $manager->syncRoles(['manager']);

        $masterUser = User::query()->updateOrCreate(
            ['email' => 'master@gmail.com'],
            [
                'name' => 'Master User',
                'phone' => '+10000000003',
                'password' => 'password',
            ]
        );
        $masterUser->syncRoles(['master']);

        $client = User::query()->updateOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Client User',
                'phone' => '+10000000004',
                'password' => 'password',
            ]
        );
        $client->syncRoles(['client']);

        $categories = [
            ['slug' => 'hair', 'name' => 'Hair', 'name_i18n' => ['en' => 'Hair', 'ru' => 'Волосы', 'hy' => 'Մազեր'], 'booking_group' => 'hair', 'sort' => 10],
            ['slug' => 'men-hair', 'name' => 'Men Hair', 'name_i18n' => ['en' => 'Men Hair', 'ru' => 'Мужские услуги', 'hy' => 'Տղամարդկանց ծառայություններ'], 'booking_group' => 'hair', 'sort' => 15],
            ['slug' => 'nails', 'name' => 'Nails', 'name_i18n' => ['en' => 'Nails', 'ru' => 'Маникюр', 'hy' => 'Մատնահարդարում'], 'booking_group' => 'nail-care', 'sort' => 20],
            ['slug' => 'pedicure', 'name' => 'Pedicure', 'name_i18n' => ['en' => 'Pedicure', 'ru' => 'Педикюр', 'hy' => 'Պեդիկյուր'], 'booking_group' => 'nail-care', 'sort' => 30],
            ['slug' => 'brows-lashes', 'name' => 'Brows & Lashes', 'name_i18n' => ['en' => 'Brows & Lashes', 'ru' => 'Брови и ресницы', 'hy' => 'Հոնքեր և թարթիչներ'], 'booking_group' => 'brows-lashes', 'sort' => 40],
            ['slug' => 'permanent-makeup', 'name' => 'Permanent Makeup', 'name_i18n' => ['en' => 'Permanent Makeup', 'ru' => 'Перманентный макияж', 'hy' => 'Պերմանենտ դիմահարդարում'], 'booking_group' => 'brows-lashes', 'sort' => 45],
            ['slug' => 'makeup', 'name' => 'Makeup', 'name_i18n' => ['en' => 'Makeup', 'ru' => 'Макияж', 'hy' => 'Դիմահարդարում'], 'booking_group' => 'makeup', 'sort' => 50],
            ['slug' => 'spa', 'name' => 'Spa & Care', 'name_i18n' => ['en' => 'Spa & Care', 'ru' => 'SPA и уход', 'hy' => 'SPA և խնամք'], 'booking_group' => 'spa-care', 'sort' => 60],
            ['slug' => 'epilation', 'name' => 'Epilation', 'name_i18n' => ['en' => 'Epilation', 'ru' => 'Эпиляция', 'hy' => 'Էպիլյացիա'], 'booking_group' => 'epilation', 'sort' => 70],
            ['slug' => 'wax-epilation', 'name' => 'Wax Epilation', 'name_i18n' => ['en' => 'Wax Epilation', 'ru' => 'Восковая депиляция', 'hy' => 'Մոմային էպիլյացիա'], 'booking_group' => 'epilation', 'sort' => 75],
            ['slug' => 'cosmetology', 'name' => 'Cosmetology', 'name_i18n' => ['en' => 'Cosmetology', 'ru' => 'Косметология', 'hy' => 'Կոսմետոլոգիա'], 'booking_group' => 'cosmetology', 'sort' => 80],
            ['slug' => 'massage-care', 'name' => 'Massage & Care', 'name_i18n' => ['en' => 'Massage & Care', 'ru' => 'Массаж и уход', 'hy' => 'Մերսում և խնամք'], 'booking_group' => 'massage', 'sort' => 90],
            ['slug' => 'specialized', 'name' => 'Specialized Treatments', 'name_i18n' => ['en' => 'Specialized Treatments', 'ru' => 'Специализированные процедуры', 'hy' => 'Մասնագիտացված ծառայություններ'], 'booking_group' => 'specialized', 'sort' => 100],
            ['slug' => 'therapeutic-massage', 'name' => 'Therapeutic Massage', 'name_i18n' => ['en' => 'Therapeutic Massage', 'ru' => 'Лечебный массаж', 'hy' => 'Բուժական մերսում'], 'booking_group' => 'massage', 'sort' => 110],
        ];

        $categoryMap = [];
        foreach ($categories as $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'name_i18n' => $categoryData['name_i18n'],
                    'booking_group' => $categoryData['booking_group'],
                    'sort' => $categoryData['sort'],
                    'is_active' => true,
                ]
            );
            $categoryMap[$categoryData['slug']] = $category;
        }

        $servicesData = [
            ['slug' => 'women-haircut', 'category_slug' => 'hair', 'name' => 'Women Haircut', 'name_i18n' => ['en' => 'Women Haircut', 'ru' => 'Женская стрижка', 'hy' => 'Կանացի սանրվածք'], 'description' => 'Haircut and styling', 'description_i18n' => ['en' => 'Haircut and styling', 'ru' => 'Стрижка и укладка', 'hy' => 'Սանրվածք և հարդարում'], 'duration_minutes' => 60, 'price_from' => 40, 'price_to' => 70, 'sort' => 10],
            ['slug' => 'mens-haircut', 'category_slug' => 'hair', 'name' => 'Men Haircut', 'name_i18n' => ['en' => 'Men Haircut', 'ru' => 'Мужская стрижка', 'hy' => 'Տղամարդու սանրվածք'], 'description' => 'Classic and modern men styles', 'description_i18n' => ['en' => 'Classic and modern men styles', 'ru' => 'Классические и современные мужские стили', 'hy' => 'Դասական և ժամանակակից տղամարդու ոճեր'], 'duration_minutes' => 45, 'price_from' => 30, 'price_to' => 55, 'sort' => 20],
            ['slug' => 'hair-coloring', 'category_slug' => 'hair', 'name' => 'Hair Coloring', 'name_i18n' => ['en' => 'Hair Coloring', 'ru' => 'Окрашивание волос', 'hy' => 'Մազերի ներկում'], 'description' => 'Full coloring and toning', 'description_i18n' => ['en' => 'Full coloring and toning', 'ru' => 'Полное окрашивание и тонирование', 'hy' => 'Լիարժեք ներկում և տոնավորում'], 'duration_minutes' => 120, 'price_from' => 90, 'price_to' => 180, 'sort' => 30],
            ['slug' => 'blowout-styling', 'category_slug' => 'hair', 'name' => 'Blowout Styling', 'name_i18n' => ['en' => 'Blowout Styling', 'ru' => 'Укладка феном', 'hy' => 'Ֆենով հարդարում'], 'description' => 'Quick smooth styling and volume', 'description_i18n' => ['en' => 'Quick smooth styling and volume', 'ru' => 'Быстрая укладка и объем', 'hy' => 'Արագ հարդարում և ծավալ'], 'duration_minutes' => 40, 'price_from' => 25, 'price_to' => 45, 'sort' => 40],

            ['slug' => 'manicure-classic', 'category_slug' => 'nails', 'name' => 'Classic Manicure', 'name_i18n' => ['en' => 'Classic Manicure', 'ru' => 'Классический маникюр', 'hy' => 'Դասական մատնահարդարում'], 'description' => 'Nail care and polish', 'description_i18n' => ['en' => 'Nail care and polish', 'ru' => 'Уход за ногтями и покрытие', 'hy' => 'Եղունգների խնամք և լաքապատում'], 'duration_minutes' => 50, 'price_from' => 30, 'price_to' => 55, 'sort' => 10],
            ['slug' => 'gel-manicure', 'category_slug' => 'nails', 'name' => 'Gel Manicure', 'name_i18n' => ['en' => 'Gel Manicure', 'ru' => 'Гелевый маникюр', 'hy' => 'Գելային մատնահարդարում'], 'description' => 'Long-lasting gel coverage', 'description_i18n' => ['en' => 'Long-lasting gel coverage', 'ru' => 'Стойкое гелевое покрытие', 'hy' => 'Երկարատև գելային ծածկույթ'], 'duration_minutes' => 70, 'price_from' => 45, 'price_to' => 75, 'sort' => 20],
            ['slug' => 'pedicure-spa', 'category_slug' => 'pedicure', 'name' => 'Spa Pedicure', 'name_i18n' => ['en' => 'Spa Pedicure', 'ru' => 'SPA-педикюр', 'hy' => 'SPA պեդիկյուր'], 'description' => 'Deep care pedicure treatment', 'description_i18n' => ['en' => 'Deep care pedicure treatment', 'ru' => 'Глубокий уход для стоп', 'hy' => 'Խորը խնամք ոտքերի համար'], 'duration_minutes' => 80, 'price_from' => 50, 'price_to' => 90, 'sort' => 30],
            ['slug' => 'nail-design', 'category_slug' => 'nails', 'name' => 'Nail Design', 'name_i18n' => ['en' => 'Nail Design', 'ru' => 'Дизайн ногтей', 'hy' => 'Եղունգների դիզայն'], 'description' => 'Art and premium design details', 'description_i18n' => ['en' => 'Art and premium design details', 'ru' => 'Арт и премиум детали дизайна', 'hy' => 'Արտ և պրեմիում դիզայն'], 'duration_minutes' => 35, 'price_from' => 20, 'price_to' => 45, 'sort' => 40],

            ['slug' => 'brow-shaping', 'category_slug' => 'brows-lashes', 'name' => 'Brow Shaping', 'name_i18n' => ['en' => 'Brow Shaping', 'ru' => 'Коррекция бровей', 'hy' => 'Հոնքերի շտկում'], 'description' => 'Shape and correction', 'description_i18n' => ['en' => 'Shape and correction', 'ru' => 'Форма и коррекция', 'hy' => 'Ձև և շտկում'], 'duration_minutes' => 30, 'price_from' => 18, 'price_to' => 30, 'sort' => 10],
            ['slug' => 'lash-lift', 'category_slug' => 'brows-lashes', 'name' => 'Lash Lift', 'name_i18n' => ['en' => 'Lash Lift', 'ru' => 'Ламинирование ресниц', 'hy' => 'Թարթիչների լամինացիա'], 'description' => 'Natural lash lifting effect', 'description_i18n' => ['en' => 'Natural lash lifting effect', 'ru' => 'Естественный лифтинг ресниц', 'hy' => 'Թարթիչների բնական բարձրացում'], 'duration_minutes' => 55, 'price_from' => 35, 'price_to' => 60, 'sort' => 20],
            ['slug' => 'brow-lamination', 'category_slug' => 'brows-lashes', 'name' => 'Brow Lamination', 'name_i18n' => ['en' => 'Brow Lamination', 'ru' => 'Ламинирование бровей', 'hy' => 'Հոնքերի լամինացիա'], 'description' => 'Volume and shape fixation', 'description_i18n' => ['en' => 'Volume and shape fixation', 'ru' => 'Фиксация формы и объема', 'hy' => 'Ձևի և ծավալի ֆիքսում'], 'duration_minutes' => 45, 'price_from' => 28, 'price_to' => 50, 'sort' => 30],

            ['slug' => 'day-makeup', 'category_slug' => 'makeup', 'name' => 'Day Makeup', 'name_i18n' => ['en' => 'Day Makeup', 'ru' => 'Дневной макияж', 'hy' => 'Օրվա դիմահարդարում'], 'description' => 'Light daily makeup style', 'description_i18n' => ['en' => 'Light daily makeup style', 'ru' => 'Легкий дневной макияж', 'hy' => 'Թեթև օրվա դիմահարդարում'], 'duration_minutes' => 50, 'price_from' => 40, 'price_to' => 70, 'sort' => 10],
            ['slug' => 'event-makeup', 'category_slug' => 'makeup', 'name' => 'Event Makeup', 'name_i18n' => ['en' => 'Event Makeup', 'ru' => 'Вечерний макияж', 'hy' => 'Երեկոյան դիմահարդարում'], 'description' => 'Evening and special events look', 'description_i18n' => ['en' => 'Evening and special events look', 'ru' => 'Образ для вечера и мероприятий', 'hy' => 'Կերպար երեկոյի և միջոցառումների համար'], 'duration_minutes' => 75, 'price_from' => 60, 'price_to' => 110, 'sort' => 20],
            ['slug' => 'bridal-makeup', 'category_slug' => 'makeup', 'name' => 'Bridal Makeup', 'name_i18n' => ['en' => 'Bridal Makeup', 'ru' => 'Свадебный макияж', 'hy' => 'Հարսանեկան դիմահարդարում'], 'description' => 'Long-lasting bridal look', 'description_i18n' => ['en' => 'Long-lasting bridal look', 'ru' => 'Стойкий свадебный образ', 'hy' => 'Երկարատև հարսանեկան կերպար'], 'duration_minutes' => 90, 'price_from' => 80, 'price_to' => 140, 'sort' => 30],

            ['slug' => 'face-cleaning', 'category_slug' => 'spa', 'name' => 'Face Cleaning', 'name_i18n' => ['en' => 'Face Cleaning', 'ru' => 'Чистка лица', 'hy' => 'Դեմքի մաքրում'], 'description' => 'Deep skin cleansing', 'description_i18n' => ['en' => 'Deep skin cleansing', 'ru' => 'Глубокое очищение кожи', 'hy' => 'Մաշկի խորը մաքրում'], 'duration_minutes' => 60, 'price_from' => 45, 'price_to' => 80, 'sort' => 10],
            ['slug' => 'relax-massage', 'category_slug' => 'spa', 'name' => 'Relax Massage', 'name_i18n' => ['en' => 'Relax Massage', 'ru' => 'Расслабляющий массаж', 'hy' => 'Հանգստացնող մերսում'], 'description' => 'Stress relief body massage', 'description_i18n' => ['en' => 'Stress relief body massage', 'ru' => 'Массаж для снятия стресса', 'hy' => 'Մարմնի մերսում սթրեսի թեթևացման համար'], 'duration_minutes' => 60, 'price_from' => 55, 'price_to' => 95, 'sort' => 20],
            ['slug' => 'body-scrub', 'category_slug' => 'spa', 'name' => 'Body Scrub', 'name_i18n' => ['en' => 'Body Scrub', 'ru' => 'Скрабирование тела', 'hy' => 'Մարմնի սկրաբ'], 'description' => 'Exfoliation and soft skin treatment', 'description_i18n' => ['en' => 'Exfoliation and soft skin treatment', 'ru' => 'Отшелушивание и мягкая обработка кожи', 'hy' => 'Մաշկի նուրբ մաքրում և խնամք'], 'duration_minutes' => 40, 'price_from' => 30, 'price_to' => 60, 'sort' => 30],

            ['slug' => 'legs-epilation', 'category_slug' => 'epilation', 'name' => 'Legs Epilation', 'name_i18n' => ['en' => 'Legs Epilation', 'ru' => 'Эпиляция ног', 'hy' => 'Ոտքերի էպիլյացիա'], 'description' => 'Wax epilation for smooth skin', 'description_i18n' => ['en' => 'Wax epilation for smooth skin', 'ru' => 'Восковая эпиляция для гладкой кожи', 'hy' => 'Մոմային էպիլյացիա հարթ մաշկի համար'], 'duration_minutes' => 50, 'price_from' => 35, 'price_to' => 70, 'sort' => 10],
            ['slug' => 'arms-epilation', 'category_slug' => 'epilation', 'name' => 'Arms Epilation', 'name_i18n' => ['en' => 'Arms Epilation', 'ru' => 'Эпиляция рук', 'hy' => 'Ձեռքերի էպիլյացիա'], 'description' => 'Fast wax epilation for arms', 'description_i18n' => ['en' => 'Fast wax epilation for arms', 'ru' => 'Быстрая эпиляция рук воском', 'hy' => 'Ձեռքերի արագ մոմային էպիլյացիա'], 'duration_minutes' => 30, 'price_from' => 20, 'price_to' => 40, 'sort' => 20],

            ['slug' => 'anti-age-treatment', 'category_slug' => 'cosmetology', 'name' => 'Anti-age Treatment', 'name_i18n' => ['en' => 'Anti-age Treatment', 'ru' => 'Антивозрастной уход', 'hy' => 'Հակատարիքային խնամք'], 'description' => 'Firming and hydration protocol', 'description_i18n' => ['en' => 'Firming and hydration protocol', 'ru' => 'Протокол лифтинга и увлажнения', 'hy' => 'Ձգման և խոնավացման պրոտոկոլ'], 'duration_minutes' => 75, 'price_from' => 70, 'price_to' => 130, 'sort' => 10],
            ['slug' => 'chemical-peel', 'category_slug' => 'cosmetology', 'name' => 'Chemical Peel', 'name_i18n' => ['en' => 'Chemical Peel', 'ru' => 'Химический пилинг', 'hy' => 'Քիմիական պիլինգ'], 'description' => 'Skin renewal and texture improvement', 'description_i18n' => ['en' => 'Skin renewal and texture improvement', 'ru' => 'Обновление кожи и улучшение текстуры', 'hy' => 'Մաշկի թարմացում և տեքստուրայի բարելավում'], 'duration_minutes' => 50, 'price_from' => 65, 'price_to' => 120, 'sort' => 20],

            ['slug' => 'hair-keratin-treatment', 'category_slug' => 'hair', 'name' => 'Hair Keratin Treatment', 'name_i18n' => ['en' => 'Hair Keratin Treatment', 'ru' => 'Кератиновое выпрямление', 'hy' => 'Մազերի կերատին'], 'description' => 'Keratin hair straightening and smoothing', 'description_i18n' => ['en' => 'Keratin hair straightening and smoothing', 'ru' => 'Кератиновое выпрямление и разглаживание волос', 'hy' => 'Կերատինային հարթեցում և ուղղում'], 'duration_minutes' => 120, 'price_from' => 20000, 'price_to' => 50000, 'sort' => 50],
            ['slug' => 'hair-lamination', 'category_slug' => 'hair', 'name' => 'Hair Lamination', 'name_i18n' => ['en' => 'Hair Lamination', 'ru' => 'Ламинирование волос', 'hy' => 'Մազերի լամինացիա'], 'description' => 'Protective laminating care for hair', 'description_i18n' => ['en' => 'Protective laminating care for hair', 'ru' => 'Ламинирующий уход для защиты волос', 'hy' => 'Լամինացնող պաշտպանիչ խնամք մազերի համար'], 'duration_minutes' => 90, 'price_from' => 10000, 'price_to' => 20000, 'sort' => 60],
            ['slug' => 'hair-botox', 'category_slug' => 'hair', 'name' => 'Hair Botox', 'name_i18n' => ['en' => 'Hair Botox', 'ru' => 'Ботокс для волос', 'hy' => 'Մազերի բոտոքս'], 'description' => 'Restorative botox treatment for hair', 'description_i18n' => ['en' => 'Restorative botox treatment for hair', 'ru' => 'Восстанавливающий ботокс для волос', 'hy' => 'Վերականգնող բոտոքսային խնամք մազերի համար'], 'duration_minutes' => 90, 'price_from' => 10000, 'price_to' => 20000, 'sort' => 70],
            ['slug' => 'evening-hairstyle', 'category_slug' => 'hair', 'name' => 'Evening Hairstyle', 'name_i18n' => ['en' => 'Evening Hairstyle', 'ru' => 'Вечерняя укладка', 'hy' => 'Երեկոյան սանրվածք'], 'description' => 'Evening hairstyle and styling', 'description_i18n' => ['en' => 'Evening hairstyle and styling', 'ru' => 'Вечерняя укладка и прическа', 'hy' => 'Երեկոյան սանրվածք և հարդարում'], 'duration_minutes' => 90, 'price_from' => 7000, 'price_to' => 25000, 'sort' => 80],
            ['slug' => 'creative-haircut', 'category_slug' => 'hair', 'name' => 'Creative Haircut', 'name_i18n' => ['en' => 'Creative Haircut', 'ru' => 'Креативная стрижка', 'hy' => 'Կրեատիվ կտրվածք'], 'description' => 'Creative haircut with custom shape', 'description_i18n' => ['en' => 'Creative haircut with custom shape', 'ru' => 'Креативная стрижка с индивидуальной формой', 'hy' => 'Կրեատիվ կտրվածք անհատական ձևավորմամբ'], 'duration_minutes' => 60, 'price_from' => 5000, 'price_to' => 5000, 'sort' => 90],
            ['slug' => 'hollywood-hairstyle', 'category_slug' => 'hair', 'name' => 'Hollywood Hairstyle', 'name_i18n' => ['en' => 'Hollywood Hairstyle', 'ru' => 'Голливудская укладка', 'hy' => 'Հոլիվուդյան սանրվածք'], 'description' => 'Hollywood-style hair styling', 'description_i18n' => ['en' => 'Hollywood-style hair styling', 'ru' => 'Голливудская укладка волос', 'hy' => 'Հոլիվուդյան ոճի հարդարում'], 'duration_minutes' => 70, 'price_from' => 7000, 'price_to' => 7000, 'sort' => 100],
            ['slug' => 'restorative-hair-care', 'category_slug' => 'hair', 'name' => 'Restorative Hair Care', 'name_i18n' => ['en' => 'Restorative Hair Care', 'ru' => 'Восстанавливающий уход за волосами', 'hy' => 'Մազերի վերականգնող խնամք'], 'description' => 'Deep restorative treatment for damaged hair', 'description_i18n' => ['en' => 'Deep restorative treatment for damaged hair', 'ru' => 'Глубокий восстановительный уход для поврежденных волос', 'hy' => 'Խորը վերականգնող խնամք վնասված մազերի համար'], 'duration_minutes' => 75, 'price_from' => 8000, 'price_to' => 10000, 'sort' => 110],
            ['slug' => 'hair-perm', 'category_slug' => 'hair', 'name' => 'Hair Perm', 'name_i18n' => ['en' => 'Hair Perm', 'ru' => 'Химическая завивка для волос', 'hy' => 'Մազերի քիմիական գանգրեցում'], 'description' => 'Chemical perm for long-lasting curls', 'description_i18n' => ['en' => 'Chemical perm for long-lasting curls', 'ru' => 'Химическая завивка для стойких локонов', 'hy' => 'Քիմիական գանգրացում երկարատև ալիքների համար'], 'duration_minutes' => 120, 'price_from' => 20000, 'price_to' => 50000, 'sort' => 120],
            ['slug' => 'advanced-coloristics', 'category_slug' => 'hair', 'name' => 'Advanced Coloristics', 'name_i18n' => ['en' => 'Advanced Coloristics', 'ru' => 'Высокая колористика', 'hy' => 'Բարձր գունաբանություն'], 'description' => 'Advanced coloring and color correction', 'description_i18n' => ['en' => 'Advanced coloring and color correction', 'ru' => 'Сложное окрашивание и коррекция цвета', 'hy' => 'Բարդ գունավորում և գույնի շտկում'], 'duration_minutes' => 150, 'price_from' => 15000, 'price_to' => 50000, 'sort' => 130],

            ['slug' => 'beard-shaping', 'category_slug' => 'men-hair', 'name' => 'Beard Shaping', 'name_i18n' => ['en' => 'Beard Shaping', 'ru' => 'Оформление бороды', 'hy' => 'Մորուքի ձևավորում'], 'description' => 'Beard contour and shape service', 'description_i18n' => ['en' => 'Beard contour and shape service', 'ru' => 'Оформление и контур бороды', 'hy' => 'Մորուքի ձևավորում և կոնտուրավորում'], 'duration_minutes' => 30, 'price_from' => 3000, 'price_to' => 3000, 'sort' => 20],
            ['slug' => 'beard-coloring', 'category_slug' => 'men-hair', 'name' => 'Beard Coloring', 'name_i18n' => ['en' => 'Beard Coloring', 'ru' => 'Окрашивание бороды', 'hy' => 'Մորուքի ներկում'], 'description' => 'Beard coloring service', 'description_i18n' => ['en' => 'Beard coloring service', 'ru' => 'Услуга окрашивания бороды', 'hy' => 'Մորուքի ներկման ծառայություն'], 'duration_minutes' => 30, 'price_from' => 3000, 'price_to' => 3000, 'sort' => 30],
            ['slug' => 'mens-hair-perm', 'category_slug' => 'men-hair', 'name' => 'Men Hair Perm', 'name_i18n' => ['en' => 'Men Hair Perm', 'ru' => 'Химическая завивка для мужчин', 'hy' => 'Տղաների քիմիական գանգրացում'], 'description' => 'Chemical perm for men', 'description_i18n' => ['en' => 'Chemical perm for men', 'ru' => 'Мужская химическая завивка', 'hy' => 'Տղամարդկանց քիմիական գանգրացում'], 'duration_minutes' => 90, 'price_from' => 10000, 'price_to' => 15000, 'sort' => 40],
            ['slug' => 'mens-hairline-shaping', 'category_slug' => 'men-hair', 'name' => 'Men Hairline Shaping', 'name_i18n' => ['en' => 'Men Hairline Shaping', 'ru' => 'Мужская окантовка', 'hy' => 'Տղաների կանտովկա'], 'description' => 'Hairline contour for men', 'description_i18n' => ['en' => 'Hairline contour for men', 'ru' => 'Окантовка мужской стрижки', 'hy' => 'Տղամարդկանց սանրվածքի եզրագծում'], 'duration_minutes' => 20, 'price_from' => 1000, 'price_to' => 1500, 'sort' => 50],
            ['slug' => 'mens-facial-care', 'category_slug' => 'men-hair', 'name' => 'Men Facial Care', 'name_i18n' => ['en' => 'Men Facial Care', 'ru' => 'Уход за мужским лицом', 'hy' => 'Տղաների դեմքի խնամք'], 'description' => 'Facial care for men', 'description_i18n' => ['en' => 'Facial care for men', 'ru' => 'Уход за кожей лица для мужчин', 'hy' => 'Դեմքի մաշկի խնամք տղամարդկանց համար'], 'duration_minutes' => 45, 'price_from' => 3000, 'price_to' => 5000, 'sort' => 60],

            ['slug' => 'manicure-with-strengthening', 'category_slug' => 'nails', 'name' => 'Manicure with Strengthening', 'name_i18n' => ['en' => 'Manicure with Strengthening', 'ru' => 'Маникюр с укреплением', 'hy' => 'Մատնահարդարում ամրեցումով'], 'description' => 'Manicure with nail strengthening', 'description_i18n' => ['en' => 'Manicure with nail strengthening', 'ru' => 'Маникюр с укреплением ногтей', 'hy' => 'Մատնահարդարում եղունգների ամրացմամբ'], 'duration_minutes' => 90, 'price_from' => 8000, 'price_to' => 10000, 'sort' => 50],
            ['slug' => 'french-manicure', 'category_slug' => 'nails', 'name' => 'French Manicure', 'name_i18n' => ['en' => 'French Manicure', 'ru' => 'Французский маникюр', 'hy' => 'Ֆրանսիական մատնահարդարում'], 'description' => 'French style manicure', 'description_i18n' => ['en' => 'French style manicure', 'ru' => 'Маникюр во французском стиле', 'hy' => 'Ֆրանսիական ոճի մատնահարդարում'], 'duration_minutes' => 35, 'price_from' => 1000, 'price_to' => 2000, 'sort' => 60],
            ['slug' => 'creative-nail-design', 'category_slug' => 'nails', 'name' => 'Creative Nail Design', 'name_i18n' => ['en' => 'Creative Nail Design', 'ru' => 'Дизайнерский нейл-дизайн', 'hy' => 'Դիզայներական լուծումներ'], 'description' => 'Creative and custom nail design', 'description_i18n' => ['en' => 'Creative and custom nail design', 'ru' => 'Креативный и индивидуальный дизайн ногтей', 'hy' => 'Կրեատիվ և անհատական եղունգների դիզայն'], 'duration_minutes' => 35, 'price_from' => 1000, 'price_to' => 2000, 'sort' => 70],
            ['slug' => 'short-nail-extensions', 'category_slug' => 'nails', 'name' => 'Short Nail Extensions', 'name_i18n' => ['en' => 'Short Nail Extensions', 'ru' => 'Наращивание короткой длины', 'hy' => 'Կարճ երկարեցում'], 'description' => 'Short length nail extensions', 'description_i18n' => ['en' => 'Short length nail extensions', 'ru' => 'Наращивание ногтей короткой длины', 'hy' => 'Կարճ երկարությամբ եղունգների երկարեցում'], 'duration_minutes' => 120, 'price_from' => 10000, 'price_to' => 10000, 'sort' => 80],
            ['slug' => 'medium-long-nail-extensions', 'category_slug' => 'nails', 'name' => 'Medium and Long Nail Extensions', 'name_i18n' => ['en' => 'Medium and Long Nail Extensions', 'ru' => 'Наращивание средней и максимальной длины', 'hy' => 'Միջին և max երկարեցում'], 'description' => 'Medium and long nail extensions', 'description_i18n' => ['en' => 'Medium and long nail extensions', 'ru' => 'Наращивание ногтей средней и максимальной длины', 'hy' => 'Միջին և երկար եղունգների երկարեցում'], 'duration_minutes' => 140, 'price_from' => 10000, 'price_to' => 15000, 'sort' => 90],
            ['slug' => 'one-nail-correction', 'category_slug' => 'nails', 'name' => 'One Nail Correction', 'name_i18n' => ['en' => 'One Nail Correction', 'ru' => 'Коррекция одного ногтя', 'hy' => 'Շտկում մեկ մատի'], 'description' => 'Correction for one nail', 'description_i18n' => ['en' => 'Correction for one nail', 'ru' => 'Коррекция одного ногтя', 'hy' => 'Մեկ եղունգի շտկում'], 'duration_minutes' => 15, 'price_from' => 1000, 'price_to' => 1000, 'sort' => 100],
            ['slug' => 'gel-polish-removal', 'category_slug' => 'nails', 'name' => 'Gel Polish Removal', 'name_i18n' => ['en' => 'Gel Polish Removal', 'ru' => 'Снятие гель-лака', 'hy' => 'Գել լաքի հեռացում'], 'description' => 'Safe gel polish removal', 'description_i18n' => ['en' => 'Safe gel polish removal', 'ru' => 'Безопасное снятие гель-лака', 'hy' => 'Գել լաքի անվտանգ հեռացում'], 'duration_minutes' => 20, 'price_from' => 2000, 'price_to' => 2000, 'sort' => 110],
            ['slug' => 'salon-hand-care', 'category_slug' => 'nails', 'name' => 'Salon Hand Care', 'name_i18n' => ['en' => 'Salon Hand Care', 'ru' => 'Салонный уход за руками', 'hy' => 'Ձեռքի սալոնային խնամք'], 'description' => 'Salon hand care treatment', 'description_i18n' => ['en' => 'Salon hand care treatment', 'ru' => 'Салонный уход за руками', 'hy' => 'Ձեռքերի սալոնային խնամք'], 'duration_minutes' => 45, 'price_from' => 3000, 'price_to' => 5000, 'sort' => 120],
            ['slug' => 'wet-pedicure-gel-polish', 'category_slug' => 'pedicure', 'name' => 'Wet Pedicure with Gel Polish', 'name_i18n' => ['en' => 'Wet Pedicure with Gel Polish', 'ru' => 'Влажный педикюр с гель-лаком', 'hy' => 'Ոտքի թաց մաքրում և գել լաք'], 'description' => 'Wet pedicure with gel polish', 'description_i18n' => ['en' => 'Wet pedicure with gel polish', 'ru' => 'Влажный педикюр с покрытием гель-лаком', 'hy' => 'Թաց պեդիկյուր գել լաքով'], 'duration_minutes' => 90, 'price_from' => 7000, 'price_to' => 10000, 'sort' => 40],

            ['slug' => 'powder-brows', 'category_slug' => 'permanent-makeup', 'name' => 'Powder Brows', 'name_i18n' => ['en' => 'Powder Brows', 'ru' => 'Пудровое напыление бровей', 'hy' => 'Հոնքերի փոշեդրում'], 'description' => 'Powder eyebrow shading', 'description_i18n' => ['en' => 'Powder eyebrow shading', 'ru' => 'Пудровое напыление бровей', 'hy' => 'Հոնքերի փոշային շեյդինգ'], 'duration_minutes' => 120, 'price_from' => 30000, 'price_to' => 30000, 'sort' => 10],
            ['slug' => 'powder-lips', 'category_slug' => 'permanent-makeup', 'name' => 'Powder Lips', 'name_i18n' => ['en' => 'Powder Lips', 'ru' => 'Пудровое напыление губ', 'hy' => 'Շուրթերի փոշեդրում'], 'description' => 'Powder lips / lip blush', 'description_i18n' => ['en' => 'Powder lips / lip blush', 'ru' => 'Пудровое напыление губ', 'hy' => 'Շուրթերի փոշային գունավորում'], 'duration_minutes' => 120, 'price_from' => 30000, 'price_to' => 30000, 'sort' => 20],
            ['slug' => 'powder-eyeliner', 'category_slug' => 'permanent-makeup', 'name' => 'Powder Eyeliner', 'name_i18n' => ['en' => 'Powder Eyeliner', 'ru' => 'Пудровое напыление век', 'hy' => 'Աչքերի փոշեդրում'], 'description' => 'Powder eyeliner / eyelid shading', 'description_i18n' => ['en' => 'Powder eyeliner / eyelid shading', 'ru' => 'Пудровое напыление век', 'hy' => 'Կոպերի փոշային շեյդինգ'], 'duration_minutes' => 120, 'price_from' => 30000, 'price_to' => 30000, 'sort' => 30],
            ['slug' => 'brow-and-lash-lamination', 'category_slug' => 'brows-lashes', 'name' => 'Brow and Lash Lamination', 'name_i18n' => ['en' => 'Brow and Lash Lamination', 'ru' => 'Ламинирование бровей и ресниц', 'hy' => 'Լամինացիա հոնքերի և թարթիչների'], 'description' => 'Combined brow and lash lamination', 'description_i18n' => ['en' => 'Combined brow and lash lamination', 'ru' => 'Комплексное ламинирование бровей и ресниц', 'hy' => 'Հոնքերի և թարթիչների համակցված լամինացիա'], 'duration_minutes' => 75, 'price_from' => 10000, 'price_to' => 15000, 'sort' => 40],
            ['slug' => 'eyelash-extensions-premium', 'category_slug' => 'brows-lashes', 'name' => 'Eyelash Extensions', 'name_i18n' => ['en' => 'Eyelash Extensions', 'ru' => 'Наращивание ресниц', 'hy' => 'Թարթիչների լիցք'], 'description' => 'Eyelash extension service', 'description_i18n' => ['en' => 'Eyelash extension service', 'ru' => 'Услуга наращивания ресниц', 'hy' => 'Թարթիչների երկարացման ծառայություն'], 'duration_minutes' => 120, 'price_from' => 10000, 'price_to' => 20000, 'sort' => 50],
            ['slug' => 'makeup-with-false-lashes', 'category_slug' => 'makeup', 'name' => 'Makeup with False Lashes', 'name_i18n' => ['en' => 'Makeup with False Lashes', 'ru' => 'Макияж с накладными ресницами', 'hy' => 'Դիմահարդարում թարթիչով'], 'description' => 'Full makeup with false lashes', 'description_i18n' => ['en' => 'Full makeup with false lashes', 'ru' => 'Полный макияж с накладными ресницами', 'hy' => 'Լիարժեք դիմահարդարում կեղծ թարթիչներով'], 'duration_minutes' => 90, 'price_from' => 15000, 'price_to' => 15000, 'sort' => 40],
            ['slug' => 'brow-tinting', 'category_slug' => 'brows-lashes', 'name' => 'Brow Tinting', 'name_i18n' => ['en' => 'Brow Tinting', 'ru' => 'Окрашивание бровей', 'hy' => 'Հոնքի ներկում'], 'description' => 'Eyebrow tinting service', 'description_i18n' => ['en' => 'Eyebrow tinting service', 'ru' => 'Окрашивание бровей', 'hy' => 'Հոնքերի ներկման ծառայություն'], 'duration_minutes' => 25, 'price_from' => 2000, 'price_to' => 2000, 'sort' => 60],

            ['slug' => 'elos-face', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Face', 'name_i18n' => ['en' => 'ELOS Hair Removal - Face', 'ru' => 'ЭЛОС эпиляция - лицо', 'hy' => 'ԷԼՈՍ էպիլյացիա - դեմք'], 'description' => 'ELOS laser hair removal for face', 'description_i18n' => ['en' => 'ELOS laser hair removal for face', 'ru' => 'ЭЛОС эпиляция для лица', 'hy' => 'ԷԼՈՍ էպիլյացիա դեմքի համար'], 'duration_minutes' => 30, 'price_from' => 2000, 'price_to' => 4000, 'sort' => 30],
            ['slug' => 'elos-arms-full', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Arms Full', 'name_i18n' => ['en' => 'ELOS Hair Removal - Arms Full', 'ru' => 'ЭЛОС эпиляция - руки полностью', 'hy' => 'ԷԼՈՍ էպիլյացիա - թև ամբողջությամբ'], 'description' => 'ELOS laser hair removal for full arms', 'description_i18n' => ['en' => 'ELOS laser hair removal for full arms', 'ru' => 'ЭЛОС эпиляция для рук полностью', 'hy' => 'ԷԼՈՍ էպիլյացիա ամբողջական թևերի համար'], 'duration_minutes' => 45, 'price_from' => 3000, 'price_to' => 5000, 'sort' => 40],
            ['slug' => 'elos-underarms', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Underarms', 'name_i18n' => ['en' => 'ELOS Hair Removal - Underarms', 'ru' => 'ЭЛОС эпиляция - подмышки', 'hy' => 'ԷԼՈՍ էպիլյացիա - թևատակ'], 'description' => 'ELOS laser hair removal for underarms', 'description_i18n' => ['en' => 'ELOS laser hair removal for underarms', 'ru' => 'ЭЛОС эпиляция для подмышек', 'hy' => 'ԷԼՈՍ էպիլյացիա թևատակերի համար'], 'duration_minutes' => 30, 'price_from' => 2000, 'price_to' => 3000, 'sort' => 50],
            ['slug' => 'elos-legs-full', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Legs Full', 'name_i18n' => ['en' => 'ELOS Hair Removal - Legs Full', 'ru' => 'ЭЛОС эпиляция - ноги полностью', 'hy' => 'ԷԼՈՍ էպիլյացիա - ոտք ամբողջությամբ'], 'description' => 'ELOS laser hair removal for full legs', 'description_i18n' => ['en' => 'ELOS laser hair removal for full legs', 'ru' => 'ЭЛОС эпиляция для ног полностью', 'hy' => 'ԷԼՈՍ էպիլյացիա ամբողջական ոտքերի համար'], 'duration_minutes' => 60, 'price_from' => 5000, 'price_to' => 12000, 'sort' => 60],
            ['slug' => 'elos-contour', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Contour', 'name_i18n' => ['en' => 'ELOS Hair Removal - Contour', 'ru' => 'ЭЛОС эпиляция - контур', 'hy' => 'ԷԼՈՍ էպիլյացիա - կանտովկա'], 'description' => 'ELOS contour zone hair removal', 'description_i18n' => ['en' => 'ELOS contour zone hair removal', 'ru' => 'ЭЛОС эпиляция зоны контура', 'hy' => 'ԷԼՈՍ էպիլյացիա կոնտուր գոտու համար'], 'duration_minutes' => 25, 'price_from' => 2000, 'price_to' => 3000, 'sort' => 70],
            ['slug' => 'elos-classic-bikini', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Classic Bikini', 'name_i18n' => ['en' => 'ELOS Hair Removal - Classic Bikini', 'ru' => 'ЭЛОС эпиляция - классическое бикини', 'hy' => 'ԷԼՈՍ էպիլյացիա - բիկինի'], 'description' => 'ELOS hair removal for classic bikini zone', 'description_i18n' => ['en' => 'ELOS hair removal for classic bikini zone', 'ru' => 'ЭЛОС эпиляция зоны классического бикини', 'hy' => 'ԷԼՈՍ էպիլյացիա բիկինի գոտու համար'], 'duration_minutes' => 35, 'price_from' => 3000, 'price_to' => 5000, 'sort' => 80],
            ['slug' => 'elos-deep-bikini', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Deep Bikini', 'name_i18n' => ['en' => 'ELOS Hair Removal - Deep Bikini', 'ru' => 'ЭЛОС эпиляция - глубокое бикини', 'hy' => 'ԷԼՈՍ էպիլյացիա - խորը բիկինի'], 'description' => 'ELOS hair removal for deep bikini zone', 'description_i18n' => ['en' => 'ELOS hair removal for deep bikini zone', 'ru' => 'ЭЛОС эпиляция зоны глубокого бикини', 'hy' => 'ԷԼՈՍ էպիլյացիա խորը բիկինիի համար'], 'duration_minutes' => 45, 'price_from' => 5000, 'price_to' => 6000, 'sort' => 90],
            ['slug' => 'elos-back', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Back', 'name_i18n' => ['en' => 'ELOS Hair Removal - Back', 'ru' => 'ЭЛОС эпиляция - спина', 'hy' => 'ԷԼՈՍ էպիլյացիա - մեջք'], 'description' => 'ELOS hair removal for back', 'description_i18n' => ['en' => 'ELOS hair removal for back', 'ru' => 'ЭЛОС эпиляция зоны спины', 'hy' => 'ԷԼՈՍ էպիլյացիա մեջքի համար'], 'duration_minutes' => 45, 'price_from' => 3000, 'price_to' => 7000, 'sort' => 100],
            ['slug' => 'elos-full-body', 'category_slug' => 'epilation', 'name' => 'ELOS Hair Removal - Full Body', 'name_i18n' => ['en' => 'ELOS Hair Removal - Full Body', 'ru' => 'ЭЛОС эпиляция - все тело', 'hy' => 'ԷԼՈՍ էպիլյացիա - ամբողջ մարմին'], 'description' => 'ELOS full body hair removal', 'description_i18n' => ['en' => 'ELOS full body hair removal', 'ru' => 'ЭЛОС эпиляция всего тела', 'hy' => 'ԷԼՈՍ ամբողջ մարմնի էպիլյացիա'], 'duration_minutes' => 120, 'price_from' => 15000, 'price_to' => 18000, 'sort' => 110],

            ['slug' => 'wax-roller-legs-full', 'category_slug' => 'wax-epilation', 'name' => 'Waxing with Golden Roller - Legs Full', 'name_i18n' => ['en' => 'Waxing with Golden Roller - Legs Full', 'ru' => 'Восковая депиляция роликом - ноги полностью', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա - ոտք ամբողջությամբ'], 'description' => 'Golden roller waxing for full legs', 'description_i18n' => ['en' => 'Golden roller waxing for full legs', 'ru' => 'Депиляция золотым роликом для ног полностью', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա ամբողջական ոտքերի համար'], 'duration_minutes' => 60, 'price_from' => 5000, 'price_to' => 10000, 'sort' => 10],
            ['slug' => 'wax-roller-arms-full', 'category_slug' => 'wax-epilation', 'name' => 'Waxing with Golden Roller - Arms Full', 'name_i18n' => ['en' => 'Waxing with Golden Roller - Arms Full', 'ru' => 'Восковая депиляция роликом - руки полностью', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա - թև ամբողջությամբ'], 'description' => 'Golden roller waxing for full arms', 'description_i18n' => ['en' => 'Golden roller waxing for full arms', 'ru' => 'Депиляция золотым роликом для рук полностью', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա ամբողջական թևերի համար'], 'duration_minutes' => 45, 'price_from' => 3000, 'price_to' => 5000, 'sort' => 20],
            ['slug' => 'wax-roller-underarms', 'category_slug' => 'wax-epilation', 'name' => 'Waxing with Golden Roller - Underarms', 'name_i18n' => ['en' => 'Waxing with Golden Roller - Underarms', 'ru' => 'Восковая депиляция роликом - подмышки', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա - թևատակ'], 'description' => 'Golden roller waxing for underarms', 'description_i18n' => ['en' => 'Golden roller waxing for underarms', 'ru' => 'Депиляция золотым роликом для подмышек', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա թևատակերի համար'], 'duration_minutes' => 20, 'price_from' => 2000, 'price_to' => 2000, 'sort' => 30],
            ['slug' => 'wax-roller-face', 'category_slug' => 'wax-epilation', 'name' => 'Waxing with Golden Roller - Face', 'name_i18n' => ['en' => 'Waxing with Golden Roller - Face', 'ru' => 'Восковая депиляция роликом - лицо', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա - դեմք'], 'description' => 'Golden roller waxing for face', 'description_i18n' => ['en' => 'Golden roller waxing for face', 'ru' => 'Депиляция золотым роликом для лица', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա դեմքի համար'], 'duration_minutes' => 20, 'price_from' => 1000, 'price_to' => 3000, 'sort' => 40],
            ['slug' => 'wax-roller-contour', 'category_slug' => 'wax-epilation', 'name' => 'Waxing with Golden Roller - Contour', 'name_i18n' => ['en' => 'Waxing with Golden Roller - Contour', 'ru' => 'Восковая депиляция роликом - контур', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա - կանտովկա'], 'description' => 'Golden roller waxing for contour zones', 'description_i18n' => ['en' => 'Golden roller waxing for contour zones', 'ru' => 'Депиляция золотым роликом контурных зон', 'hy' => 'Ոսկյա ռոլիկով էպիլյացիա կոնտուր գոտիների համար'], 'duration_minutes' => 20, 'price_from' => 2000, 'price_to' => 2000, 'sort' => 50],
            ['slug' => 'hot-wax-face', 'category_slug' => 'wax-epilation', 'name' => 'Hot Golden Wax - Face', 'name_i18n' => ['en' => 'Hot Golden Wax - Face', 'ru' => 'Горячий золотой воск - лицо', 'hy' => 'Տաքացվող ոսկով էպիլյացիա - դեմք'], 'description' => 'Hot golden wax for face', 'description_i18n' => ['en' => 'Hot golden wax for face', 'ru' => 'Депиляция горячим золотым воском для лица', 'hy' => 'Տաք ոսկե մոմով էպիլյացիա դեմքի համար'], 'duration_minutes' => 25, 'price_from' => 2000, 'price_to' => 5000, 'sort' => 60],
            ['slug' => 'hot-wax-legs-full', 'category_slug' => 'wax-epilation', 'name' => 'Hot Golden Wax - Legs Full', 'name_i18n' => ['en' => 'Hot Golden Wax - Legs Full', 'ru' => 'Горячий золотой воск - ноги полностью', 'hy' => 'Տաքացվող ոսկով էպիլյացիա - ոտք ամբողջությամբ'], 'description' => 'Hot golden wax for full legs', 'description_i18n' => ['en' => 'Hot golden wax for full legs', 'ru' => 'Депиляция горячим золотым воском для ног полностью', 'hy' => 'Տաք ոսկե մոմով էպիլյացիա ամբողջական ոտքերի համար'], 'duration_minutes' => 60, 'price_from' => 7000, 'price_to' => 12000, 'sort' => 70],
            ['slug' => 'hot-wax-arms-full', 'category_slug' => 'wax-epilation', 'name' => 'Hot Golden Wax - Arms Full', 'name_i18n' => ['en' => 'Hot Golden Wax - Arms Full', 'ru' => 'Горячий золотой воск - руки полностью', 'hy' => 'Տաքացվող ոսկով էպիլյացիա - թև ամբողջությամբ'], 'description' => 'Hot golden wax for full arms', 'description_i18n' => ['en' => 'Hot golden wax for full arms', 'ru' => 'Депиляция горячим золотым воском для рук полностью', 'hy' => 'Տաք ոսկե մոմով էպիլյացիա ամբողջական թևերի համար'], 'duration_minutes' => 45, 'price_from' => 4000, 'price_to' => 7000, 'sort' => 80],
            ['slug' => 'hot-wax-half-arm', 'category_slug' => 'wax-epilation', 'name' => 'Hot Golden Wax - Half Arm', 'name_i18n' => ['en' => 'Hot Golden Wax - Half Arm', 'ru' => 'Горячий золотой воск - половина руки', 'hy' => 'Տաքացվող ոսկով էպիլյացիա - կիսաթև'], 'description' => 'Hot golden wax for half arm', 'description_i18n' => ['en' => 'Hot golden wax for half arm', 'ru' => 'Депиляция горячим золотым воском для половины руки', 'hy' => 'Տաք ոսկե մոմով էպիլյացիա կիսաթևի համար'], 'duration_minutes' => 30, 'price_from' => 2000, 'price_to' => 4000, 'sort' => 90],
            ['slug' => 'hot-wax-contour', 'category_slug' => 'wax-epilation', 'name' => 'Hot Golden Wax - Contour', 'name_i18n' => ['en' => 'Hot Golden Wax - Contour', 'ru' => 'Горячий золотой воск - контур', 'hy' => 'Տաքացվող ոսկով էպիլյացիա - կանտովկա'], 'description' => 'Hot golden wax for contour zone', 'description_i18n' => ['en' => 'Hot golden wax for contour zone', 'ru' => 'Депиляция горячим золотым воском контура', 'hy' => 'Տաք ոսկե մոմով էպիլյացիա կոնտուր գոտու համար'], 'duration_minutes' => 20, 'price_from' => 2000, 'price_to' => 3000, 'sort' => 100],

            ['slug' => 'facial-masks', 'category_slug' => 'cosmetology', 'name' => 'Facial Masks', 'name_i18n' => ['en' => 'Facial Masks', 'ru' => 'Маски для лица', 'hy' => 'Դեմքի դիմակներ'], 'description' => 'Facial mask treatments', 'description_i18n' => ['en' => 'Facial mask treatments', 'ru' => 'Уходовые маски для лица', 'hy' => 'Դիմակային խնամք դեմքի համար'], 'duration_minutes' => 45, 'price_from' => 5000, 'price_to' => 7000, 'sort' => 30],
            ['slug' => 'deep-facial-cleansing-oily-skin', 'category_slug' => 'cosmetology', 'name' => 'Deep Facial Cleansing (Oily Skin)', 'name_i18n' => ['en' => 'Deep Facial Cleansing (Oily Skin)', 'ru' => 'Глубокая чистка лица от жира', 'hy' => 'Դեմքի խորը մաքրում ճարպից'], 'description' => 'Deep cleansing treatment for oily skin', 'description_i18n' => ['en' => 'Deep cleansing treatment for oily skin', 'ru' => 'Глубокая чистка для жирной кожи', 'hy' => 'Խորը մաքրում յուղոտ մաշկի համար'], 'duration_minutes' => 60, 'price_from' => 5000, 'price_to' => 7000, 'sort' => 40],
            ['slug' => 'ultrasonic-facial-cleansing', 'category_slug' => 'cosmetology', 'name' => 'Ultrasonic Facial Cleansing', 'name_i18n' => ['en' => 'Ultrasonic Facial Cleansing', 'ru' => 'Ультразвуковая чистка лица', 'hy' => 'Դեմքի ուլտրաձայնային մաքրում'], 'description' => 'Ultrasonic deep skin cleansing', 'description_i18n' => ['en' => 'Ultrasonic deep skin cleansing', 'ru' => 'Ультразвуковая чистка лица', 'hy' => 'Դեմքի ուլտրաձայնային մաքրում'], 'duration_minutes' => 50, 'price_from' => 5000, 'price_to' => 5000, 'sort' => 50],
            ['slug' => 'ultrasonic-cleansing-mask', 'category_slug' => 'cosmetology', 'name' => 'Ultrasonic Cleansing and Mask', 'name_i18n' => ['en' => 'Ultrasonic Cleansing and Mask', 'ru' => 'Ультразвуковая чистка и маска', 'hy' => 'Դեմքի ուլտրաձայնային մաքրում և դիմակ'], 'description' => 'Ultrasonic cleansing with finishing mask', 'description_i18n' => ['en' => 'Ultrasonic cleansing with finishing mask', 'ru' => 'Ультразвуковая чистка с маской', 'hy' => 'Ուլտրաձայնային մաքրում և դիմակ'], 'duration_minutes' => 60, 'price_from' => 10000, 'price_to' => 10000, 'sort' => 60],
            ['slug' => 'facial-massage', 'category_slug' => 'cosmetology', 'name' => 'Facial Massage', 'name_i18n' => ['en' => 'Facial Massage', 'ru' => 'Массаж лица', 'hy' => 'Դեմքի մերսում'], 'description' => 'Facial massage therapy', 'description_i18n' => ['en' => 'Facial massage therapy', 'ru' => 'Массаж лица', 'hy' => 'Դեմքի մերսում'], 'duration_minutes' => 40, 'price_from' => 5000, 'price_to' => 5000, 'sort' => 70],
            ['slug' => 'facial-decollete-massage', 'category_slug' => 'cosmetology', 'name' => 'Facial and Décolleté Massage', 'name_i18n' => ['en' => 'Facial and Décolleté Massage', 'ru' => 'Массаж лица и зоны декольте', 'hy' => 'Դեմքի մերսում և լանջ'], 'description' => 'Massage for face and décolleté zone', 'description_i18n' => ['en' => 'Massage for face and décolleté zone', 'ru' => 'Массаж лица и зоны декольте', 'hy' => 'Դեմքի և դեկոլտեի մերսում'], 'duration_minutes' => 50, 'price_from' => 7000, 'price_to' => 7000, 'sort' => 80],

            ['slug' => 'massage-paraffin-firming-mask', 'category_slug' => 'massage-care', 'name' => 'Massage with Paraffin and Firming Mask', 'name_i18n' => ['en' => 'Massage with Paraffin and Firming Mask', 'ru' => 'Массаж с парафином и фиксирующей маской', 'hy' => 'Մերսում պարաֆինով և ֆիքսող դիմակով'], 'description' => 'Massage treatment with paraffin and mask', 'description_i18n' => ['en' => 'Massage treatment with paraffin and mask', 'ru' => 'Массаж с парафином и фиксирующей маской', 'hy' => 'Մերսում պարաֆինով և ֆիքսող դիմակով'], 'duration_minutes' => 60, 'price_from' => 5000, 'price_to' => 8000, 'sort' => 10],
            ['slug' => 'korean-massage', 'category_slug' => 'massage-care', 'name' => 'Korean Massage', 'name_i18n' => ['en' => 'Korean Massage', 'ru' => 'Корейский массаж', 'hy' => 'Կորեական մերսում'], 'description' => 'Korean massage technique', 'description_i18n' => ['en' => 'Korean massage technique', 'ru' => 'Корейская техника массажа', 'hy' => 'Կորեական մերսման տեխնիկա'], 'duration_minutes' => 45, 'price_from' => 4000, 'price_to' => 6000, 'sort' => 20],
            ['slug' => 'korean-facial-care', 'category_slug' => 'massage-care', 'name' => 'Korean Facial Care', 'name_i18n' => ['en' => 'Korean Facial Care', 'ru' => 'Корейский уход за лицом', 'hy' => 'Կորեական խնամք'], 'description' => 'Korean facial care protocol', 'description_i18n' => ['en' => 'Korean facial care protocol', 'ru' => 'Корейский уход за лицом', 'hy' => 'Կորեական խնամք դեմքի համար'], 'duration_minutes' => 60, 'price_from' => 8000, 'price_to' => 10000, 'sort' => 30],
            ['slug' => 'hand-care-with-massage', 'category_slug' => 'massage-care', 'name' => 'Hand Care with Massage', 'name_i18n' => ['en' => 'Hand Care with Massage', 'ru' => 'Уход за руками с массажем', 'hy' => 'Ձեռքերի խնամք մերսում'], 'description' => 'Hand care treatment with massage', 'description_i18n' => ['en' => 'Hand care treatment with massage', 'ru' => 'Уход за руками с массажем', 'hy' => 'Ձեռքերի խնամք մերսմամբ'], 'duration_minutes' => 40, 'price_from' => 4000, 'price_to' => 6000, 'sort' => 40],
            ['slug' => 'facial-decollete-hand-massage', 'category_slug' => 'massage-care', 'name' => 'Facial, Décolleté and Hand Massage', 'name_i18n' => ['en' => 'Facial, Décolleté and Hand Massage', 'ru' => 'Массаж лица, зоны декольте и рук', 'hy' => 'Մերսում լանջով և ձեռքերով'], 'description' => 'Face, décolleté and hand massage', 'description_i18n' => ['en' => 'Face, décolleté and hand massage', 'ru' => 'Массаж лица, декольте и рук', 'hy' => 'Դեմքի, դեկոլտեի և ձեռքերի մերսում'], 'duration_minutes' => 50, 'price_from' => 5000, 'price_to' => 7000, 'sort' => 50],
            ['slug' => 'foot-massage-care', 'category_slug' => 'massage-care', 'name' => 'Foot Massage', 'name_i18n' => ['en' => 'Foot Massage', 'ru' => 'Массаж стоп', 'hy' => 'Ոտնաթաթերի մերսում'], 'description' => 'Foot massage care', 'description_i18n' => ['en' => 'Foot massage care', 'ru' => 'Массаж стоп', 'hy' => 'Ոտնաթաթերի մերսում'], 'duration_minutes' => 45, 'price_from' => 5000, 'price_to' => 7000, 'sort' => 60],

            ['slug' => 'papilloma-removal', 'category_slug' => 'specialized', 'name' => 'Papilloma Removal', 'name_i18n' => ['en' => 'Papilloma Removal', 'ru' => 'Удаление папиллом', 'hy' => 'Պապոլիմայի հեռացում'], 'description' => 'Removal of papillomas', 'description_i18n' => ['en' => 'Removal of papillomas', 'ru' => 'Удаление папиллом', 'hy' => 'Պապիլոմաների հեռացում'], 'duration_minutes' => 30, 'price_from' => 1000, 'price_to' => 5000, 'sort' => 10],
            ['slug' => 'electrolysis-needle-epilation', 'category_slug' => 'specialized', 'name' => 'Electrolysis (Needle Epilation)', 'name_i18n' => ['en' => 'Electrolysis (Needle Epilation)', 'ru' => 'Игольчатая эпиляция', 'hy' => 'Ասեղային էպիլացիա'], 'description' => 'Needle electrolysis epilation', 'description_i18n' => ['en' => 'Needle electrolysis epilation', 'ru' => 'Игольчатая электроэпиляция', 'hy' => 'Ասեղային էլեկտրոէպիլյացիա'], 'duration_minutes' => 45, 'price_from' => 6000, 'price_to' => 8000, 'sort' => 20],
            ['slug' => 'ear-piercing', 'category_slug' => 'specialized', 'name' => 'Ear Piercing', 'name_i18n' => ['en' => 'Ear Piercing', 'ru' => 'Прокол ушей', 'hy' => 'Ականջի դակում'], 'description' => 'Professional ear piercing', 'description_i18n' => ['en' => 'Professional ear piercing', 'ru' => 'Профессиональный прокол ушей', 'hy' => 'Ականջի պրոֆեսիոնալ ծակում'], 'duration_minutes' => 30, 'price_from' => 4000, 'price_to' => 5000, 'sort' => 30],
            ['slug' => 'threading-hair-removal', 'category_slug' => 'specialized', 'name' => 'Threading', 'name_i18n' => ['en' => 'Threading', 'ru' => 'Удаление волосков нитью', 'hy' => 'Թելով մաքրում'], 'description' => 'Thread hair removal treatment', 'description_i18n' => ['en' => 'Thread hair removal treatment', 'ru' => 'Удаление волос нитью', 'hy' => 'Մազիկների հեռացում թելով'], 'duration_minutes' => 25, 'price_from' => 2000, 'price_to' => 3000, 'sort' => 40],

            ['slug' => 'natural-makeup-without-lashes', 'category_slug' => 'makeup', 'name' => 'Natural Makeup without Lashes', 'name_i18n' => ['en' => 'Natural Makeup without Lashes', 'ru' => 'Натуральный макияж без ресниц', 'hy' => 'Նատուրալ դիմահարդարում առանց թարթիչների'], 'description' => 'Natural style makeup without lashes', 'description_i18n' => ['en' => 'Natural style makeup without lashes', 'ru' => 'Натуральный макияж без ресниц', 'hy' => 'Բնական դիմահարդարում առանց թարթիչների'], 'duration_minutes' => 70, 'price_from' => 8000, 'price_to' => 8000, 'sort' => 50],
            ['slug' => 'natural-makeup-with-lashes', 'category_slug' => 'makeup', 'name' => 'Natural Makeup with Lashes', 'name_i18n' => ['en' => 'Natural Makeup with Lashes', 'ru' => 'Натуральный макияж с ресницами', 'hy' => 'Նատուրալ դիմահարդարում թարթիչներով'], 'description' => 'Natural makeup with lashes', 'description_i18n' => ['en' => 'Natural makeup with lashes', 'ru' => 'Натуральный макияж с ресницами', 'hy' => 'Բնական դիմահարդարում թարթիչներով'], 'duration_minutes' => 75, 'price_from' => 10000, 'price_to' => 10000, 'sort' => 60],
            ['slug' => 'decollete-makeup-enhancement', 'category_slug' => 'makeup', 'name' => 'Décolleté Enhancement Makeup', 'name_i18n' => ['en' => 'Décolleté Enhancement Makeup', 'ru' => 'Макияж зоны декольте', 'hy' => 'Դեկոլտեի մշակում'], 'description' => 'Makeup and enhancement for décolleté area', 'description_i18n' => ['en' => 'Makeup and enhancement for décolleté area', 'ru' => 'Макияж и обработка зоны декольте', 'hy' => 'Դեկոլտե գոտու դիմահարդարում և մշակում'], 'duration_minutes' => 30, 'price_from' => 3000, 'price_to' => 5000, 'sort' => 70],
            ['slug' => 'individual-lash-extensions', 'category_slug' => 'brows-lashes', 'name' => 'Individual Lash Extensions', 'name_i18n' => ['en' => 'Individual Lash Extensions', 'ru' => 'Ресницы пучками', 'hy' => 'Թարթիչ հատիկով'], 'description' => 'Cluster/individual lash extensions', 'description_i18n' => ['en' => 'Cluster/individual lash extensions', 'ru' => 'Наращивание ресниц пучками', 'hy' => 'Թարթիչների հատիկային երկարեցում'], 'duration_minutes' => 45, 'price_from' => 3000, 'price_to' => 4000, 'sort' => 70],
            ['slug' => 'full-lash-extensions', 'category_slug' => 'brows-lashes', 'name' => 'Full Lash Extensions', 'name_i18n' => ['en' => 'Full Lash Extensions', 'ru' => 'Полное наращивание ресниц', 'hy' => 'Թարթիչ ընդհանուր'], 'description' => 'Full set lash extensions', 'description_i18n' => ['en' => 'Full set lash extensions', 'ru' => 'Полное наращивание ресниц', 'hy' => 'Թարթիչների ամբողջական երկարեցում'], 'duration_minutes' => 75, 'price_from' => 2000, 'price_to' => 2000, 'sort' => 80],
            ['slug' => 'brow-styling', 'category_slug' => 'brows-lashes', 'name' => 'Brow Styling', 'name_i18n' => ['en' => 'Brow Styling', 'ru' => 'Оформление бровей', 'hy' => 'Հոնքերի հարդարում'], 'description' => 'Brow styling and shaping', 'description_i18n' => ['en' => 'Brow styling and shaping', 'ru' => 'Оформление и укладка бровей', 'hy' => 'Հոնքերի ձևավորում և հարդարում'], 'duration_minutes' => 30, 'price_from' => 3000, 'price_to' => 3000, 'sort' => 90],

            ['slug' => 'kids-massage-up-to-12', 'category_slug' => 'therapeutic-massage', 'name' => 'Kids Massage up to 12', 'name_i18n' => ['en' => 'Kids Massage up to 12', 'ru' => 'Детский массаж до 12 лет', 'hy' => 'Մանկական մերսում մինչև 12 տարեկան'], 'description' => 'Children massage up to 12 years old', 'description_i18n' => ['en' => 'Children massage up to 12 years old', 'ru' => 'Детский массаж до 12 лет', 'hy' => 'Մանկական մերսում մինչև 12 տարեկան'], 'duration_minutes' => 45, 'price_from' => 8000, 'price_to' => 8000, 'sort' => 10],
            ['slug' => 'teen-massage-12-17', 'category_slug' => 'therapeutic-massage', 'name' => 'Teen Massage 12-17', 'name_i18n' => ['en' => 'Teen Massage 12-17', 'ru' => 'Подростковый массаж 12-17 лет', 'hy' => 'Մանկական մերսում 12-ից մինչև 17 տարեկան'], 'description' => 'Teen massage for age 12-17', 'description_i18n' => ['en' => 'Teen massage for age 12-17', 'ru' => 'Подростковый массаж 12-17 лет', 'hy' => 'Պատանեկան մերսում 12-17 տարեկանների համար'], 'duration_minutes' => 45, 'price_from' => 9000, 'price_to' => 9000, 'sort' => 20],
            ['slug' => 'full-therapeutic-massage-90', 'category_slug' => 'therapeutic-massage', 'name' => 'Full Therapeutic Massage (90 min)', 'name_i18n' => ['en' => 'Full Therapeutic Massage (90 min)', 'ru' => 'Полный лечебный массаж', 'hy' => 'Ամբողջական բուժական մերսում'], 'description' => 'Full therapeutic massage session', 'description_i18n' => ['en' => 'Full therapeutic massage session', 'ru' => 'Полный лечебный массаж', 'hy' => 'Ամբողջական բուժական մերսում'], 'duration_minutes' => 90, 'price_from' => 15000, 'price_to' => 15000, 'sort' => 30],
            ['slug' => 'back-massage-therapy', 'category_slug' => 'therapeutic-massage', 'name' => 'Back Massage', 'name_i18n' => ['en' => 'Back Massage', 'ru' => 'Массаж спины', 'hy' => 'Մեջքի մերսում'], 'description' => 'Therapeutic back massage', 'description_i18n' => ['en' => 'Therapeutic back massage', 'ru' => 'Лечебный массаж спины', 'hy' => 'Բուժական մեջքի մերսում'], 'duration_minutes' => 45, 'price_from' => 8000, 'price_to' => 8000, 'sort' => 40],
            ['slug' => 'leg-massage-therapy', 'category_slug' => 'therapeutic-massage', 'name' => 'Leg Massage', 'name_i18n' => ['en' => 'Leg Massage', 'ru' => 'Массаж ног', 'hy' => 'Ոտքերի մերսում'], 'description' => 'Therapeutic leg massage', 'description_i18n' => ['en' => 'Therapeutic leg massage', 'ru' => 'Лечебный массаж ног', 'hy' => 'Բուժական ոտքերի մերսում'], 'duration_minutes' => 45, 'price_from' => 8000, 'price_to' => 8000, 'sort' => 50],
            ['slug' => 'lymphatic-drainage-massage-90', 'category_slug' => 'therapeutic-massage', 'name' => 'Lymphatic Drainage Massage (90 min)', 'name_i18n' => ['en' => 'Lymphatic Drainage Massage (90 min)', 'ru' => 'Лимфодренажный массаж', 'hy' => 'Լիմֆոդրենաժ մերսում'], 'description' => 'Lymphatic drainage full-body session', 'description_i18n' => ['en' => 'Lymphatic drainage full-body session', 'ru' => 'Лимфодренажный массаж', 'hy' => 'Լիմֆոդրենաժային մերսում'], 'duration_minutes' => 90, 'price_from' => 15000, 'price_to' => 15000, 'sort' => 60],
            ['slug' => 'anti-cellulite-massage-120', 'category_slug' => 'therapeutic-massage', 'name' => 'Anti-cellulite Massage (120 min)', 'name_i18n' => ['en' => 'Anti-cellulite Massage (120 min)', 'ru' => 'Антицеллюлитный массаж', 'hy' => 'Հակացելյուլիտային մերսում'], 'description' => 'Long anti-cellulite massage session', 'description_i18n' => ['en' => 'Long anti-cellulite massage session', 'ru' => 'Антицеллюлитный массаж 120 минут', 'hy' => 'Հակացելյուլիտային մերսում 120 րոպե'], 'duration_minutes' => 120, 'price_from' => 20000, 'price_to' => 20000, 'sort' => 70],
            ['slug' => 'arm-massage-20', 'category_slug' => 'therapeutic-massage', 'name' => 'Arm Massage (20 min)', 'name_i18n' => ['en' => 'Arm Massage (20 min)', 'ru' => 'Массаж рук 20 мин', 'hy' => 'Ձեռքերի մերսում 20 րոպե'], 'description' => 'Short arm massage session', 'description_i18n' => ['en' => 'Short arm massage session', 'ru' => 'Короткий массаж рук', 'hy' => 'Կարճ ձեռքերի մերսում'], 'duration_minutes' => 20, 'price_from' => 5000, 'price_to' => 5000, 'sort' => 80],
            ['slug' => 'anti-cellulite-legs-stomach-sides-60', 'category_slug' => 'therapeutic-massage', 'name' => 'Anti-cellulite: Legs, Stomach, Sides (60 min)', 'name_i18n' => ['en' => 'Anti-cellulite: Legs, Stomach, Sides (60 min)', 'ru' => 'Антицеллюлитный массаж: ноги, живот, бока', 'hy' => 'Հակացելյուլիտային մերսում՝ ոտքեր, փոր, կողքեր'], 'description' => 'Targeted anti-cellulite massage', 'description_i18n' => ['en' => 'Targeted anti-cellulite massage', 'ru' => 'Антицеллюлитный массаж для ног, живота и боков', 'hy' => 'Թիրախային հակացելյուլիտային մերսում'], 'duration_minutes' => 60, 'price_from' => 10000, 'price_to' => 10000, 'sort' => 90],
            ['slug' => 'post-stroke-prevention-massage-60', 'category_slug' => 'therapeutic-massage', 'name' => 'Post-stroke Prevention Massage (60 min)', 'name_i18n' => ['en' => 'Post-stroke Prevention Massage (60 min)', 'ru' => 'Антиинсультный массаж', 'hy' => 'Հակաինսուլտային մերսում'], 'description' => 'Preventive anti-stroke massage', 'description_i18n' => ['en' => 'Preventive anti-stroke massage', 'ru' => 'Профилактический антиинсультный массаж', 'hy' => 'Պրոֆիլակտիկ հակաինսուլտային մերսում'], 'duration_minutes' => 60, 'price_from' => 15000, 'price_to' => 15000, 'sort' => 100],
            ['slug' => 'thermo-sauna', 'category_slug' => 'therapeutic-massage', 'name' => 'Thermo Sauna', 'name_i18n' => ['en' => 'Thermo Sauna', 'ru' => 'Термосауна', 'hy' => 'Թերմոսաունա'], 'description' => 'Thermo sauna recovery procedure', 'description_i18n' => ['en' => 'Thermo sauna recovery procedure', 'ru' => 'Процедура термосауны', 'hy' => 'Թերմոսաունայի վերականգնողական պրոցեդուրա'], 'duration_minutes' => 30, 'price_from' => 5000, 'price_to' => 5000, 'sort' => 110],
            ['slug' => 'post-injury-recovery-massage', 'category_slug' => 'therapeutic-massage', 'name' => 'Post-injury Recovery Massage', 'name_i18n' => ['en' => 'Post-injury Recovery Massage', 'ru' => 'Восстановительный массаж после травм', 'hy' => 'Մերսում տրավմայից հետո'], 'description' => 'Recovery massage after injury', 'description_i18n' => ['en' => 'Recovery massage after injury', 'ru' => 'Восстановительный массаж после травм', 'hy' => 'Վերականգնողական մերսում վնասվածքներից հետո'], 'duration_minutes' => 60, 'price_from' => 5000, 'price_to' => 15000, 'sort' => 120],
            ['slug' => 'pregnancy-massage-60', 'category_slug' => 'therapeutic-massage', 'name' => 'Pregnancy Massage (60 min)', 'name_i18n' => ['en' => 'Pregnancy Massage (60 min)', 'ru' => 'Массаж для беременных', 'hy' => 'Մերսում հղիների համար'], 'description' => 'Prenatal massage session', 'description_i18n' => ['en' => 'Prenatal massage session', 'ru' => 'Массаж для беременных', 'hy' => 'Մերսում հղիների համար'], 'duration_minutes' => 60, 'price_from' => 10000, 'price_to' => 10000, 'sort' => 130],
        ];

        $serviceMap = [];
        foreach ($servicesData as $serviceData) {
            $category = $categoryMap[$serviceData['category_slug']];
            $service = Service::query()->updateOrCreate(
                ['slug' => $serviceData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $serviceData['name'],
                    'name_i18n' => $serviceData['name_i18n'],
                    'description' => $serviceData['description'],
                    'description_i18n' => $serviceData['description_i18n'],
                    'duration_minutes' => $serviceData['duration_minutes'],
                    'price_from' => $serviceData['price_from'],
                    'price_to' => $serviceData['price_to'],
                    'sort' => $serviceData['sort'],
                    'is_active' => true,
                ]
            );
            $serviceMap[$serviceData['slug']] = $service;
        }

        $workingHours = [
            'monday' => [['start' => '10:00', 'end' => '19:00']],
            'tuesday' => [['start' => '10:00', 'end' => '19:00']],
            'wednesday' => [['start' => '10:00', 'end' => '19:00']],
            'thursday' => [['start' => '10:00', 'end' => '19:00']],
            'friday' => [['start' => '10:00', 'end' => '19:00']],
            'saturday' => [['start' => '10:00', 'end' => '19:00']],
            'sunday' => [['start' => '10:00', 'end' => '19:00']],
        ];

        $mastersData = [
            [
                'slug' => 'anna-master',
                'user_id' => $masterUser->id,
                'name' => 'Anna Master',
                'name_i18n' => ['en' => 'Anna Master', 'ru' => 'Анна Мастер', 'hy' => 'Աննա Մաստեր'],
                'bio' => 'Senior beauty specialist in haircut, coloring and premium care rituals.',
                'bio_i18n' => ['en' => 'Senior beauty specialist in haircut, coloring and premium care rituals.', 'ru' => 'Старший бьюти-специалист по стрижкам, окрашиванию и premium-уходу.', 'hy' => 'Ավագ beauty մասնագետ՝ սանրվածքի, ներկման և premium խնամքի ուղղությամբ։'],
                'avatar' => 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?auto=format&fit=crop&w=900&q=80',
                'experience_years' => 9,
                'specialties' => ['Haircut', 'Coloring', 'Event styling', 'Nail care'],
                'specialties_i18n' => ['en' => ['Haircut', 'Coloring', 'Event styling', 'Nail care'], 'ru' => ['Стрижки', 'Окрашивание', 'Образ на событие', 'Уход за ногтями'], 'hy' => ['Սանրվածքներ', 'Ներկում', 'Կերպար միջոցառման համար', 'Եղունգների խնամք']],
                'languages' => ['Armenian', 'Russian', 'English'],
                'service_slugs' => array_keys($serviceMap),
                'sort' => 10,
            ],
            ['slug' => 'sofia-hair-pro', 'user_id' => null, 'name' => 'Sofia Hair Pro', 'name_i18n' => ['en' => 'Sofia Hair Pro', 'ru' => 'София Hair Pro', 'hy' => 'Սոֆիա Hair Pro'], 'bio' => 'Hair and styling specialist.', 'bio_i18n' => ['en' => 'Hair and styling specialist.', 'ru' => 'Специалист по волосам и укладкам.', 'hy' => 'Մազերի և հարդարման մասնագետ։'], 'avatar' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80', 'experience_years' => 7, 'specialties' => ['Haircut', 'Styling'], 'specialties_i18n' => ['en' => ['Haircut', 'Styling'], 'ru' => ['Стрижки', 'Укладки'], 'hy' => ['Սանրվածքներ', 'Հարդարում']], 'languages' => ['Russian', 'English'], 'service_slugs' => ['women-haircut', 'mens-haircut', 'blowout-styling', 'hair-coloring'], 'sort' => 20],
            ['slug' => 'mariam-color', 'user_id' => null, 'name' => 'Mariam Color', 'name_i18n' => ['en' => 'Mariam Color', 'ru' => 'Мариам Колорист', 'hy' => 'Մարիամ Color'], 'bio' => 'Color and treatment expert.', 'bio_i18n' => ['en' => 'Color and treatment expert.', 'ru' => 'Эксперт по окрашиванию и восстановлению.', 'hy' => 'Ներկման և վերականգնման փորձագետ։'], 'avatar' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80', 'experience_years' => 8, 'specialties' => ['Coloring', 'Care'], 'specialties_i18n' => ['en' => ['Coloring', 'Care'], 'ru' => ['Окрашивание', 'Уход'], 'hy' => ['Ներկում', 'Խնամք']], 'languages' => ['Armenian', 'English'], 'service_slugs' => ['hair-coloring', 'face-cleaning', 'anti-age-treatment'], 'sort' => 30],
            ['slug' => 'lilit-nails', 'user_id' => null, 'name' => 'Lilit Nails', 'name_i18n' => ['en' => 'Lilit Nails', 'ru' => 'Лилит Ногти', 'hy' => 'Լիլիթ Nails'], 'bio' => 'Nail artist and pedicure specialist.', 'bio_i18n' => ['en' => 'Nail artist and pedicure specialist.', 'ru' => 'Нейл-артист и специалист по педикюру.', 'hy' => 'Եղունգների դիզայներ և պեդիկյուրի մասնագետ։'], 'avatar' => 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=900&q=80', 'experience_years' => 6, 'specialties' => ['Manicure', 'Pedicure'], 'specialties_i18n' => ['en' => ['Manicure', 'Pedicure'], 'ru' => ['Маникюр', 'Педикюр'], 'hy' => ['Մատնահարդարում', 'Պեդիկյուր']], 'languages' => ['Armenian', 'Russian'], 'service_slugs' => ['manicure-classic', 'gel-manicure', 'nail-design', 'pedicure-spa'], 'sort' => 40],
            ['slug' => 'nare-brows', 'user_id' => null, 'name' => 'Nare Brows', 'name_i18n' => ['en' => 'Nare Brows', 'ru' => 'Наре Бровист', 'hy' => 'Նարե Brows'], 'bio' => 'Brows and lashes specialist.', 'bio_i18n' => ['en' => 'Brows and lashes specialist.', 'ru' => 'Специалист по бровям и ресницам.', 'hy' => 'Հոնքերի և թարթիչների մասնագետ։'], 'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=900&q=80', 'experience_years' => 5, 'specialties' => ['Brows', 'Lashes'], 'specialties_i18n' => ['en' => ['Brows', 'Lashes'], 'ru' => ['Брови', 'Ресницы'], 'hy' => ['Հոնքեր', 'Թարթիչներ']], 'languages' => ['Russian', 'English'], 'service_slugs' => ['brow-shaping', 'brow-lamination', 'lash-lift', 'day-makeup'], 'sort' => 50],
            ['slug' => 'eva-makeup', 'user_id' => null, 'name' => 'Eva Makeup', 'name_i18n' => ['en' => 'Eva Makeup', 'ru' => 'Ева Визажист', 'hy' => 'Էվա Makeup'], 'bio' => 'Makeup artist for events and bridal.', 'bio_i18n' => ['en' => 'Makeup artist for events and bridal.', 'ru' => 'Визажист для мероприятий и свадеб.', 'hy' => 'Դիմահարդար՝ միջոցառումների և հարսանիքների համար։'], 'avatar' => 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=900&q=80', 'experience_years' => 10, 'specialties' => ['Event makeup', 'Bridal looks'], 'specialties_i18n' => ['en' => ['Event makeup', 'Bridal looks'], 'ru' => ['Вечерний макияж', 'Свадебные образы'], 'hy' => ['Երեկոյան դիմահարդարում', 'Հարսանեկան կերպարներ']], 'languages' => ['English', 'Russian'], 'service_slugs' => ['day-makeup', 'event-makeup', 'bridal-makeup', 'lash-lift'], 'sort' => 60],
            ['slug' => 'david-spa', 'user_id' => null, 'name' => 'David Spa', 'name_i18n' => ['en' => 'David Spa', 'ru' => 'Давид SPA', 'hy' => 'Դավիթ SPA'], 'bio' => 'Body care and massage therapist.', 'bio_i18n' => ['en' => 'Body care and massage therapist.', 'ru' => 'Терапевт по уходу за телом и массажу.', 'hy' => 'Մարմնի խնամքի և մերսման թերապևտ։'], 'avatar' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&w=900&q=80', 'experience_years' => 11, 'specialties' => ['Massage', 'Body care'], 'specialties_i18n' => ['en' => ['Massage', 'Body care'], 'ru' => ['Массаж', 'Уход за телом'], 'hy' => ['Մերսում', 'Մարմնի խնամք']], 'languages' => ['Armenian', 'English'], 'service_slugs' => ['relax-massage', 'body-scrub', 'face-cleaning'], 'sort' => 70],
            ['slug' => 'karine-cosmo', 'user_id' => null, 'name' => 'Karine Cosmo', 'name_i18n' => ['en' => 'Karine Cosmo', 'ru' => 'Карине Косметолог', 'hy' => 'Կարինե Cosmo'], 'bio' => 'Clinical cosmetology specialist.', 'bio_i18n' => ['en' => 'Clinical cosmetology specialist.', 'ru' => 'Специалист клинической косметологии.', 'hy' => 'Կլինիկական կոսմետոլոգիայի մասնագետ։'], 'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=900&q=80', 'experience_years' => 12, 'specialties' => ['Cosmetology', 'Skin treatment'], 'specialties_i18n' => ['en' => ['Cosmetology', 'Skin treatment'], 'ru' => ['Косметология', 'Лечение кожи'], 'hy' => ['Կոսմետոլոգիա', 'Մաշկի բուժում']], 'languages' => ['Armenian', 'Russian', 'English'], 'service_slugs' => ['face-cleaning', 'anti-age-treatment', 'chemical-peel'], 'sort' => 80],
            ['slug' => 'mila-epil', 'user_id' => null, 'name' => 'Mila Epil', 'name_i18n' => ['en' => 'Mila Epil', 'ru' => 'Мила Эпиляция', 'hy' => 'Միլա Epil'], 'bio' => 'Quick and precise epilation services.', 'bio_i18n' => ['en' => 'Quick and precise epilation services.', 'ru' => 'Быстрая и аккуратная эпиляция.', 'hy' => 'Արագ և ճշգրիտ էպիլյացիա։'], 'avatar' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80', 'experience_years' => 4, 'specialties' => ['Epilation'], 'specialties_i18n' => ['en' => ['Epilation'], 'ru' => ['Эпиляция'], 'hy' => ['Էպիլյացիա']], 'languages' => ['Russian'], 'service_slugs' => ['legs-epilation', 'arms-epilation', 'body-scrub'], 'sort' => 90],
            ['slug' => 'armine-combo', 'user_id' => null, 'name' => 'Armine Combo', 'name_i18n' => ['en' => 'Armine Combo', 'ru' => 'Армине Комбо', 'hy' => 'Արմինե Combo'], 'bio' => 'Universal specialist for combo appointments.', 'bio_i18n' => ['en' => 'Universal specialist for combo appointments.', 'ru' => 'Универсальный мастер для комбинированных записей.', 'hy' => 'Համընդհանուր մասնագետ կոմբո ամրագրումների համար։'], 'avatar' => 'https://images.unsplash.com/photo-1524502397800-2eeaad7c3fe5?auto=format&fit=crop&w=900&q=80', 'experience_years' => 9, 'specialties' => ['Hair', 'Nails', 'Makeup'], 'specialties_i18n' => ['en' => ['Hair', 'Nails', 'Makeup'], 'ru' => ['Волосы', 'Ногти', 'Макияж'], 'hy' => ['Մազեր', 'Եղունգներ', 'Դիմահարդարում']], 'languages' => ['Armenian', 'Russian'], 'service_slugs' => ['women-haircut', 'gel-manicure', 'day-makeup', 'brow-shaping', 'manicure-classic'], 'sort' => 100],
            ['slug' => 'gaya-premium', 'user_id' => null, 'name' => 'Gaya Premium', 'name_i18n' => ['en' => 'Gaya Premium', 'ru' => 'Гая Премиум', 'hy' => 'Գայա Premium'], 'bio' => 'Premium full-look appointments.', 'bio_i18n' => ['en' => 'Premium full-look appointments.', 'ru' => 'Премиальные комплексные образы.', 'hy' => 'Պրեմիում համալիր կերպարներ։'], 'avatar' => 'https://images.unsplash.com/photo-1524250502761-1ac6f2e30d43?auto=format&fit=crop&w=900&q=80', 'experience_years' => 13, 'specialties' => ['Full look', 'Premium care'], 'specialties_i18n' => ['en' => ['Full look', 'Premium care'], 'ru' => ['Полный образ', 'Премиум уход'], 'hy' => ['Ամբողջական կերպար', 'Պրեմիում խնամք']], 'languages' => ['English', 'Armenian'], 'service_slugs' => ['hair-coloring', 'bridal-makeup', 'nail-design', 'anti-age-treatment', 'lash-lift'], 'sort' => 110],
        ];

        $masterMap = [];
        foreach ($mastersData as $index => $masterData) {
            $certificatesBase = [
                [
                    'title' => 'Advanced Beauty Technique',
                    'issuer' => 'Freya Academy',
                    'year' => 2020 + ($index % 5),
                    'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1200&q=80',
                ],
            ];

            $certificatesI18n = [
                'en' => $certificatesBase,
                'ru' => [[
                    'title' => 'Продвинутая beauty-техника',
                    'issuer' => 'Freya Academy',
                    'year' => 2020 + ($index % 5),
                    'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1200&q=80',
                ]],
                'hy' => [[
                    'title' => 'Beauty առաջադեմ տեխնիկա',
                    'issuer' => 'Freya Academy',
                    'year' => 2020 + ($index % 5),
                    'image' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1200&q=80',
                ]],
            ];

            $master = Master::query()->updateOrCreate(
                ['slug' => $masterData['slug']],
                [
                    'user_id' => $masterData['user_id'],
                    'name' => $masterData['name'],
                    'name_i18n' => $masterData['name_i18n'],
                    'bio' => $masterData['bio'],
                    'bio_i18n' => $masterData['bio_i18n'],
                    'avatar' => $masterData['avatar'],
                    'experience_years' => $masterData['experience_years'],
                    'specialties' => $masterData['specialties'],
                    'specialties_i18n' => $masterData['specialties_i18n'],
                    'languages' => $masterData['languages'],
                    'certificates' => $certificatesBase,
                    'certificates_i18n' => $certificatesI18n,
                    'instagram' => '@'.str_replace('-', '.', $masterData['slug']),
                    'is_active' => true,
                    'sort' => $masterData['sort'],
                    'schedule_rules' => $workingHours,
                ]
            );

            $pivot = [];
            foreach ($masterData['service_slugs'] as $serviceSlug) {
                if (!isset($serviceMap[$serviceSlug])) {
                    continue;
                }
                $service = $serviceMap[$serviceSlug];
                $pivot[$service->id] = [
                    'duration_minutes' => $service->duration_minutes,
                    'price' => (float) $service->price_from + (float) (($index % 3) * 5),
                ];
            }

            $master->services()->sync($pivot);
            $masterMap[$masterData['slug']] = $master;
        }

        $serviceCut = $serviceMap['women-haircut'];
        $serviceManicure = $serviceMap['manicure-classic'];
        $annaMaster = $masterMap['anna-master'];

        $startPending = Carbon::now()->addDays(1)->setTime(11, 0);
        $pendingAppointment = Appointment::query()->firstOrCreate(
            [
                'client_id' => $client->id,
                'master_id' => $annaMaster->id,
                'service_id' => $serviceCut->id,
                'start_at' => $startPending,
            ],
            [
                'end_at' => $startPending->copy()->addMinutes($serviceCut->duration_minutes),
                'status' => AppointmentStatus::Pending,
                'source' => 'site',
            ]
        );
        $pendingAppointment->services()->sync([
            $serviceCut->id => [
                'duration_minutes' => $serviceCut->duration_minutes,
                'price' => $serviceCut->price_from,
                'sort_order' => 0,
            ],
        ]);

        $startConfirmed = Carbon::now()->addDays(2)->setTime(12, 0);
        $confirmedAppointment = Appointment::query()->firstOrCreate(
            [
                'client_id' => $client->id,
                'master_id' => $annaMaster->id,
                'service_id' => $serviceManicure->id,
                'start_at' => $startConfirmed,
            ],
            [
                'end_at' => $startConfirmed->copy()->addMinutes($serviceManicure->duration_minutes),
                'status' => AppointmentStatus::Confirmed,
                'source' => 'instagram',
            ]
        );
        $confirmedAppointment->services()->sync([
            $serviceManicure->id => [
                'duration_minutes' => $serviceManicure->duration_minutes,
                'price' => $serviceManicure->price_from,
                'sort_order' => 0,
            ],
        ]);
    }
}
