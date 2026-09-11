<?php

return [

    'name' => 'ShiftReality',
    'tagline' => 'Your eyes are lying to you.',
    'description' => 'An immersive optical illusion and perception experience in Zimbabwe. Step inside spaces designed to challenge everything you think you know.',
    'url' => env('APP_URL', 'https://shiftreality.co.zw'),

    'contact' => [
        'email' => 'hello@shiftreality.co.zw',
        'phone' => null, // TBD
        'whatsapp' => null, // TBD
    ],

    'location' => [
        'name' => 'ShiftReality Zimbabwe',
        'address' => null, // TBD — exact address to be confirmed
        'city' => 'Harare',
        'region' => 'Harare Province',
        'country' => 'Zimbabwe',
        'country_code' => 'ZW',
        'coordinates' => [
            'lat' => null, // TBD
            'lng' => null, // TBD
        ],
    ],

    'hours' => [
        ['days' => 'Tuesday – Friday', 'time' => '10:00 – 19:00'],
        ['days' => 'Saturday – Sunday', 'time' => '09:00 – 20:00'],
        ['days' => 'Monday', 'time' => 'Closed'],
    ],

    'visit' => [
        'duration' => '1.5 – 2.5 hours',
        'min_age' => 5,
        'price_from' => null, // TBD — pricing to be confirmed
        'booking_url' => '/book',
    ],

    'shifts' => [
        [
            'number' => '01',
            'title' => 'Perspective',
            'slug' => 'perspective',
            'tagline' => 'What looks impossible from one angle makes perfect sense from another.',
            'description' => 'Step into rooms where architecture bends the rules. Walls that shouldn\'t work, together, do. Angles that defy logic become landscapes you can walk through.',
        ],
        [
            'number' => '02',
            'title' => 'Gravity',
            'slug' => 'gravity',
            'tagline' => 'Maybe gravity isn\'t behaving today.',
            'description' => 'Experience spaces where water flows upward, balls roll uphill, and your inner ear argues with your eyes. Who do you trust?',
        ],
        [
            'number' => '03',
            'title' => 'Scale',
            'slug' => 'scale',
            'tagline' => 'Big. Small. Somewhere in between.',
            'description' => 'Become a giant. Shrink to nothing. The same corridor can make you feel enormous and invisible within three steps.',
        ],
        [
            'number' => '04',
            'title' => 'Perception',
            'slug' => 'perception',
            'tagline' => 'Your brain fills in the gaps.',
            'description' => 'Colours that aren\'t the colours they are. Patterns that move when they\'re still. Your brain is doing its best. It\'s just not always right.',
        ],
    ],

    'audiences' => [
        'Friends',
        'Couples',
        'Families',
        'Tourists',
        'School Groups',
        'Corporate Teams',
        'Birthday Groups',
        'Content Creators',
        'Date Nights',
        'Curious Minds',
    ],

    'testimonials' => [
        [
            'quote' => 'I\'ve never felt so genuinely confused by a room. We stayed two hours longer than planned. Mind completely blown.',
            'name' => 'Tatenda M.',
            'detail' => 'Visited with friends',
            'rating' => 5,
        ],
        [
            'quote' => 'Best experience in Zimbabwe, full stop. My kids are still arguing about what they actually saw. Perfect for the whole family.',
            'name' => 'Sarah K.',
            'detail' => 'Family visit',
            'rating' => 5,
        ],
        [
            'quote' => 'Our team-building afternoon became a two-hour debate about reality. 10/10. We\'re already planning to come back.',
            'name' => 'Farai N.',
            'detail' => 'Corporate group',
            'rating' => 5,
        ],
    ],

    'faq' => [
        [
            'question' => 'Do I need to book in advance?',
            'answer' => 'We strongly recommend booking ahead, especially on weekends and public holidays. Walk-ins are welcome subject to availability.',
        ],
        [
            'question' => 'Is ShiftReality suitable for children?',
            'answer' => 'Absolutely. ShiftReality is designed for ages 5 and up. Some installations involve gentle physical activity — comfortable shoes are recommended.',
        ],
        [
            'question' => 'How long does the experience take?',
            'answer' => 'Most visitors spend between 1.5 and 2.5 hours. There\'s no rush — explore at your own pace.',
        ],
        [
            'question' => 'Can I take photos inside?',
            'answer' => 'Yes, and we encourage it. ShiftReality was designed to be shared. Some of our installations are practically made for your camera.',
        ],
        [
            'question' => 'Is the venue accessible?',
            'answer' => 'We\'re committed to making ShiftReality accessible to everyone. Please contact us before your visit and we\'ll ensure the best possible experience for your needs.',
        ],
    ],

    'social' => [
        'instagram' => null, // TBD
        'facebook' => null, // TBD
        'tiktok' => null, // TBD
        'x' => null, // TBD
    ],

];
